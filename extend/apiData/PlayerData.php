<?php
/**
 * 获取充值接口
 * User: Administrator
 * Date: 2019/3/13
 * Time: 10:39
 */
namespace apiData;
use Hprose\Client;
class PlayerData
{
    /**
     * 获取玩家充值接口
     * @param $time String 日期：20181212
     * @return object
     * @throws \Exception
     */
    public static function getRechargeInfo($time)
    {
        $ServiceUrl = config("api.ServiceUrl");
        $client     = Client::create($ServiceUrl, false);
        try {
            $timestamp = time();
            $appkey    = config('api.appkey');
            $token     = md5($appkey . $time . $timestamp);
            $ret       = $client->getRechargeInfo($time, $timestamp, $token);
            //save_log('apidata/getRechargeInfo', "code:{$ret->code}, message:{$ret->message}");
            return $ret;
        } catch (\Exception $e) {
            save_log('apidata/getRechargeInfo', "code:500, message:{$e->getMessage()}");
            return (object)['code' => 500, 'message' => $e->getMessage()];
        }
    }

    /**
     * 获取代理的玩家列表
     * @param $proxyId String 代理账号code
     * @return object
     * @throws \Exception
     */
    public static function getPlayerList($proxyId)
    {
        $ServiceUrl = config("api.ServiceUrl");
        $client     = Client::create($ServiceUrl, false);
        try {
            $timestamp = time();
            $appkey    = config('api.appkey');
            $token     = md5($appkey . $proxyId .  $timestamp);
            $ret       = $client->getPlayerList($proxyId, $timestamp,  $token);
            //save_log('apidata/getPlayerList', "proxyId:{$proxyId},code:{$ret->code}, message:{$ret->message}");
            return $ret;
        } catch (\Exception $e) {
            save_log('apidata/getPlayerList', "proxyId:{$proxyId},code:500, message:{$e->getMessage()}");
            return (object)['code' => 500, 'message' => $e->getMessage()];
        }
    }

    /**
     * 获取代理商玩家税收
     * @param $proxyId String 代理商code
     * @return object
     * @throws \Exception
     */
    public static function getBillList($proxyId)
    {
        $ServiceUrl = config("api.ServiceUrl");
        $client     = Client::create($ServiceUrl, false);
        try {
            $timestamp = time();
            $appkey    = config('api.appkey');
            $token     = md5($appkey . $proxyId .  $timestamp);
            $ret       = $client->getBillList($proxyId, $timestamp,  $token);
            save_log('apidata/getBillList', "proxyId:{$proxyId},code:{$ret->code}, message:{$ret->message}");
            return $ret;
        } catch (\Exception $e) {
            save_log('apidata/getBillList', "proxyId:{$proxyId},code:500, message:{$e->getMessage()}");
            return (object)['code' => 500, 'message' => $e->getMessage()];
        }
    }

    //获取在线玩家列表
    public static function getOnlineList($proxyId)
    {
        $ServiceUrl = config("api.ServiceUrl");
        $client     = Client::create($ServiceUrl, false);
        try {
            $timestamp = time();
            $appkey    = config('api.appkey');
            $token     = md5($appkey . $proxyId .  $timestamp);
            $ret       = $client->getOnlineUser($proxyId, $timestamp,  $token);
            return $ret;
        } catch (\Exception $e) {
            return (object)['code' => 500, 'message' => $e->getMessage()];
        }
    }

    //获取玩家游戏明细
    public static function getUsergame($proxyId)
    {
        $ServiceUrl = config("api.ServiceUrl");
        $client     = Client::create($ServiceUrl, false);
        try {
            $timestamp = time();
            $appkey    = config('api.appkey');
            $token     = md5($appkey . $proxyId .  $timestamp);
            $ret       = $client->getUsergame($proxyId, $timestamp,  $token);
            //save_log('apidata/getUsergame', "proxyId:{$proxyId},code:{$ret->code}, message:{$ret->message}");
            return $ret;
        } catch (\Exception $e) {
            save_log('apidata/getUsergame', "proxyId:{$proxyId},code:500, message:{$e->getMessage()}");
            return (object)['code' => 500, 'message' => $e->getMessage()];
        }
    }

    //获取玩家余额
    public static function getUserAccount()
    {
        $ServiceUrl = config("api.ServiceUrl");
        $client     = Client::create($ServiceUrl, false);
        try {
            $timestamp = time();
            $appkey    = config('api.appkey');
            $token     = md5($appkey . $timestamp);
            $ret       = $client->getUserAccount($timestamp,  $token);
            return $ret;
        } catch (\Exception $e) {
            return (object)['code' => 500, 'message' => $e->getMessage()];
        }
    }
}