<?php

namespace app\index\controller;

use sms\Sms;
use think\Controller;
use think\Request;
use app\index\model\Proxy;
class Sendmsg extends Controller
{

    protected $middleware = ['Auth'];
    //给绑定手机发送验证码
    public function index()
    {
        $data = ['code' => 0, 'msg' => ''];
        $proxyModel = new Proxy();
        $userInfo = $proxyModel->getRowById(session('id'), 'bind_mobile');
        if (!$userInfo['bind_mobile']) {
            $data['code'] = 1;
            $data['msg'] = config('msg.bind_mobile');
            return json($data);
        }
        $res = Sms::send_sms($userInfo['bind_mobile']);
        if ($res->code != 0) {
            $data['code'] = 2;
            $data['msg'] = config('msg.sms_fail');
        } else {
            $data['msg'] = config('msg.sms_success');
        }

        return json($data);
    }

    //修改新密保手机发送验证码
    public function index2()
    {
        $result = $this->validate($this->request->post(), 'app\index\validate\SendmsgMobile');
        $data = ['code' => 0, 'msg' => ''];
        if (true !== $result) {
            $data['code'] = 1;
            $data['msg'] = $result;
            return json($data);
        }
        $mobile = $this->request->mobile;
        $res = Sms::send_sms($mobile);
        if ($res->code != 0) {
            $data['code'] = 2;
            $data['msg'] = config('msg.sms_fail');
        } else {
            $data['msg'] = config('msg.sms_success');
        }

        return json($data);
    }

}
