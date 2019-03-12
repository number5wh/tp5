<?php
/**
 * 安全设置
 * User: Administrator
 * Date: 2019/3/8
 * Time: 10:26
 */

namespace app\index\controller;

use app\index\model\Proxy;
use sms\Sms;
use think\Controller;

class Safeset extends Controller
{
    protected $middleware = ['Auth'];
    public function index()
    {
        return view('index');
    }

    //修改密码
    public function changePwd()
    {
        $data = [
            'code' => 0,
            'msg'  => '',
            'data' => []
        ];
        $result = $this->validate($this->request->post(), 'app\index\validate\ChangePwd');
        if (true !== $result) {
            $data['code'] = 1;
            $data['msg']  = $result;
            return json($data);
        }

        $password = $this->request->password;
        $code     = $this->request->code;
        //获取用户信息
        $proxyModel = new Proxy();
        $userInfo   = $proxyModel->getRowById(session('id'), 'bind_mobile');

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

        //生成盐
        $salt = random_str(6);
        $res  = [
            'password'    => md5($salt . $password),
            'salt'        => $salt,
            'updatetime' => time()
        ];
        $ret  = $proxyModel->updateById(session('id'), $res);
        if (!$ret) {
            $data['code'] = 4;
            $data['msg']  = config('msg.update_fail');
            return json($data);
        }

        $data['msg'] = config('msg.update_success');
        return json($data);
    }

    //修改手机
    public function changeMobile()
    {
        $result = $this->validate($this->request->post(), 'app\index\validate\ChangeMobile');
        $data   = [
            'code' => 0,
            'msg'  => '',
            'data' => []
        ];
        if (true !== $result) {
            $data['code'] = 1;
            $data['msg']  = $result;
            return json($data);
        }
        $mobile = $this->request->mobile;
        $code   = $this->request->code;

        $proxyModel = new Proxy();
        //检查手机号是否一致
        $userInfo = $proxyModel->getRowById(session('id'), 'bind_mobile');
        if ($userInfo['bind_mobile'] && $userInfo['bind_mobile'] == $mobile) {
            $data['code'] = 2;
            $data['msg']  = config('msg.same_mobile');
            return json($data);
        }
        //验证码验证
        $check = Sms::validateSms($mobile, $code);
        if ($check->code != 0) {
            $data['code'] = 3;
            $data['msg']  = config('msg.wrong_code');
            return json($data);
        }

        $ret = $proxyModel->updateById(session('id'), ['bind_mobile' => $mobile, 'updatetime' => time()]);
        if (!$ret) {
            $data['code'] = 4;
            $data['msg']  = config('msg.update_fail');
            return json($data);
        }
        $data['msg'] = config('msg.update_success');
        return json($data);
    }
}