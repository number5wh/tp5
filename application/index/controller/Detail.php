<?php
/**
 * 明细查询
 * User: Administrator
 * Date: 2019/3/8
 * Time: 11:52
 */
namespace app\index\controller;
use think\Controller;
class Detail extends Controller
{
    public function index()
    {
        return view('index');
    }

    public function getData()
    {
        $data = [
            'code' => 0,
            'msg' => '',
            'count' => 50,
            'data' => [
                [
                    'id' => 1,
                    'account' => '11111111111',
                    'time'   => date('Y-m-d H:i:s'),
                    'room' => '1',
                    'win'  => 12345,
                    'comment' => 1
                ],
                [
                    'id' => 2,
                    'account' => '11111111112',
                    'time'   => date('Y-m-d H:i:s'),
                    'room' => '2',
                    'win' => -222,
                    'comment' => 1
                ]
            ]
        ];
        return json($data);
    }
}