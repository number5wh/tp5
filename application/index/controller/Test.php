<?php

namespace app\index\controller;

use think\Controller;
use think\Db;
use think\Request;
use app\index\model\Bankinfo;

class Test extends Controller
{

//    public function initialize()
//    {
//        echo 0;
//    }

//    public function __construct()
//    {
//        parent::__construct();
//        echo -1;
//    }
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        //
        $this->view('start/index.html');
    }

    /**
     * 显示创建资源表单页.
     *
     * @return \think\Response
     */
    public function create()
    {
        //
        $res = db('bankinfo')->where('id', 1)->find();
        $res2 = Bankinfo::all()->toArray();
        var_dump($res, $res2);die;

    }

    /**
     * 保存新建的资源
     *
     * @param  \think\Request  $request
     * @return \think\Response
     */
    public function save(Request $request)
    {
        //
    }

    /**
     * 显示指定的资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function read($id)
    {
        //
    }

    /**
     * 显示编辑资源表单页.
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * 保存更新的资源
     *
     * @param  \think\Request  $request
     * @param  int  $id
     * @return \think\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * 删除指定资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function delete($id)
    {
        //
    }
}
