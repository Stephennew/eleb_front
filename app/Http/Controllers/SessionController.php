<?php

namespace App\Http\Controllers;

use App\Model\Member;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redis;
use Validator;
use Illuminate\Http\Request;

class SessionController extends Controller
{
    public function loginCheck(Request $request)
    {
        //验证数据是否为空
        //手动创建验证器
        $validator = Validator::make($request->all(),[
            'name'=>'required',
            'password'=>'required'
        ],[
            'name.required'=>'用户名不能为空',
            'password.required'=>'密码不能为空',
        ]);
        //没有错误返回的是null   有未填写的数据返回 1
        //echo  $validator->fails();
        if($validator->fails()){
           $str = [];
           $strr ='';
           foreach ($validator->errors()->all() as $v)
            {
                $str[] = $v;
            }
            if(count($str) == 2){
               $strr.=$str[0].'和'.$str[1];
            }
            if(count($str) == 1){
                $strr.=$str[0];
            }
            return [
                'status'=>'-1',
                'message'=>$strr,
            ];
        }
        //验证用户名密码是否正确
        if(Auth::attempt(['username'=>$request->name,'password'=>$request->password])){
           return [
               "status"=>"true",
               "message"=>"登录成功",
               "user_id"=>Auth::user()->id,
               "username"=>Auth::user()->username,
            ];
        }else{
           return [
               'status'=>'false',
               'message'=>'用户名或密码错误，请重新输入！',
           ];
        }
    }


    public function register(Request $request)
    {
        //发送短信成功保存到redis  注册时在取出
        //将redis 中序列化后打的取出来进行反序列化再比较
        $sms = Redis::get('sms_'.$request->tel);
        if(trim($request->sms) != $sms){
            return [
                "status"=> "false",
                "message"=> "验证码错误，注册失败",
            ];
        }
        $data = [
          'username'=>$request->username,
          'tel'=>$request->tel,
          'status'=>1,
          'password'=>bcrypt($request->password),//加密
            'rememberToken'=>str_random(40),//随机token用于做自动登录使用的
        ];
        Member::create($data);
        return [
          "status"=>'true',
          'message'=>'恭喜您！注册成功'
        ];
    }

    public function changePassword(Request $request)
    {
        /**
         * oldPassword: 旧密码
         * newPassword: 新密码
        {
        "status": "true",
        "message": "修改成功"
        }
         */
        $members = Member::find(Auth::id());
        if (Hash::check($request->oldPassword, $members->password)) {
            Member::where('id',$members->id)->update(['password'=>bcrypt($request->newPassword)]);
            return [
                'status'=>'true',
                'message'=>'修改成功'
            ];
        }else{
            return [
                'status'=>'false',
                'message'=>'旧密码不正确，请重新输入！'
            ];
        }
    }

    public function forgetPassword(Request $request)
    {
        //var_dump($request);exit;
        /**
         * tel: 手机号
         * sms: 短信验证码
         * password: 密码
        {
        "status": "true",
        "message": "添加成功"
        }
         */
        //验证电话号码和验证码
        $code = Redis::get('sms_'.$request->tel);
        if(trim($request->sms) != $code){
            return [
              'status'=>'false',
              'message'=>'验证码错误',
            ];
        }
        //通过电话号码取出信息
        $member = Member::where('tel',$request->tel)->first();
        if($member == null){
            return [
              'status'=>'false',
              'message'=>'改号码还未注册，请注册！',
            ];
        }
        //重置密码
        Member::where('tel',$member->tel)->update(['password'=>bcrypt($request->password)]);
        return [
          'status'=>'true',
          'message'=>'重置密码成功',
        ];
    }
}
