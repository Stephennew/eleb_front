<?php

namespace App\Http\Controllers;

use App\Model\Address;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Validator;
use Illuminate\Http\Request;

class AddressController extends Controller
{
    public function addAddress(Request $request)
    {
        /*
         * /**
         * name: 收货人
         * tel: 联系方式
         * provence: 省
         * city: 市
         * area: 区
         * detail_address: 详细地址
         * user_id  用户id
         * is_default 是否是默认地址
         *
         * {
          "status": "true",
          "message": "添加成功"
            }


         'name' => string 'aaa' (length=3)
          'tel' => string 'aaa' (length=3)
          'provence' => string 'aa' (length=2)
          'city' => string 'aa' (length=2)
          'area' => string 'aa' (length=2)
          'detail_address' => string 'aaa' (length=3)
        */
        //var_dump($request);exit;
        $data = [
          'name'=>$request->name,
          'provence'=>$request->provence,
          'city'=>$request->city,
          'area'=>$request->area,
          'detail_address'=>$request->detail_address,
            'user_id'=>Auth::id(),
        ];
        //preg_match('/^1[3,4,5,7,8]\d{9}$/',$request->tel)
        if(!is_numeric($request->tel)){
            return [
              'status'=>'false',
              'message'=>'您的电话号码必须是数字'
            ];
        }
        if(strlen($request->tel) != 11){
            return [
                'status'=>'false',
                'message'=>'您的电话号码必输是11位的数字'
            ];
        }
        $data['tel'] = $request->tel;
        Address::create($data);
        return [
            'status'=>'true',
            'message'=>'添加成功',
        ];
    }

    public function addressList()
    {
        /*
         *
         * 响应的数据是一个二维数组
          *[
         * {
                  "id": "1",
                  "provence": "四川省",
                  "city": "成都市",
                  "area": "武侯区",
                  "detail_address": "四川省成都市武侯区天府大道56号",
                  "name": "张三",
                  "tel": "18584675789"
             },
            {
                  "id": "2",
                 "provence": "河北省",
                 "city": "保定市",
                 "area": "武侯区",
                 "detail_address": "四川省成都市武侯区天府大道56号",
                 "name": "张三",
                 "tel": "18584675789"
            }
        ]

         * */
        //地址列表
        $addressList = DB::table('addresses')
            ->where('user_id',Auth::id())
            ->select('id','provence','city','area','name','tel','detail_address')
            ->get();

        return $addressList;
    }

    public function editView(Request $request)
    {
        /*{
          "id": "2",
         "provence": "河北省",
         "city": "保定市",
         "area": "武侯区",
         "detail_address": "四川省成都市武侯区天府大道56号",
         "name": "张三",
         "tel": "18584675789"
        }*/
        $address = DB::table('addresses')
            ->where('id',$request->id)
            ->select('id','provence','city','area','detail_address','name','tel')
            ->get();

        /*$addresses [] = $address;
        $kname = array('id', 'provence', 'city', 'area', 'detail_address','name','tel');

        function zuhe(&$v, $k, $kname) {

            $v = array_combine($kname, array_slice($v, 0, -1));

        }
        array_walk($addresses, 'zuhe', $kname);*/
        /*$address->each(function ($item,$key) use ($addresses){

        });*/
        //$address->
        //$addresses []=$address;
        $ad = [];
        foreach ($address as $v)
        {
            foreach ($v as $k=>$a)
            {
                $ad[$k] = $a;
            }
        }
        return $ad;

    }

    public function editAddress(Request $request)
    {
        $data = [
            'name'=>$request->name,
            'provence'=>$request->provence,
            'city'=>$request->city,
            'area'=>$request->area,
            'detail_address'=>$request->detail_address,

        ];
        if(!is_numeric($request->tel)){
            return [
                'status'=>'false',
                'message'=>'您的电话号码必须是数字'
            ];
        }
        if(strlen($request->tel) != 11){
            return [
                'status'=>'false',
                'message'=>'您的电话号码必输是11位的数字'
            ];
        }
        $data['tel'] = $request->tel;
        Address::create($data);
        return [
            'status'=>'true',
            'message'=>'修改成功',
        ];
    }
}
