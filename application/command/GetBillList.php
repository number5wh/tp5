<?php

namespace app\command;

use apiData\PlayerData;
use app\index\model\Proxy;
use app\index\model\Teamlevel;
use think\console\Command;
use think\console\Input;
use think\console\Output;

class GetBillList extends Command
{
    protected function configure()
    {
        // 指令配置
        $this->setName('getBillList');
        // 设置参数
        
    }

    protected function execute(Input $input, Output $output)
    {
        save_log('apidata/getBillList', "start at:".date('Y-m-d H:i:s'));
        //获取代理列表
        $proxyModel = new Proxy();
        $proxyList = $proxyModel->getListAll([], 'code');
        $proxyList = array_column($proxyList, 'code');
        foreach ($proxyList as $proxy) {
            //循环获取账单
            $info = PlayerData::getBillList($proxy);
            if ($info->code != 0) {
                $output->writeln('code:'.$info->code.',msg:'.$info->msg);
            } else {
                if (!$info->data) {
                    save_log('apidata/getBillList', "proxyId:{$proxy},handlemsg:nodata");
                    $output->writeln('code:'.$info->code.',msg:'.$info->msg.'data:nodata');
                } else {
                    $teamlevelModel = new Teamlevel();
                    //分成级别
                    $teamlevels = $teamlevelModel->getListAll(['proxy_id' => $proxy]);
                    //获取自身分成
                    $selfPercent = $proxyModel->getValue(['code' => $proxy], 'percent');

                }
            }
        }
    }
}
