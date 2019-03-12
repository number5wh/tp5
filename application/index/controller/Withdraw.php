<?php
/**
 * 提现管理
 * User: Administrator
 * Date: 2019/3/7
 * Time: 11:51
 */

namespace app\index\controller;

use app\index\model\Proxy;
use sms\Sms;
use think\Controller;
use app\index\model\Bankinfo;
use app\index\model\Checklog;

class Withdraw extends Controller
{
    protected $middleware = ['Auth'];

    //获取提现记录
    public function getList()
    {
        return view('list');
    }

    public function getListData()
    {
        $data  = [
            'code'  => 0,
            'msg'   => '',
            'count' => 0,
            'data'  => [],
        ];
        $page  = (isset($this->request->page) && intval($this->request->page) > 0) ? intval($this->request->page) : 1;
        $limit = (isset($this->request->limit) && intval($this->request->limit) > 0) ? intval($this->request->limit) : 10;

        $statusArr     = config('config.checklog_status');
        $checklogModel = new Checklog();
        //获取总数
        $count         = $checklogModel->getCount(['proxy_id' => session('code')]);
        $data['count'] = $count;
        if ($count == 0) {
            return json($data);
        }
        //获取记录
        $res = $checklogModel->getList(['proxy_id' => session('code')], $page, $limit);
        //checktype 1 支付宝  2 银行卡
        foreach ($res as &$v) {
            $v['statusInf']     = in_array($v['status'], $statusArr) ? $statusArr[$v['status']] : config('config.checklog_status_other');
            $v['getname']       = $v['checktype'] == 1 ? $v['alipay_name'] : $v['name'];
            $v['checktypeName'] = config('config.check_type')[$v['checktype']];
            $v['account']       = $v['checktype'] == 1 ? $v['alipay'] : $v['cardaccount'];
        }
        unset($v);
        $data['data'] = $res;
        return json($data);
    }

    //获取总额
    public function getAmount()
    {
        $checklogModel = new Checklog();
        $amount        = $checklogModel->getRow(['proxy_id' => session('code')], 'sum(amount) amount');
        $data          = [
            'code'   => 0,
            'amount' => $amount['amount']
        ];
        return json($data);
    }

    //提现申请
    public function apply()
    {
        $bankInfoModel = new Bankinfo();
        $info          = $bankInfoModel->getRow(['proxy_id' => session('code')]);
        //获取可提现金额
        $proxyModel = new Proxy();
        $user       = $proxyModel->getRow(['id' => session('id')], 'balance');
        $this->assign('info', $info);
        $this->assign('balance', $user['balance']);
        return view('apply');
    }

    //处理提现
    public function doApply()
    {
        $result = $this->validate($this->request->post(), 'app\index\validate\DoWithdraw');
        $data   = [
            'code' => 0,
            'msg'  => config('msg.withdraw_0')
        ];
        if (true !== $result) {
            $data['code'] = 1;
            $data['msg']  = $result;
            return json($data);
        }
        $checktype = $this->request->checktype;
        $amount    = round($this->request->amount, 2);
        $code      = $this->request->code;


        //获取用户信息
        $proxyModel = new Proxy();
        $userInfo   = $proxyModel->getRow(['id' => session('id')]);
        //绑定手机判断
        if (!$userInfo['bind_mobile']) {
            $data['code'] = 2;
            $data['msg']  = config('msg.bind_mobile');
            return json($data);
        }
        //验证码验证
        $check = Sms::validateSms($userInfo['bind_mobile'], $code);
        if ($check->code != 0) {
            $data['code'] = 3;
            $data['msg']  = config('msg.wrong_code');
            return json($data);
        }

        //判断提现账户是否存在
        $bankInfoModel = new Bankinfo();
        $info          = $bankInfoModel->getRow(['proxy_id' => session('code')]);
        $account       = '';
        $account       = $checktype == 1 ? $info['alipay'] : $info['cardaccount'];
        if (!$account) {
            $data['code'] = 4;
            $data['msg']  = config('msg.no_account');
            return json($data);
        }

        //判断剩余金额
        if ($userInfo['balance'] < $amount) {
            $data['code'] = 5;
            $data['msg']  = config('msg.enough_money');
            return json($data);
        }

        //开始处理提现申请
        //先扣除总额
        $leftMoney = $userInfo['balance'] - $amount;
        $res       = $proxyModel->updateById(
            session('id'),
            ['balance' => $leftMoney]
        );
        if (!$res) {
            $data['code'] = 6;
            $data['msg']  = config('msg.withdraw_1');
            save_log('withdraw/apply', "status:0,returncode:1,username:{$userInfo['username']},amount:{$amount},type:{$checktype},account:{$account}");
            return json($data);
        }
        //再添加到提现表
        $checklogModel = new Checklog();
        $orderid       = random_orderid();
        $addData       = [
            'orderid'    => $orderid,
            'proxy_id'   => session('code'),
            'amount'     => $amount,//提现金额
            'balance'    => $leftMoney,//剩余金额
            'checktype'  => $checktype,
            'descript'   => $userInfo['nickname'] . ',' . $userInfo['code'] . '于' . date('Y-m-d H:i:s') . '提现金额' . $amount . '元',
            'status'     => 0,
            'createtime' => time(),
            'addtime'    => date('Y-m-d H:i:s')
        ];
        if ($checktype == 1) {
            $addData['alipay']      = $info['alipay'];
            $addData['alipay_name'] = $info['alipay_name'];
        } else {
            $addData['name']        = $info['name'];
            $addData['bank']        = $info['bank'];
            $addData['cardaccount'] = $info['cardaccount'];
        }
        $res2 = $checklogModel->add($addData);
        if (!$res2) {
            //总金额还原
            $proxyModel->updateById(
                session('id'),
                ['balance' => $userInfo['balance']]
            );
            $data['code'] = 7;
            $data['msg']  = config('msg.withdraw_1');
            save_log('withdraw/apply', "status:0,returncode:2,username:{$userInfo['username']},amount:{$amount},type:{$checktype},account:{$account}");
            return json($data);
        }

        save_log('withdraw/apply', "status:1,returncode:0,username:{$userInfo['username']},amount:{$amount},type:{$checktype},account:{$account}");
        $data['leftmoney'] = $leftMoney;
        return json($data);
    }

