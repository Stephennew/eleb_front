<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;

class SmsContorller extends Controller
{
    //发送短信验证码
    public function sendSms(Request $request)
    {
        $params = array ();

        // *** 需用户填写部分 ***
        // fixme 必填：是否启用https
        $security = false;

        // fixme 必填: 请参阅 https://ak-console.aliyun.com/ 取得您的AK信息
        $accessKeyId = "LTAI7lBhYhH04u3Y";
        $accessKeySecret = "k9qyjWsavO4AlZhTosfEzzUXOwbADv";

        // fixme 必填: 短信接收号码
        $params["PhoneNumbers"] = $request->tel;

        // fixme 必填: 短信签名，应严格按"签名名称"填写，请参考: https://dysms.console.aliyun.com/dysms.htm#/develop/sign
        $params["SignName"] = "健康快乐每天";

        // fixme 必填: 短信模板Code，应严格按"模板CODE"填写, 请参考: https://dysms.console.aliyun.com/dysms.htm#/develop/template
        $params["TemplateCode"] = "SMS_149102604";

        // fixme 可选: 设置模板参数, 假如模板中存在变量需要替换则为必填项
        //$str = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijglmnopqrstuvwxyz123456789';
        //$string = substr(str_shuffle($str),0,4);
        $yanzeng = rand(1000,9999);
        $params['TemplateParam'] = Array (
            //这里code就是发送给用户的验证码
            "code" => $yanzeng,
            //"product" => "阿里通信"
        );

        // fixme 可选: 设置发送短信流水号
        $params['OutId'] = "12345";

        //fixme 可选: 上行短信扩展码, 扩展码字段控制在7位或以下，无特殊需求用户请忽略此字段
        $params['SmsUpExtendCode'] = "1234567";


        // *** 需用户填写部分结束, 以下代码若无必要无需更改 ***
        if(!empty($params["TemplateParam"]) && is_array($params["TemplateParam"])) {
            $params["TemplateParam"] = json_encode($params["TemplateParam"], JSON_UNESCAPED_UNICODE);
        }

        // 初始化SignatureHelper实例用于设置参数，签名以及发送请求
        //请求的是这个类来实现的发短信的功能
        $helper = new SignatureHelper();

        // 此处可能会抛出异常，注意catch
        $content = $helper->request(
            $accessKeyId,
            $accessKeySecret,
            "dysmsapi.aliyuncs.com",
            array_merge($params, array(
                "RegionId" => "cn-hangzhou",
                "Action" => "SendSms",
                "Version" => "2017-05-25",
            )),
            $security
        );
        //dd($content);
        //Redis::set('sms',$yanzeng,);
        //（String）过期时间正确设置方法：
        //$expired_at 就是过期时间，单位秒
        Redis::setex( 'sms_'.$request->tel , 300 , $yanzeng);
        return [
            "status"=>"true",
            "message"=>"获取短信验证码成功"
        ];

    }
}
