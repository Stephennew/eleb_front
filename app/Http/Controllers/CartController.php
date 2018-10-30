<?php

namespace App\Http\Controllers;

use App\Model\Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CartController extends Controller
{
    public function cartView()
    {
        /*
         *  {
              "goods_list": [
                {
                    "goods_id": "1",
                    "goods_name": "汉堡",
                    "goods_img": "http://www.homework.com/images/slider-pic2.jpeg",
                    "amount": 6,
                    "goods_price": 10
              },{
                    "goods_id": "1",
                    "goods_name": "汉堡",
                    "goods_img": "http://www.homework.com/images/slider-pic2.jpeg",
                    "amount": 6,
                    "goods_price": 10
              }
            ],
            "totalCost": 120
        }
        */
       $carts = DB::table('carts')->where('user_id',Auth::id())->select('goods_id','amount')->get();
       $arr = [];
       foreach ($carts as $cr)
       {
           $menus = DB::table('menus')
               ->where('id',$cr->goods_id)
               ->select('goods_name','goods_price')
               ->get();
           $menus[0]->goods_id = $cr->goods_id;
           $menus[0]->amount = $cr->amount;
           foreach ($menus as $m)
           {
               $arr['goods_list'][] = $m;
           }
       }
       return $arr;
    }

    public function addCart(Request $request)
    {
        /*
         *  * goodsList: 商品列表
         * goodsCount: 商品数量
         * user_id
         {
              "status": "true",
              "message": "添加成功"
           }*/
        if(!$request->input('goodsList')){
            return [
              'status'=>'false',
              'message'=>'还未添加商品哦，快添加哦！！！',
            ];
        }
        $a = count($request->input('goodsList'));
        for ($i=0;$i<=$a-1;++$i)
        {
            Cart::create([
                'amount'=>$request->input('goodsCount')[$i],
                'goods_id'=>$request->input('goodsList')[$i],
                'user_id'=>Auth::id(),//可以直接获取已经认证的用户ID
                ]);
        }
        return [
          'status'=>'true',
          'message'=>'添加成功',
        ];
    }
}
