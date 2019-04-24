<?php
namespace app\index\controller;

use think\Db;
use think\Model;
use think\Controller;
use think\View;
use think\Cache;

use sms\SmsSenderUtil;
use Sms\SmsSingleSender;
use Sms\SmsMultiSender;




class Duanxin extends Index{

    

    public function send($tel=''){
        $num = rand(1000,9999);

        $data=array(
                    'tel'=>$tel,
                    'code'=>$num,
                    'time'=>date('Y-m-d H:i:s')
                );
        $res = db('code')->where('tel',$tel)->find();
            if(!empty($res)){
                $data=db("code")->where('tel',$tel)->update($data);//数据库没有就插入，有就更新
            }else{
                $data=db("code")->insert($data);
            }   
        $res = db('code')->where('tel',$tel)->value('code');

        // 短信应用SDK AppID
        $appid = appid; // 1400开头
        // 短信应用SDK AppKey
        $appkey = appkey;
        // 需要发送短信的手机号码
        $phoneNumbers = [$tel];
        // 短信模板ID，需要在短信应用中申请
        $templateId = templateId;  // NOTE: 这里的模板ID`7839`只是一个示例，真实的模板ID需要在短信控制台中申请
        // 签名
        $smsSign = smsSign; // NOTE: 这里的签名只是示例，请使用真实的已申请的签名，签名参数使用的是`签名内容`，而不是`签名ID`
        // 单发短信
        try {
            $ssender = new SmsSingleSender($appid, $appkey);
            $params = ["$num",'6'];//数组具体的元素个数和模板中变量个数必须一致，例如事例中 templateId:5678对应一个变量，参数数组中元素个数也必须是一个
            //$result = $ssender->sendWithParam("86", $phoneNumbers[0], $templateId, $params, $smsSign, "", "");  // 签名参数未提供或者为空时，会使用默认签名发送短信
            $result = $ssender->send(0, "86", $phoneNumbers[0], "【其声呜呜然】 ".$num."为您的登录验证码，请于5分钟内填写。如非本人操作，请忽略本短信。");
            //var_dump($result);
            $rsp = json_decode($result);
            $data = [
                'num' => $num,
            ];
            return $this->jsonaa('200','发送成功',$num);
            echo $result;
        } catch(\Exception $e) {
            echo var_dump($e);
        }

    }
    


    

}