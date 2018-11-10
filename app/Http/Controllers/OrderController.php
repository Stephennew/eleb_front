<?php

namespace App\Http\Controllers;

use App\Model\Order;
use App\Model\OrderDetail;
use function foo\func;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function addOrder(Request $request)
    {
        /*1.保存order表
        shop_id-->购物车找最新的goods_id-->goods-->shop_id*/
        $goods_id = DB::table('carts')->select('goods_id')->orderBy('id','desc')->value('goods_id');
        $shop = DB::table('menus')->where('id',$goods_id)->select('shop_id','goods_name','goods_img','goods_price')->get();
        $address_list = DB::table('addresses')
            ->where('id',$request->address_id)
            ->select('provence','city','area','detail_address','tel','name')
            ->first();
        $carts = DB::table('carts')->where('user_id',Auth::id())->get();
        $price_sum = 0;
        foreach ($carts as $cart)
        {
            $goods_price = DB::table('menus')
                ->where('id',$cart->goods_id)
                ->select('goods_price')
                ->value('goods_price');
            $price_sum += $goods_price*$cart->amount;
        }
        $order = [
                'user_id'=>Auth::id(),
                'shop_id'=>$shop[0]->shop_id,
                'sn'=>date('YmdHis',time()).mt_rand(1000,9999),
                'province'=>$address_list->provence,
                'city'=>$address_list->city,
                'county'=>$address_list->area,
                'address'=>$address_list->detail_address,
                'tel'=>$address_list->tel,
                'name'=>$address_list->name,
                'out_trade_no'=>str_random(40),
                'status'=>0,
                'total'=>$price_sum,
            ];

       /* $goods_id = [];
        foreach ($carts as $ca)
        {
            if($ca->user_id == Auth::id()){
                $goods_id[] = $ca->goods_id;
            }
        }
        $order_goods_id = array_unique($goods_id);
        $ge = count($order_goods_id);
        $arr = [];
        $i = 0;
        for ($a=0;$a<$ge;++$a)
        {
            foreach ($carts as $carta)
            {
                if($order_goods_id[$a] == $carta->goods_id){
                    $arr[$order_goods_id[$a]] = ($i+=$carta->amount);
                }
            }

        }*/

        /*DB::enableQueryLog();
        $arr = DB::table('carts')->select('goods_id','amount')->where('user_id',Auth::id())->groupBy('goods_id')->count('*');
        var_dump(DB::getQueryLog());*/
        $last_order_id = '';
        $shu = DB::select("select goods_id,amount from carts WHERE user_id=?",[Auth::id()]);
        DB::transaction(function () use (&$last_order_id,$order,$shu,$shop){
            //DB::enableQueryLog();
            $ord = Order::create($order);
            //订单添加成功后，清除当前用户购物车内的所有订单
            DB::table('carts')
                ->where('user_id',Auth::id())
                ->delete();
            $order_id = $ord->id;
            $last_order_id = $order_id;
            for ($i=0;$i<count($shu);++$i)
            {
                $order_details = [
                    'order_id'=>$order_id,
                    'goods_id'=>$shu[$i]->goods_id,
                    'amount'=>$shu[$i]->amount,
                ];
                $shop = DB::table('menus')
                    ->where('id',$shu[$i]->goods_id)
                    ->select('goods_name','goods_img','goods_price')
                    ->get();
                $order_details['goods_name'] = $shop[0]->goods_name;
                $order_details['goods_img'] = $shop[0]->goods_img;
                $order_details['goods_price'] = $shop[0]->goods_price;
                OrderDetail::create($order_details);
            }
        //var_dump(DB::getQueryLog());
    });
        //$last_order_id = DB::table('orders')->select('id')->orderBy('id','desc')->value('id');
        return [
            "status"=>"true",
            "message"=>"添加成功",
            "order_id"=>$last_order_id,
        ];

    }

    public function order(Request $request)
    {
        $order = DB::table('orders')
            ->where('id',$request->id)
            ->get();
        $arr = [];
        foreach ($order as $ord)
        {
            $arr['id'] = $ord->id;
            $arr['order_code'] = $ord->sn;
            $arr['order_birth_time'] = $ord->created_at;
            $arr['order_status'] = '代付款';
            $arr['shop_id'] = $ord->shop_id;
            $shop = DB::table('shops')
                   ->where('id',$ord->shop_id)
                   ->select('shop_name','shop_img')
                   ->get();
            $arr['shop_name'] =$shop[0]->shop_name;
            $arr['shop_img'] =$shop[0]->shop_img;
            $arr['order_price'] =$ord->total;
            $arr['order_address'] =$ord->province.$ord->city.$ord->county.$ord->address;
            $order_deail = DB::table('order_details')
                ->where('order_id',$order[0]->id)
                ->select('goods_id','goods_name','goods_img','amount','goods_price')
                ->get();
            $arr['goods_list'] = $order_deail;
        }
        return $arr;
    }

    public function orderList()
    {
        //获取当前用户所有订单列表
        $order = DB::table('orders')
            ->where('user_id',Auth::id())
            ->orderBy('created_at','desc')
            ->offset(0)
            ->limit(5)
            ->get();
        $arr = [];
       foreach ($order as $ord)
        {
            $a = [];
            $a['id'] = $ord->id;
            $a['order_code'] = $ord->sn;
            $a['order_birth_time'] = $ord->created_at;
            $a['order_status'] = '代付款';
            $a['shop_id'] = $ord->shop_id;
            $shop = DB::table('shops')
                ->where('id',$ord->shop_id)
                ->select('shop_name','shop_img')
                ->get();
            $a['shop_name'] =$shop[0]->shop_name;
            $a['shop_img'] =$shop[0]->shop_img;
            $a['order_price'] =$ord->total;
            $a['order_address'] =$ord->province.$ord->city.$ord->county.$ord->address;
            $order_detail = DB::table('order_details')
                ->where('order_id',$ord->id)
                ->select('goods_id','goods_name','goods_img','amount','goods_price')
                ->get();
            $a['goods_list'] = $order_detail;
            $arr[] = $a;
        }
        return $arr;
    }
}
