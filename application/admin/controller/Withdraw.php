<?php
/**
 * 提现管理
 * User: Administrator
 * Date: 2019/3/7
 * Time: 11:51
 */

namespace app\admin\controller;

use app\model\Proxy;
use apiData\Sms;
use think\Controller;
use app\model\Bankinfo;
use app\model\Checklog;
use think\Db;

class Withdraw extends Controller
{
    protected $middleware = ['AdminAuth'];

    //获取提现记录
    public function getList()
    {
        return view('list');
    }

    public function getListData()
    {
        $data   = ['code' => 0, 'msg' => '', 'count' => 0, 'data' => []];
        $page   = (isset($this->request->page) && intval($this->request->page) > 0) ? intval($this->request->page) : 1;
        $limit  = (isset($this->request->limit) && intval($this->request->limit) > 0) ? intval($this->request->limit) : 10;
        $status = intval($this->request->status);
        $where  = [];
        if (in_array($status, array_keys(config('config.checklog_status')))) {
            $where['status'] = $status;
        }

        $statusArr     = config('config.checklog_status');
        $checklogModel = new Checklog();
        //获取总数
        $count         = $checklogModel->getCount($where);
        $data['count'] = $count;
        if ($count == 0) {
            return json($data);
        }
        //获取记录
        $res = $checklogModel->getList($where, $page, $limit, '*', ['createtime' => 'desc']);
        //checktype 1 支付宝  2 银行卡
        foreach ($res as $k =>&$v) {
            $v['statusInf']     = in_array($v['status'], array_keys($statusArr)) ? $statusArr[$v['status']] : config('config.checklog_status_other');
            $v['getname']       = $v['checktype'] == 1 ? $v['alipay_name'] : $v['name'];
            $v['checktypeName'] = config('config.check_type')[$v['checktype']];
            $v['account']       = $v['checktype'] == 1 ? $v['alipay'] : $v['cardaccount'];
        }
        unset($v);
        $data['data'] = $res;
        return json($data);
    }

    //处理提现
    public function doWithdraw()
    {
        $data   = ['code' => 0, 'msg' => config('msg.handle_withdraw_0'), 'statusinfo' => '', 'info' => ''];
        $result = $this->validate($this->request->post(), 'app\admin\validate\HandleWithdraw');
        if (true !== $result) {
            $data['code'] = 1;
            $data['msg']  = $result;
            return json($data);
        }

        $id            = intval($this->request->id);
        $status        = intval($this->request->status);
        $checklogModel = new Checklog();
        $info          = $checklogModel->getRowById($id);
        if (!$info) {
            $data['code'] = 2;
            $data['msg']  = config('msg.handle_withdraw_1');
            return json($data);
        }
        if ($info['status'] == $status && $status==1) {//重复设置
            $data['code'] = 3;
            $data['msg']  = config('msg.handle_withdrawall_111');
            return json($data);
        }
        if ($info['status'] == 2 || $info['status'] == 3) {//已拒绝或已完成
            $data['code'] = 3;
            $data['msg']  = config('msg.handle_withdraw_2');
            return json($data);
        }
        if ($info['status'] == 0 && $status == 3) {//未审批的直接设置已完成
            $data['code'] = 4;
            $data['msg']  = config('msg.handle_withdraw_3');
            return json($data);
        }
        if ($info['status'] == 1 && $status == 2) {
            $data['code'] = 5;
            $data['msg']  = config('msg.handle_withdraw_5');
            return json($data);
        }

        $statusArr     = config('config.checklog_status');
        Db::startTrans();
        try {
            $checklogModel->updateById($id, ['status' => $status, 'info' => $statusArr[$status], 'checktime' => date('YmdHis'), 'checkuser' => session('adminname')]);
            if ($status == 2) {//退款
                $proxyModel = new Proxy();
                $proxyModel->updateByWhere(['code' => $info['proxy_id']], ['balance' => Db::raw('balance+' . $info['amount'])]);
            }
            Db::commit();
            save_log('withdraw/handle', "success,status:$status,proxyid:{$info['proxy_id']},checkuser:" . session('adminname'));
            $data['statusinfo'] = $statusArr[$status];
            $data['info'] = $statusArr[$status];
            return json($data);
        } catch (\Exception $e) {
            Db::rollback();
            save_log('withdraw/handle', "fail,msg:{$e->getMessage()}status:$status,proxyid:{$info['proxy_id']},checkuser:" . session('adminname'));
            $data['code'] = 5;
            $data['msg']  = config('msg.handle_withdraw_4');
            return json($data);
        }
    }


    //批量处理提现
    public function doWithdrawAll()
    {
        $data   = ['code' => 0, 'msg' => config('msg.handle_withdrawall_0'), 'statusinfo' => '', 'info' => ''];
        $result = $this->validate($this->request->post(), 'app\admin\validate\HandleWithdrawAll');
        if (true !== $result) {
            $data['code'] = 1;
            $data['msg']  = $result;
            return json($data);
        }

        $idArr         = $this->request->idArr;

        $status        = intval($this->request->status);
        $checklogModel = new Checklog();
        //判断id数组合法性
        if (!$idArr) {
            $data['code'] = 2;
            $data['msg']  = config('msg.handle_withdrawall_1');
            return json($data);
        }
        foreach ($idArr as $id) {
            if (!is_numeric($id)) {
                $data['code'] = 2;
                $data['msg']  = config('msg.handle_withdrawall_1');
                return json($data);
            }
        }
        $list  = $checklogModel->getListAll(['id' => $idArr]);
        if (!$list || count($list) != count($idArr)) {
            $data['code'] = 2;
            $data['msg']  = config('msg.handle_withdrawall_1');
            return json($data);
        }

        //检查数据合法性
        foreach ($list as $v) {
            if ($v['status'] == $status && $status==1) {//重复设置
                $data['code'] = 3;
                $data['msg']  = config('msg.handle_withdrawall_111');
                return json($data);
            }
            if ($v['status'] == 2 || $v['status'] == 3) {//已拒绝或已完成
                $data['code'] = 3;
                $data['msg']  = config('msg.handle_withdraw_2');
                return json($data);
            }
            if ($v['status'] == 0 && $status == 3) {//未审批的直接设置已完成
                $data['code'] = 4;
                $data['msg']  = config('msg.handle_withdraw_3');
                return json($data);
            }
            if ($v['status'] == 1 && $status == 2) {
                $data['code'] = 5;
                $data['msg']  = config('msg.handle_withdraw_5');
                return json($data);
            }
        }


        $statusArr     = config('config.checklog_status');
        $proxyModel = new Proxy();

        //逐条处理
        Db::startTrans();
        foreach ($list as $info) {
            try {
                $checklogModel->updateById($info['id'], ['status' => $status, 'info' => $statusArr[$status], 'checktime' => date('YmdHis'), 'checkuser' => session('adminname')]);
                if ($status == 2) {//退款
                    $proxyModel->updateByWhere(['code' => $info['proxy_id']], ['balance' => Db::raw('balance+' . $info['amount'])]);
                }
                save_log('withdraw/handle', "success,status:$status,proxyid:{$info['proxy_id']},checkuser:" . session('adminname'));

            } catch (\Exception $e) {
                Db::rollback();
                save_log('withdraw/handle', "fail,msg:{$e->getMessage()}status:$status,proxyid:{$info['proxy_id']},checkuser:" . session('adminname'));
                $data['code'] = 5;
                $data['msg']  = config('msg.handle_withdrawall_10');
                return json($data);
            }
        }
        Db::commit();
        $data['msg']  = config('msg.handle_withdrawall_01');
        return json($data);
    }
}