<?php
/**
 * 获取玩家充值记录  5分钟左右读取一次
 */

namespace app\command;

use apiData\PlayerData;
use app\model\Paytime;
use app\model\Planlog;
use app\model\Thirdpaytime;
use think\console\Command;
use think\console\Input;
use think\console\Output;
use think\Db;

class GetRechargeInfo extends Command
{
    protected function configure()
    {
        // 指令配置
        $this->setName('getRechargeInfo');
        // 设置参数

    }

    protected function execute(Input $input, Output $output)
    {
//        $planlogModel = new Planlog();
//        $today        = date('Ymd');
//        $planId       = $planlogModel->add(
//            [
//                'plan'       => 'getRechargeInfo',
//                'day'        => $today,
//                'status'     => 0,
//                'inserttime' => date('Y-m-d H:i:s')
//            ]
//        );

        save_log('apidata/getRechargeInfo', "start at:" . date('Y-m-d H:i:s'));
        if (intval(date('G')) == 0 && intval(date('i')) < 10) {//前10分钟取前一天的
            $today = date('Ymd', strtotime('-1 day'));
        } else {
            $today = date('Ymd');
        }

        $info = PlayerData::getRechargeInfo($today);
        if ($info->code != 0) {
            $output->writeln('code:' . $info->code . ',msg:' . $info->message);
        } else {
            if (!$info->data) {
                //save_log('apidata/getRechargeInfo', "handlemsg:nodata");
                //$output->writeln('code:'.$info->code.',msg:'.$info->message.'data:nodata');
            } else {
                $thirdpaytimeModel = new Thirdpaytime();
                $paytimeModel      = new Paytime();
                $insertThirdData   = $insertPayData = [];
                $insertTime        = date('YmdHis');
                $addNum            = 0;
                foreach ($info->data as $data) {
                    //插入新增的
                    if (!$thirdpaytimeModel->getCount(['loginid' => $data->LoginId, 'totalfee' => $data->TotalFee, 'updatetime' => $data->UpdateTime])) {
                        $addNum++;
                        $insertThirdData[] = [
                            'totalfee'   => $data->TotalFee,
                            'loginid'    => $data->LoginId,
                            'updatetime' => $data->UpdateTime
                        ];
                        $insertPayData[]   = [
                            'userid'     => $data->LoginId,
                            'totalfee'   => change_to_yuan($data->TotalFee, 2),
                            'addtime'    => $insertTime,
                            'updatetime' => $data->UpdateTime,
                            'createday'  => $today
                        ];
                    }
                }

                if ($addNum > 0) {
                    Db::startTrans();
                    try {
                        $thirdpaytimeModel->addAll($insertThirdData);
                        $paytimeModel->addAll($insertPayData);
                        Db::commit();
                        save_log('apidata/getBillList', "recordnum:" . count($info->data) . ",addnum:" . $addNum . "handlemsg:insertsuccess");
                    } catch (\Exception $e) {
                        Db::rollback();
                        save_log('apidata/getBillList', "handlemsg:" . $e->getMessage());
                        $output->writeln('code:500,msg:' . $e->getMessage() . 'data:insertfail');
                    }
                }
            }
        }
        //$planlogModel->updateById($planId, ['updatetime' => date('Y-m-d H:i:s'), 'status' => 1]);
        save_log('apidata/getRechargeInfo', "end at:" . date('Y-m-d H:i:s'));
    }
}
