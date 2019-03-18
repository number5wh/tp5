<?php
/**
 * 获取玩家注册列表 5分钟左右读取一次
 */

namespace app\command;

use apiData\PlayerData;
use app\index\model\Planlog;
use app\index\model\Proxy;
use app\index\model\Thirdplayer;
use app\index\model\Player;
use think\console\Command;
use think\console\Input;
use think\console\Output;
use think\Db;

class GetPlayerList extends Command
{
    protected function configure()
    {
        // 指令配置
        $this->setName('getPlayerList');
        // 设置参数
    }

    protected function execute(Input $input, Output $output)
    {
        $planlogModel = new Planlog();
        $today        = date('Ymd');
        $planId       = $planlogModel->add(
            [
                'plan'       => 'getPlayerList',
                'day'        => $today,
                'status'     => 0,
                'inserttime' => date('Y-m-d H:i:s')
            ]
        );

        save_log('apidata/getPlayerList', "start ");
        //获取代理列表
        $proxyModel = new Proxy();
        $proxyList  = $proxyModel->getListAll([], 'code');
        $proxyList  = array_column($proxyList, 'code');
        foreach ($proxyList as $proxy) {
            //循环获取各自的玩家
            $info = PlayerData::getPlayerList($proxy);
            if ($info->code != 0) {
                $output->writeln('proxy' . $proxy . ',code:' . $info->code . ',msg:' . $info->message);
                continue;
            }
            if (!$info->data) {
                //save_log('apidata/getPlayerList', "proxyId:{$proxy},handlemsg:nodata");
                //$output->writeln('code:' . $info->code . ',msg:' . $info->message . 'data:nodata');
                continue;
            }
            $thirdplayerModel = new Thirdplayer();
            $playerModel      = new Player();
            $insertThirdData  = $insertPlayerData = [];
            $insertTime       = date('YmdHis');
            $thirdnum         = 0;
            foreach ($info->data as $data) {
                if (!$thirdplayerModel->getCount(['userid' => $data->userid])) {
                    $insertThirdData[]  = [
                        'userid'     => $data->userid,
                        'accountid'  => $data->accountid,
                        'regtime'    => $data->regtime,
                        //'ismobile'   => $data->ismobile,
                        'nickname'   => $data->nickname,
                        'balance'    => $data->balance,
                        'inserttime' => $insertTime
                    ];
                    $insertPlayerData[] = [
                        'userid'    => $data->userid,
                        'accountid' => $data->accountid,
                        'regtime'   => $data->regtime,
                        //'ismobile' => $data->ismobile,
                        'proxy_id'  => $proxy, //是$proxy，记得改回来
                        'nickname'  => $data->nickname,
                        'leftmoney'   => change_to_yuan($data->balance),
                        'addtime'   => $insertTime
                    ];
                    $thirdnum++;
                }
            }


            //插入数据
            if ($thirdnum > 0) {
                Db::startTrans();
                try {
                    //插入第三方表数据
                    $thirdplayerModel->addAll($insertThirdData);
                    //插入玩家表数据
                    $playerModel->addAll($insertPlayerData);

                    Db::commit();
                    save_log('apidata/getPlayerList', "proxyId:{$proxy},thirdnum:{$thirdnum},handlemsg:insertsuccess");
                } catch (\Exception $e) {
                    Db::rollback();
                    save_log('apidata/getPlayerList', "proxyId:{$proxy},handlemsg:{$e->getMessage()}");
                    $output->writeln('code:500,msg:' . $e->getMessage() . 'data:insertfail');
                }
            }

        }
        $planlogModel->updateById($planId, ['updatetime' => date('Y-m-d H:i:s'), 'status' => 1]);
        save_log('apidata/getPlayerList', "end ");
    }
}
