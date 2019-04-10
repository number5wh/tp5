<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/3/9
 * Time: 9:59
 */
namespace apiData;
use Hprose\Client;
class Sms
{

//    /**
//     * @param $proxyId 发送人id
//     * @param $mobile  手机号
//     * 检查最近的发送记录
//     * @return array
//     */
//    public static function checkRecently($proxyId, $mobile, $event)
//    {
//        $code = 0;
//        $msg  = '';
//        //检查最近的发送记录
//        $smsModel = new SmsModel();
//        $info = $smsModel->getRecent($proxyId, $mobile, $event);
//        if ($info && $info['expiretime'] < time()) {
//            $code = 1;
//            $msg  = config('msg.sms_expire');
//        }
//        $data = [
//            'code' => $code,
//            'msg'  => $msg
//        ];
//        return $data;
//    }
    /*
     * 发送短信
     */
    public static function send_sms($mobile)
    {
        $ServiceUrl = config("api.ServiceUrl");
        $client     = Client::create($ServiceUrl, false);
        try {
            $timestamp = time();
            $appkey    = config('api.appkey');
            $token     = md5($appkey . $mobile . $timestamp);
            $ret       = $client->sendCode($mobile, $timestamp, get_client_ip(), $token);
            save_log('sms', "mobile:{$mobile},data:{$ret->data},code:{$ret->code},msg:{$ret->message}");
            return $ret;
        } catch (\Exception $e) {
            save_log('sms', "mobile:{$mobile},".json_encode($e->getMessage()));
            return (object)['code' => 100];
        }
    }


    /*
     * 检测验证码
     */
    public static function validateSms($mobile,$code){
        $ServiceUrl = config("api.ServiceUrl");
        $client = Client::create($ServiceUrl,false);
        try {
            $timestamp = time();
            $appkey =config('api.appkey');
            $token = md5($appkey.$mobile.$code.$timestamp);
            $ret = $client->validateCode($mobile,$code,$timestamp,$token);
            return  $ret;
        }catch (\Exception $e) {
            return  false;
        }
    }

}