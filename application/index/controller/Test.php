<?php

namespace app\index\controller;

use apiData\PlayerData;
use app\index\model\Paytime;
use app\index\model\Playerorder;
use app\index\model\Proxy;
use app\index\model\Teamlevel;
use apiData\Sms;
use Endroid\QrCode\QrCode;
use think\Controller;
use think\Db;
use think\Request;

class Test extends Controller
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {


        $info = PlayerData::getOnlineList('FC0000004');
        $proxyModel = new Proxy();
        $proxyId = 'FC0000004';
        $info = compile($proxyId);
        $url = "http://distrbute.game2019.com/?proxyid=".$info;
        //$info = Ostime::getOsTime();

//        $info = intval(date('i'));
//        $info = intval('09');
        $qrCode = new QrCode('http://www.baidu.com');
//        $qrCode->setErrorCorrectionLevel('Q');

        header('Content-Type: '.$qrCode->getContentType());
        $qrCode->writeFile(env('root_path').'public/upload/qrcode/test.png');
        exit;
    }

    /**
     * 显示创建资源表单页.
     *
     * @return \think\Response
     */
    public function create()
    {
        //
    }

    /**
     * 保存新建的资源
     *
     * @param  \think\Request  $request
     * @return \think\Response
     */
    public function save(Request $request)
    {
        //
    }

    /**
     * 显示指定的资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function read($id)
    {
        //
    }

    /**
     * 显示编辑资源表单页.
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * 保存更新的资源
     *
     * @param  \think\Request  $request
     * @param  int  $id
     * @return \think\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * 删除指定资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function delete($id)
    {
        //
    }
}
