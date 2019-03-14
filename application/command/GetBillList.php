<?php
/**
 * 获取税收账单(一天读取一次)
 */
namespace app\command;

use apiData\PlayerData;
use app\index\model\Incomelog;
use app\index\model\Playerorder;
use app\index\model\Proxy;
use app\index\model\Teamlevel;
use app\index\model\ThirdPlayerOrder;
use think\console\Command;
use think\console\Input;
use think\console\Output;
use think\Db;

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
        save_log('apidata/getBillList', "start at:" . date('Y-m-d H:i:s'));
        //获取代理列表
        $today      = date('Ymd');
        $proxyModel = new Proxy();
        $proxyList  = $proxyModel->getListAll([], 'code');
        $proxyList  = array_column($proxyList, 'code');
        foreach ($proxyList as $proxy) {
            //循环获取账单
            $info = PlayerData::getBillList($proxy);
            if ($info->code != 0) {
                $output->writeln('code:' . $info->code . ',msg:' . $info->message);
                continue;
            }
            if (!$info->data) {
                save_log('apidata/getBillList', "proxyId:{$proxy},handlemsg:nodata");
                $output->writeln('code:' . $info->code . ',msg:' . $info->message . 'data:nodata');
                continue;
            }

            $teamlevelModel  = new Teamlevel();
            $insertThirdData = $insertIncomeData = $insertOrderData = $levelData = [];
            //获取自身分成
            $selfPercent  = $proxyModel->getValue(['code' => $proxy], 'percent');
            $levelData[0] = [
                'proxy_id'  => $proxy,
                'parent_id' => '',
                'level'     => 0,
                'percent'   => $selfPercent
            ];
            //分成级别
            $teamlevels = $teamlevelModel->getListAll(['proxy_id' => $proxy]);
            if ($teamlevels) {
                foreach ($teamlevels as $l) {
                    $percent                = $proxyModel->getValue(['code' => $l['parent_id']], 'percent');
                    $levelData[$l['level']] = [
                        'proxy_id'  => $proxy,
                        'parent_id' => $l['parent_id'],
                        'level'     => $l['level'],
                        'percent'   => $percent
                    ];
                }
            }


            $insertTime            = date('YmdHis');
            $timestamp             = time();
            $thirdPlayerOrderModel = new ThirdPlayerOrder();
            $incomelogModel        = new Incomelog();
            $playerorderModel      = new Playerorder();

            //计算总税收。用于更新账户余额和历史金额
            $allTax = 0;

            foreach ($info->data as $data) {
                $allTax            += $data->tax;
                $insertThirdData[] = [
                    'userid'     => $data->userid,
                    'game'       => '',
                    'tax'        => $data->tax,
                    'inserttime' => $insertTime,
                    'time'      => $data->date,
                    'createday' => $today
                ];
                $insertOrderData = [
                    'proxy_id' => $proxy,
                    'userid'   => $data->userid,
                    'game'     => '',
                    'total_tax' => change_to_yuan($data->tax, 4),
                    'time'      => $data->date,
                    'createday' => $today,
                    'createtime' => $insertTime,
                ];
                foreach ($levelData as $level => $lv) {
                    $totalTax = change_to_yuan($data->tax, 4);
                    if ($level == 0) { //当前运营商
                        $insertIncomeData[] = [
                            'proxy_id'   => $lv['proxy_id'],
                            'totaltax'   => $totalTax,
                            'changmoney' => change_to_yuan($data->tax * $lv['percent'] / 100, 4),
                            'time'       => $data->date,
                            'createtime' => $timestamp,
                            'createday'  => $today,
                            'descript'   => $proxy . '代理的玩家的税收分成，总金额' . change_to_yuan($data->tax * $lv['percent'] / 100, 4)
                        ];
                    } else { //1级代理或2级代理
                        $getPercent = intval($lv['percent'] - $levelData[$level - 1]['percent']);
                        if ($getPercent > 0) {
                            $insertIncomeData[] = [
                                'proxy_id'   => $lv['proxy_id'],
                                'totaltax'   => $totalTax,
                                'changmoney' => change_to_yuan($data->tax * $getPercent / 100, 4),
                                'time'       => $data->date,
                                'createtime' => $timestamp,
                                'createday'  => $today,
                                'descript'   => $proxy . '给' . $level . '级代理税收分成，总金额' . change_to_yuan($data->tax * $getPercent / 100, 4)
                            ];
                        }
                    }
                }
            }

            if ($insertIncomeData || $insertThirdData) {
                Db::startTrans();
                try {
                    //插入第三方表数据
                    if ($insertThirdData) {
                        $thirdPlayerOrderModel->addAll($insertThirdData);
                    }
                    //插入税收表记录
                    if ($insertOrderData) {
                        $playerorderModel->addAll($insertOrderData);
                    }
                    //插入税收分成表数据
                    if ($insertIncomeData) {
                        $incomelogModel->addAll($insertIncomeData);
                    }
                    //计算新增金额
                    foreach ($levelData as $k => $v) {
                        if ($k == 0) { //当前代理
                            $proxyModel->updateByWhere(
                                ['code' => $v['proxy_id']],
                                [
                                    'balance'   => Db::raw('balance+' . change_to_yuan($allTax * $v['percent'] / 100, 2)),
                                    'historyin' => Db::raw('historyin+' . change_to_yuan($allTax * $v['percent'] / 100, 2)),
                                ]
                            );
                            save_log('apidata/getBillList', "proxyId:{$v['proxy_id']},addmoney:" . change_to_yuan($allTax * $v['percent'] / 100, 2) . ".");
                        } else { //上级代理
                            $getPercent = intval($v['percent'] - $levelData[$k - 1]['percent']);
                            $proxyModel->updateByWhere(
                                ['code' => $v['proxy_id']],
                                [
                                    'balance'   => Db::raw('balance+' . change_to_yuan($allTax * $getPercent / 100, 2)),
                                    'historyin' => Db::raw('historyin+' . change_to_yuan($allTax * $getPercent / 100, 2)),
                                ]
                            );
                            save_log('apidata/getBillList', "proxyId:{$v['proxy_id']},addmoney:" . change_to_yuan($allTax * $v['percent'] / 100, 2) . ".");
                        }
                    }

                    Db::commit();
                    save_log('apidata/getBillList', "proxyId:{$proxy},recordnum:" . count($info->data) . ",handlemsg:insertsuccess");
                } catch (\Exception $e) {
                    Db::rollback();
                    save_log('apidata/getBillList', "proxyId:{$proxy},handlemsg:{$e->getMessage()}");
                    $output->writeln('code:500,msg:' . $e->getMessage() . 'data:insertfail');
                }
            }
        }
        save_log('apidata/getPlayerList', "end at:" . date('Y-m-d H:i:s'));
    }

}
