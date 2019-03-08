<?php

namespace app\index\controller;

use think\Controller;
use think\facade\Session;
use app\index\model\Proxy;

class Login extends Controller
{
    public function login()
    {
        return view('login');
    }

    public function doLogin()
    {
        $result = $this->validate($this->request->post(), 'app\index\validate\Login');
        $data = [
            'code' => 0,
            'msg'  => '',
            'data' => []
        ];
        if (true !== $result) {
            $data['code'] = 1;
            $data['msg'] = $result;
            return json($data);
        }
        $username = trim($this->request->username);
        $password = trim($this->request->password);

        //数据验证
        $proxyModel = new Proxy();
        $res = $proxyModel->getInfoByUsername($username);
        if (!$res) {
            $data['code'] = 1;
            $data['msg'] = config('msg.wrong_username');
            return json($data);
        }
        if (md5($res['salt'].$password) != $res['password']) {
            $data['code'] = 2;
            $data['msg'] = config('msg.wrong_password');
            return json($data);
        }



        //验证
        session('user', $res['username']);
        session('code', $res['code']);
        $data['code'] = 0;
        $data['msg'] = '登录成功';
        $data['data'] = $res['username'];
        return json($data);
    }

    public function logout()
    {
        if (Session::has('user')) {
            Session::delete('user');
        }
        return redirect(url('login'));
    }
}
