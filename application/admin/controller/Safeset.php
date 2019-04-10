<?php
/**
 * 安全设置
 * User: Administrator
 * Date: 2019/3/8
 * Time: 10:26
 */

namespace app\admin\controller;

use app\model\Sysadmin;
use think\Controller;

class Safeset extends Controller
{
    protected $middleware = ['AdminAuth'];
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
        $result = $this->validate($this->request->post(), 'app\admin\validate\ChangePwd');
        if (true !== $result) {
            $data['code'] = 1;
            $data['msg']  = $result;
            return json($data);
        }

        $password = $this->request->password;
        //获取用户信息
        $sysadminModel = new Sysadmin();

        //生成盐
        $salt = random_str(6);
        $res  = [
            'password'    => md5($salt . $password),
            'salt'        => $salt,
            'updatetime' => time()
        ];
        $ret  = $sysadminModel->updateById(session('adminid'), $res);
        if (!$ret) {
            $data['code'] = 4;
            $data['msg']  = config('msg.update_fail');
            return json($data);
        }

        $data['msg'] = config('msg.update_success');
        return json($data);
    }
}