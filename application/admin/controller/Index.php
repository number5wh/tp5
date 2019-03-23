<?php
namespace app\admin\controller;

use think\Controller;
class Index extends Controller
{
    protected $middleware = ['AdminAuth'];
    //模板
    public function layout()
    {
        $this->assign('roleid', session('roleid'));
        $this->assign('username', session('adminname'));
        return view('/layout');
    }
}
