<?php

namespace app\command;

use apiData\PlayerData;
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
    	// 指令输出
        save_log('apidata/getPlayerList', "start at:".date('Y-m-d H:i:s'));
        //获取代理列表
        $proxyModel = new Proxy();
        $proxyList = $proxyModel->getListAll([], 'code');
        $proxyList = array_column($proxyList, 'code');
        foreach ($proxyList as $proxy) {
            //循环获取各自的玩家
            $info = PlayerData::getPlayerList($proxy);
            if ($info->code != 0) {
                $output->writeln('code:'.$info->code.',msg:'.$info->msg);
            } else {
                if (!$info->data) {
                    save_log('apidata/getPlayerList', "proxyId:{$proxy},handlemsg:nodata");
                    $output->writeln('code:'.$info->code.',msg:'.$info->msg.'data:nodata');
                }
                $thirdplayerModel = new Thirdplayer();
                $playerModel = new Player();
                $insertThirdData = $insertPlayerData = [];
                $insertTime = date('YmdHis');
                $thirdnum = $playernum = 0;
                foreach ($info->data as $data) {
                    if (!$thirdplayerModel->getCount(['userid' => $data->userid])) {
                        $insertThirdData[] = [
                            'userid' => $data->userid,
                            'accountid' => $data->accountid,
                            'regtime' => $data->regtime,
                            //'ismobile' => $data->ismobile,
                            'nickname' => $data->nickname,
                            'inserttime' => $insertTime
                        ];
                        $thirdnum++;
                    }

                    if (!$playerModel->getCount(['userid' => $data->userid])) {
                        $insertPlayerData[] = [
                            'userid' => $data->userid,
                            'accountid' => $data->accountid,
                            //'ismobile' => $data->ismobile,
                            'regtime' => $data->regtime,
                            'proxy_id' => $data->proxy, //是$proxy，记得改回来
                            'nickname' => $data->nickname,
                            'addtime' => $insertTime
                        ];
                        $playernum++;
                    }
                }


                //插入数据
                if ($insertThirdData || $insertPlayerData) {
                    Db::startTrans();
                    try {
                        //插入第三方表数据
                        if ($insertThirdData) {
                            $thirdplayerModel->addAll($insertThirdData);
                        }
                        //插入玩家表数据
                        if ($insertPlayerData) {
                            $playerModel->addAll($insertPlayerData);
                        }
                        save_log('apidata/getPlayerList', "proxyId:{$proxy},thirdnum:{$thirdnum},playernum:{$playernum},handlemsg:insertsuccess");
                        Db::commit();
                    } catch (\Exception $e) {
                        save_log('apidata/getPlayerList', "proxyId:{$proxy},handlemsg:{$e->getMessage()}");
                        $output->writeln('code:111,msg:'.$e->getMessage().'data:insertfail');
                    }
                }

            }
        }
        save_log('apidata/getPlayerList', "end at:".date('Y-m-d H:i:s'));
    }
}
