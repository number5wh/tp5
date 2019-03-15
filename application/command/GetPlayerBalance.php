<?php
/**
 * 获取玩家余额
 */
namespace app\command;

use apiData\PlayerData;
use app\index\model\Player;
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
        save_log('apidata/getPlayerBalance', "start at:" . date('Y-m-d H:i:s'));
        $updateNum = 0;
        $info = PlayerData::getUserAccount();
        if ($info->code != 0) {
            $output->writeln('code:'.$info->code.',msg:'.$info->message);
        } else {
            if (!$info->data) {
                save_log('apidata/getPlayerBalance', "handlemsg:nodata");
                $output->writeln('code:'.$info->code.',msg:'.$info->message.'data:nodata');
            } else {
                $playerModel = new Player();
                $updateTime = date('YmdHis');
                foreach ($info->data as $data) {
                    if ($playerModel->getCount(['userid' => $data->userid])) {
                        $playerModel->updateByWhere(['userid' => $data->userid], ['leftmoney' => $data->balance, 'updatemoney' => $updateTime]);
                        $updateNum++;
                    }
                }
            }
        }

        save_log('apidata/getPlayerBalance', "end at:" . date('Y-m-d H:i:s').',updatenum:'.$updateNum);
    }
}
