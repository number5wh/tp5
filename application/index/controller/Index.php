<?php
namespace app\index\controller;
use think\facade\Config;
use think\Controller;
class Index extends Controller
{
    protected $middleware = ['Auth'];
    //模板
    public function layout()
    {
        $this->assign('username', session('username'));
        return view('/layout');
    }

    //主页
    public function home()
    {
        return view('home');
    }

    //利润统计
    public function profitStatistics()
    {
        $data = [
            'code' => 0,
            'msg' => '',
            'data' => [
                111,222,333,444,555,666,777
            ],
            'date' => [
                1,2,3,4,5,6,7
            ]
        ];
      return json($data);
    }
}
