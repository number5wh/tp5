<?php
/**
 * 常规配置
 * User: wuhao
 * Date: 2019/3/8
 * Time: 20:30
 */
return [
    'cookie_expire' => 7*24*3600,
    //二维码
    'qrcode_dir'    => env('root_path').'public/upload/qrcode',
    'qrcode_url'    => 'http://distribute.game2019.net/?proxyid=',
    'upload_dir'    => env('root_path').'public/upload',

    //log文件地址
    'log_dir'       => env('root_path') . 'log',
    //提现状态
    'checklog_status' => [
        0 => '系统处理中',
        1 => '已审核',
        2 => '已驳回',
        3 => '已完成',
    ],
    'checklog_tax' => 0.01,
    'minimum_money' => 100,
    'checklog_status_other' => '系统处理中',
    //提现类型
    'check_type' => [
        1 => '支付宝',
        2 => '银行卡'
    ],
    //短信超时时间
    'sms_expire' => 60,

    //分成比例相差值
    'percent_diff' => 10
];