<?php
/**
 * 获取玩家游戏数据
 */

namespace app\command;

use apiData\PlayerData;
use app\index\model\Planlog;
use app\index\model\Playergame;
use app\index\model\Proxy;
use app\index\model\Thirdplayergame;
use think\console\Command;
use think\console\Input;
use think\console\Output;
use think\Db;

class GetUsergame extends Command
{
    protected function configure()
    {
        // 指令配置
        $this->setName('getUsergame');
        // 设置参数

    }

    protected function execute(Input $input, Output $output)
    {
        $planlogModel = new Planlog();
        $today        = date('Ymd');
        $planId       = $planlogModel->add(
            [
                'plan'       => 'getUsergame',
                'day'        => $today,
                'status'     => 0,
                'inserttime' => date('Y-m-d H:i:s')
            ]
        );
        save_log('apidata/getUsergame', "start ");
        //获取代理列表
        $today      = date('Ymd');
        $proxyModel = new Proxy();
        $proxyList  = $proxyModel->getListAll([], 'code');
        $proxyList  = array_column($proxyList, 'code');
        $insertTime = date('YmdHis');
        foreach ($proxyList as $proxy) {
            //循环获取游戏数据
            $info = PlayerData::getUsergame($proxy);
            if ($info->code != 0) {
                $output->writeln('proxy' . $proxy . ',code:' . $info->code . ',msg:' . $info->message);
                continue;
            }
            if (!$info->data) {
                //save_log('apidata/getUsergame', "proxyId:{$proxy},handlemsg:nodata");
                //$output->writeln('code:' . $info->code . ',msg:' . $info->message . 'data:nodata');
                continue;
            }

            $insertThirdData      = $insertData = [];
            $addNum               = 0;
            $thirdplayergameModel = new Thirdplayergame();
            $playergameModel      = new Playergame();

            foreach ($info->data as $data) {
                if (!$thirdplayergameModel->getCount(['userid' => $data->userid, 'addtime' => $data->addtime, 'changemoney' => $data->changemoney])) {
                    $addNum++;
                    $insertThirdData[] = [
                        'userid'      => $data->userid,
                        'addtime'     => $data->addtime,
                        'changemoney' => $data->changemoney,
                        'nickname'    => $data->nickname,
                        'roomname'    => $data->roomname,
                        'inserttime'  => $insertTime
                    ];
                    $insertData[]      = [
                        'userid'      => $data->userid,
                        'addtime'     => $data->addtime,
                        'changemoney' => change_to_yuan($data->changemoney),
                        'nickname'    => $data->nickname,
                        'roomname'    => $data->roomname,
                        'inserttime'  => $insertTime,
                        'proxy_id'    => $proxy
                    ];
                }
            }

            if ($addNum > 0) {
                Db::startTrans();
                try {
                    $thirdplayergameModel->addAll($insertThirdData);
                    $playergameModel->addAll($insertData);
                    Db::commit();
                    save_log('apidata/getUsergame', "recordnum:" . count($info->data) . ",addnum:" . $addNum . "handlemsg:insertsuccess");
                } catch (\Exception $e) {
                    Db::rollback();
                    save_log('apidata/getUsergame', "handlemsg:" . $e->getMessage());
                    $output->writeln('code:500,msg:' . $e->getMessage() . 'data:insertfail');
                }
            }
        }
        $planlogModel->updateById($planId, ['updatetime' => date('Y-m-d H:i:s'), 'status' => 1]);
        save_log('apidata/getUsergame', "end ");
    }
}
