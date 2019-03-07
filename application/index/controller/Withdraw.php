<?php
/**
 * 提现管理
 * User: Administrator
 * Date: 2019/3/7
 * Time: 11:51
 */
namespace app\index\controller;
use think\Controller;
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
        $data = [
            'code' => 0,
            'msg' => '',
            'count' => 50,
            'data' => [
                [
                    'id' => 1,
                    'account' => '11111111111',
                    'name' => 'wwww',
                    'time' => date('Y-m-d H:i:s'),
                    'status' => 1,
                    'money' => 10000,
                    'comment' => '测试测试'
                ],
                [
                    'id' => 2,
                    'account' => '11111111112',
                    'name' => '老王',
                    'time' => date('Y-m-d H:i:s'),
                    'status' => 0,
                    'money' => 210000,
                    'comment' => '测试测试'
                ]
            ]
        ];
        return json($data);
    }

    //提现申请
    public function apply()
    {
        $token = $this->request->token('__token__', 'sha1');
        $this->assign('token', $token);
        return view('apply');
    }

    public function doApply()
    {

    }
    
    //结算账号
    public function settle()
    {
        $token = $this->request->token('__token__', 'sha1');
        $this->assign('token', $token);
        return view('settle');
    }
}