    //结算账号
    public function set()
    {
        $bankInfoModel = new Bankinfo();
        $info          = $bankInfoModel->getRow(['proxy_id' => session('code')]);
        $this->assign('info', $info);
        return view('set');
    }

    //修改新增支付宝账号
    public function doSetAlipay()
    {
        $result = $this->validate($this->request->post(), 'app\index\validate\ChangeAlipay');
        $data   = [
            'code' => 0,
            'msg'  => config('msg.update_success')
        ];
        if (true !== $result) {
            $data['code'] = 1;
            $data['msg']  = $result;
            return json($data);
        }
        $alipay     = $this->request->alipay;
        $alipayName = $this->request->alipay_name;

        $bankInfoModel = new Bankinfo();
        $info          = $bankInfoModel->getRow(['proxy_id' => session('code')]);
        //判断是否有改动
        if ($info && $info['alipay'] == $alipay && $info['alipay_name'] == $alipayName) {
            $data['code'] = 2;
            $data['msg']  = config('msg.nochange');
            return json($data);
        }

        if (!$info) {
            $res = $bankInfoModel->add(['proxy_id' => session('code'), 'alipay' => $alipay, 'alipay_name' => $alipayName]);
        } else {
            $res = $bankInfoModel->updateByWhere(['proxy_id' => session('code')], ['alipay' => $alipay, 'alipay_name' => $alipayName]);
        }

        if (!$res) {
            $data['code'] = 3;
            $data['msg']  = config('msg.update_fail');
        }
        return json($data);
    }

    //修改新增银行账号
    public function doSetBank()
    {
        $result = $this->validate($this->request->post(), 'app\index\validate\ChangeBank');
        $data   = [
            'code' => 0,
            'msg'  => config('msg.update_success')
        ];
        if (true !== $result) {
            $data['code'] = 1;
            $data['msg']  = $result;
            return json($data);
        }
        $name        = $this->request->name;
        $bank        = $this->request->bank;
        $cardaccount = $this->request->cardaccount;

        $bankInfoModel = new Bankinfo();
        $info          = $bankInfoModel->getRow(['proxy_id' => session('code')]);
        //判断是否有改动
        if ($info && $info['name'] == $name && $info['bank'] == $bank && $info['cardaccount'] == $cardaccount) {
            $data['code'] = 2;
            $data['msg']  = config('msg.nochange');
            return json($data);
        }

        if (!$info) {
            $res = $bankInfoModel->add(['proxy_id' => session('code'), 'name' => $name, 'bank' => $bank, 'cardaccount' => $cardaccount]);
        } else {
            $res = $bankInfoModel->updateByWhere(['proxy_id' => session('code')], ['name' => $name, 'bank' => $bank, 'cardaccount' => $cardaccount]);
        }

        if (!$res) {
            $data['code'] = 3;
            $data['msg']  = config('msg.update_fail');
        }
        return json($data);
    }
}