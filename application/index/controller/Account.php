<?php
/**
 * 账号
 * User: Administrator
 * Date: 2019/3/7
 * Time: 15:00
 */
namespace app\index\controller;
use think\Controller;
class Account extends Controller
{
    //玩家列表
    public function playerList()
    {
        return view('playerList');
    }

    public function playerListData()
    {
        $data = [
            'code' => 0,
            'msg' => '',
            'count' => 50,
            'data' => [
                [
                    'id' => 1,
                    'account' => '11111111111',
                    'room' => 1,
                    'recharge' => 123456,
                    'achieve' => 6000,
                    'money' => '8008208820',
                    'comment' => 1
                ],
                [
                    'id' => 2,
                    'account' => '11111111112',
                    'room' => 2,
                    'recharge' => 123456,
                    'achieve' => 2000,
                    'money' => '8008208821',
                    'comment' => 'test'
                ]
            ]
        ];
        return json($data);
    }
    
    //按名称搜索玩家
    public function searchPlayer()
    {
        
    }

    //代理列表
    public function agentList()
    {
        return view('agentList');
    }

    public function agentListData()
    {
        $data = [
            'code' => 0,
            'msg' => '',
            'count' => 50,
            'data' => [
                [
                    'id' => 1,
                    'account' => '11111111111',
                    'rate' => 60,
                    'recharge' => 123456,
                    'achieve' => 6000,
                    'profit' => 3333,
                    'money' => '8008208820',
                    'comment' => 1
                ],
                [
                    'id' => 2,
                    'account' => '11111111112',
                    'rate' => 70,
                    'recharge' => 1234356,
                    'achieve' => 60300,
                    'profit' => 33333,
                    'money' => '80082088210',
                    'comment' => 1111
                ]
            ]
        ];
        return json($data);
    }

    //按名称搜索代理
    public function searchAgent()
    {

    }

    public function addAgent()
    {
        return view('addAgent');
    }

}