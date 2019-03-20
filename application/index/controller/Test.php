<?php

namespace app\index\controller;

use apiData\PlayerData;
use app\index\model\Paytime;
use app\index\model\Playerorder;
use app\index\model\Proxy;
use app\index\model\Teamlevel;
use apiData\Sms;
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
        $proxyId = 'FC0000007';
        $info = $proxyModel->getValue(['parent_id' => $proxyId], 'max(percent) percent');
        //$info = Ostime::getOsTime();

//        $info = intval(date('i'));
//        $info = intval('09');
        var_dump(intval($info));
        die;
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
