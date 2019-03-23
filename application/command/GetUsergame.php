<?php
/**
 * 获取玩家游戏数据
 */

namespace app\command;

use apiData\PlayerData;
use app\model\Planlog;
use app\model\Playergame;
use app\model\Proxy;
use app\model\Thirdplayergame;
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
//        $planlogModel = new Planlog();
//        $today        = date('Ymd');
//        $planId       = $planlogModel->add(
//            [
//                'plan'       => 'getUsergame',
//                'day'        => $today,
//                'status'     => 0,
//                'inserttime' => date('Y-m-d H:i:s')
//            ]
//        );
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
            $addNum  = $updateNum = 0;
            $thirdplayergameModel = new Thirdplayergame();
            $playergameModel      = new Playergame();

            Db::startTrans();

            try {
                foreach ($info->data as $data) {
                    $row = $thirdplayergameModel->getRow(['userid' => $data->userid, 'roomname' => $data->roomname]);
                    if (!$row) {
                        $addNum++;
                        $thirdplayergameModel->add([
                            'userid'      => $data->userid,
                            'addtime'     => $data->addtime,
                            'changemoney' => $data->changemoney,
                            'nickname'    => $data->nickname,
                            'roomname'    => $data->roomname,
                            'inserttime'  => $insertTime,
                            'updatetime'  => $insertTime
                        ]);
                        $playergameModel->add([
                            'userid'      => $data->userid,
                            'addtime'     => $data->addtime,
                            'changemoney' => change_to_yuan($data->changemoney),
                            'nickname'    => $data->nickname,
                            'roomname'    => $data->roomname,
                            'inserttime'  => $insertTime,
                            'proxy_id'    => $proxy,
                            'updatetime'  => $insertTime
                        ]);

                    } else {
                        if ($row['changemoney'] != $data->changemoney) {
                            $updateNum++;
                            $thirdplayergameModel->updateById($row['id'], ['changemoney' => $data->changemoney, 'updatetime' => $insertTime]);
                            $playergameModel->updateByWhere(['userid' => $data->userid,'roomname' => $data->roomname], ['changemoney' => change_to_yuan($data->changemoney),'updatetime'  => $insertTime]);
                        }
                    }
                }
                Db::commit();
                save_log('apidata/getUsergame', "proxyid:".$proxy.",recordnum:" . count($info->data) . ",addnum:" . $addNum ."updatenum:".$updateNum. "handlemsg:insertsuccess");
            } catch(\Exception $e) {
                Db::rollback();
                save_log('apidata/getUsergame', "proxyid:".$proxy."handlemsg:" . $e->getMessage());
                $output->writeln('code:500,msg:' . $e->getMessage() . 'data:insertfail');
            }
        }
        //$planlogModel->updateById($planId, ['updatetime' => date('Y-m-d H:i:s'), 'status' => 1]);
        save_log('apidata/getUsergame', "end ");
    }
}
