<?php

namespace app\index\controller;

use think\Controller;
use think\facade\Session;
use think\Request;

class Login extends Controller
{
    protected $middleware = [
        'Auth' => ['except' => ['login', 'doLogin', 'logout']]
    ];
    public function login()
    {
        $token = $this->request->token('__token__', 'sha1');
        $this->assign('token', $token);
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

        //验证
        session('user', $this->request->username);
        $data['code'] = 0;
        $data['msg'] = '登录成功';
        $data['data'] = $this->request->username;
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
