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
        //
        $proxyModel = new Proxy();
        $proxy = 'WZ0000011';
        $teamlevelModel = new Teamlevel();
        $insertThirdData = $insertIncomeData = $levelData = [];
        //获取自身分成
        $selfPercent = $proxyModel->getValue(['code' => $proxy], 'percent');
        $levelData[0] = [
            'proxy_id' => $proxy,
            'parent_id' => '',
            'level' => 0,
            'percent' => $selfPercent
        ];
        //分成级别
        $teamlevels = $teamlevelModel->getListAll(['proxy_id' => $proxy]);
        if ($teamlevels) {
            foreach ($teamlevels as $l) {
                $percent = $proxyModel->getValue(['code' => $l['parent_id']], 'percent');
                $levelData[$l['level']] = [
                    'proxy_id' => $proxy,
                    'parent_id' => $l['parent_id'],
                    'level' => $l['level'],
                    'percent' => $percent
                ];
            }
        }


        //$info = PlayerData::getOnlineList('WZ0000011');
        //$info = Ostime::getOsTime();
        $totalTax = tax_change(65*20/100);
        var_dump($levelData);
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
