<?php
/**
 * 生成二维码
 * User: Administrator
 * Date: 2019/3/20
 * Time: 18:28
 */
namespace qrCode;

class Code
{

    public static function qrcode($proxyId)
    {
        //加载第三方类库
        require_once env('root_path').'vendor/phpqrcode/phpqrcode.php';
        $url=config("config.qrcode_url").urlencode(compile($proxyId));
        $size=4;    //图片大小
        $errorCorrectionLevel = "Q"; // 容错级别：L、M、Q、H
        $matrixPointSize = "4"; // 图片大小：1到10
        //实例化
        $qr = new \QRcode();

        //会清除缓冲区的内容，并将缓冲区关闭，但不会输出内容。
        ob_end_clean();

        if (!file_exists(config('config.qrcode_dir'))) {
            mkdir(config('config.qrcode_dir'), 0777, true);
        }

        $output = config('config.qrcode_dir').DIRECTORY_SEPARATOR.$proxyId.'.png';
        $qr::png($url, $output, $errorCorrectionLevel, $matrixPointSize);
    }
}