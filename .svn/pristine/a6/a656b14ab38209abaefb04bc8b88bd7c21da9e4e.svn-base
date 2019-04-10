<?php
/**
 * 获取玩家余额
 */

namespace app\command;

use apiData\PlayerData;
use app\model\Planlog;
use app\model\Player;
use think\console\Command;
use think\console\Input;
use think\console\Output;

class GetPlayerBalance extends Command
{
    protected function configure()
    {
        // 指令配置
        $this->setName('getPlayerBalance');
        // 设置参数

    }

    protected function execute(Input $input, Output $output)
    {
//        $planlogModel = new Planlog();
//        $today        = date('Ymd');
//        $planId       = $planlogModel->add(
//            [
//                'plan'       => 'getPlayerBalance',
//                'day'        => $today,
//                'status'     => 0,
//                'inserttime' => date('Y-m-d H:i:s')
//            ]
//        );


        save_log('apidata/getPlayerBalance', "start ");
        $updateNum = 0;
        $unUpdate  = [];
        $info      = PlayerData::getUserAccount();

        if ($info->code != 0) {
            $output->writeln('code:' . $info->code . ',msg:' . $info->message);
        } else {
            if (!$info->data) {
                save_log('apidata/getPlayerBalance', "handlemsg:nodata");
                //$output->writeln('code:'.$info->code.',msg:'.$info->message.'data:nodata');
            } else {
                $playerModel = new Player();
                $updateTime  = date('YmdHis');
                foreach ($info->data as $data) {
                    if ($playerModel->getCount(['userid' => $data->userid])) {
                        $playerModel->updateByWhere(['userid' => $data->userid], ['leftmoney' => change_to_yuan($data->balance, 2), 'updatemoney' => $updateTime]);
                        $updateNum++;
                    } else {
                        $unUpdate[] = $data->userid;
                    }
                }
            }
        }
        //$planlogModel->updateById($planId, ['updatetime' => date('Y-m-d H:i:s'), 'status' => 1]);
        save_log('apidata/getPlayerBalance', "end ,totalnum:" . count($info->data) . ',updatenum:' . $updateNum . ',unupdate:' . json_encode($unUpdate));
    }
}
