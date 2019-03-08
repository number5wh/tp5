<?php

namespace app\index\controller;

use think\Controller;
use think\facade\Session;
use app\index\model\Proxy;

class Login extends Controller
{
    public function login()
    {
        $this->checkLong();
        if (session('?username')) {
            return redirect(url('/'));
        } else {
            $username = '';
            if (isset($_COOKIE['username'])){
                $username = $_COOKIE['username'];
            }
            $this->assign('username',$username);
            return view('login');
        }
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

        //存入session
        session('username', $res['username']);
        session('id', $res['id']);


        //盐
        $salt = random_str(16);
        //第二分身标识
        $identifier = md5($salt . $res['username'] . $salt);
        //永久登录标识
        $token = md5(uniqid(rand(), true));
        //永久登录超时时间(1周)
        $expire = config('config.cookie_expire');
        $timeout = time()+$expire;
        //存入cookie
        cookie('auth',"$identifier:$token",$expire);
        $proxyModel->updateByWhere(
            ['id' => $res['id']],
            [
                'last_login' => date('Y-m-d H:i:s'),
                'last_ip' => get_client_ip(),
                'token' => $token,
                'identifier' => $identifier,
                'timeout' => $timeout
            ]
        );

        cookie('username',$res['username'],$expire);

        $data['code'] = 0;
        $data['msg'] = '登录成功';
        $data['data'] = $res['username'];
        return json($data);
    }

    public function logout()
    {
        session(null);
        cookie('username', null);
        cookie('auth', null);
        return redirect(url('login'));
    }

    //是否记住我
    public function checkLong(){
        $isLong = $this->checkRemember();
        if($isLong === false){

        }else{
            session("username",$isLong['username']);
            session("id",$isLong['id']);
        }
    }

    //验证用户是否永久登录（记住我）
    public function checkRemember(){
        $arr = array();
        $now = time();

        $auth = cookie('auth');
        if (!$auth) {
            return false;
        }
        list($identifier,$token) = explode(':',$auth);

        if (ctype_alnum($identifier) && ctype_alnum($token)){
            $arr['identifier'] = $identifier;
            $arr['token'] = $token;
        }else{
            return false;
        }

        $proxyModel = new Proxy();
        $info = $proxyModel->getInfoByIndentifier($arr['identifier']);
        if($info != null){
            if($arr['token'] != $info['token']){
                return false;
            }else if($now > $info['timeout']){
                return false;
            }else{
                return $info;
            }
        }else{
            return false;
        }
    }
}
