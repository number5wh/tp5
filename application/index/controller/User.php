<?php
/**
 * 用户信息
 * User: Administrator
 * Date: 2019/3/7
 * Time: 15:00
 */
namespace app\index\controller;
use think\Controller;
class User extends Controller
{
    protected $middleware = ['Auth'];

    //用户列表
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
                    'userid' => '8008208820',
                    'role' => 1
                ],
                [
                    'id' => 2,
                    'account' => '11111111112',
                    'userid' => '8008208821',
                    'role' => 1
                ]
            ]
        ];
        return json($data);
    }
}