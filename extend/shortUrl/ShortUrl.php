<?php
/**
 * 生成短连接地址
 * User: Administrator
 * Date: 2019/3/22
 * Time: 10:42
 */
namespace shortUrl;
class ShortUrl
{
    const ApiUrl = "http://api.t.sina.com.cn/short_url/shorten.json";
    const SOURCE = "3271760578";
    public static function geturl($longurl)
    {
        $url = self::ApiUrl.'?source='.self::SOURCE.'&url_long='.$longurl;
        try {
            $info = file_get_contents($url);
            $res = json_decode($info, true);
            return $res[0]['url_short'];
        } catch (\Exception $e) {
            return false;
        }
    }
}