<?php
/**
 * 用户管理
 * User: Administrator
 * Date: 2019/3/7
 * Time: 15:00
 */

namespace app\admin\controller;

use apiData\PlayerData;
use app\model\Playerorder;
use app\model\Proxy;
use app\model\Sysadmin;
use qrCode\Code;
use think\Controller;
use app\model\Player;
use app\model\Teamlevel;
use app\model\Paytime;
use think\Db;

class User extends Controller
{
    protected $middleware = ['AdminAuth'];

    public function getList()
    {
        return view('getList');
    }

    public function getListData()
    {
        $data           = ['code'  => 0, 'msg'   => '', 'count' => 0, 'data'  => []];
        $username       = isset($this->request->username) ? strval($this->request->username) : '';
        $page           = (isset($this->request->page) && intval($this->request->page) > 0) ? intval($this->request->page) : 1;
        $limit          = (isset($this->request->limit) && intval($this->request->limit) > 0) ? intval($this->request->limit) : 10;

        $where          = [];
        if ($username) {
            $where[] = ['username', 'like', "%$username%"];
        }
        $sysadminModel = new Sysadmin();
        //获取总数
        $count         = $sysadminModel->getCount($where);
        $data['count'] = $count;
        if (!$count) {
            return json($data);
        }
        $list = $sysadminModel->getList($where, $page, $limit, 'username, roleid, createtime');
        foreach ($list as &$v) {
            $v['role'] = config('admin.role')[$v['roleid']];
        }
        unset($v);
        $data['data'] = $list;
        return $data;
    }

    public function add()
    {
        return view('add');
    }

    public function doAdd()
    {
        //判断参数
        $data   = ['code' => 0, 'msg'  => config('msg.add_user_0')];
        $result = $this->validate($this->request->post(), 'app\admin\validate\AddUser');
        if (true !== $result) {
            $data['code'] = 1;
            $data['msg']  = $result;
            return json($data);
        }

        $username      = $this->request->username;
        $password      = $this->request->password;
        $role          = intval($this->request->role);
        $sysadminModel = new Sysadmin();
        //检查账号名
        if ($sysadminModel->getRow(['username' => $username])) {
            $data['code'] = 2;
            $data['msg']  = config('msg.add_user_1');
            return json($data);
        }

        //生成盐
        $salt = random_str(6);
        //生成密码
        $pwd = md5($salt . $password);
        $insertData = [

            'username'       => $username,
            'password'       => $pwd,
            'salt'           => $salt,
            'addid'          => session('adminid'),
            'roleid'          => $role,
            'createtime'   => date("YmdHis"),
            'updatetime'       => time()
        ];
        $res = $sysadminModel->add($insertData);
        if (!$res) {
            $data['code'] = 5;
            $data['msg']  = config('msg.add_user_2');
            save_log('admin/adduser', "status:0,username:{$username},addid:" . session('adminid'));
            return json($data);
        }
        save_log('admin/adduser', "status:1,username:{$username},addid:" . session('adminid'));
        return json($data);
    }

}