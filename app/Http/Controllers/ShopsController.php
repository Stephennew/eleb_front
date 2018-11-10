<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ShopsController extends Controller
{
    //商家信息列表
    public function businessList(Request $request)
    {
        $keyword = $request->keyword?$request->keyword:'';
        if($keyword){
            $shops = DB::table('shops')
                ->where('shop_name','like','%'.$keyword.'%')
                ->select('id','shop_name','shop_img','shop_rating','brand','on_time',
                    'fengniao','bao','piao','zhun','start_send','send_cost','notice','discount')
                ->get();
        }else{
            $shops = DB::table('shops')
                ->select('id','shop_name','shop_img','shop_rating','brand','on_time',
                    'fengniao','bao','piao','zhun','start_send','send_cost','notice','discount')
                ->get();
        }
        $res = [];
        foreach ($shops as $shop){
            $shop->distance = 635;
            $shop->estimate_time = 30;
            $res[] = $shop;
        }
        return $res;
    }

    public function business(Request $request)
    {
        //商家信息
        $shop = DB::table('shops')
            ->where('id',$request->id)
            ->select('id','shop_name','shop_img','shop_rating',
                'brand','on_time','fengniao','bao','piao','zhun','start_send','send_cost','notice',
                'discount')
            ->first();
        //$shop  stdClass
         $shop->commodity=[];
        //获取该商家菜品分类信息
        $cates =DB::table('menu_categories')
            ->where('shop_id',$request->id)
            ->select('id','description','is_selected','name','type_accumulation')
            ->get();
        //dd($cates);
        //获取该商家菜品分类下所有的菜品
        //$menus = DB::table('menus')->where('shop_id',$request->id)->get();
        foreach ($cates as $key=>$c){
            $menus = DB::table('menus')
                ->where('category_id',$c->id)
                ->select('id as goods_id','goods_name','goods_img','satisfy_rate','satisfy_count','tips','rating_count',
                    'month_sales','description','goods_price','rating')
                ->get();
            $c->goods_list=$menus;
            $shop->commodity[]=$c;
        }
        $shop->evaluate = [
            [   //评价
                "user_id"=>12344,
                "username"=>"w******k",
                "user_img"=>"/images/slider-pic4.jpeg",
                "time"=>"2017-2-22",
                "evaluate_code"=>1,
                "send_time"=>30,
                "evaluate_details"=>"不怎么好吃"
            ]
        ];
        //var_dump($shop);exit;
        /*foreach ($shop as $sh)
        {
            $sh->distance=637;
            $sh->estimate_time=31;
            $sh->service_code=4.6;// 服务总评分
            $sh->foods_code=4.4;// 食物总评分
            $sh->high_or_low=true;// 低于还是高于周边商家
            $sh->h_l_percent=30;// 低于还是高于周边商家的百分比
            foreach ($sh as $key=>$s)
            {
                $arr[$key] = $s;
            }
        }*/

//        foreach ($shop as $sh)
//        {
//
//            foreach ($cates as $key=>$c){
//
//                $menus = DB::table('menus')->where('category_id',$c->id)->get();
//                foreach ($menus as $k=>$m){
//                    $cates[$key]->goods_list[]=$menus[$k];
//                }
//
//                $sh->commodity[]=$cates[$key];
//            }
//
//            $arr[]=$sh;
//        }

        return json_encode($shop);

    }
}
