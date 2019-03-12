<?php
namespace app\index\controller;
use app\index\model\Incomelog;
use app\index\model\Paytime;
use app\index\model\Player;
use app\index\model\Playerorder;
use app\index\model\Proxy;
use app\index\model\Teamlevel;
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

    //总统计和昨日统计
    public function getStatistics()
    {
        $data = [
            'code' => 0,
            'msg' => ''
        ];
        $data['proxynum'] = $this->getProxyNum();
        $data['playernum'] = $this->getPlayerNum();
        $data['totalfee']= $this->getTotalFee();
        $data['totaltax'] = $this->getTotalTax();
        $data['totalin'] = $this->getTotalIn();
        $data['yesterdayfee'] = $this->getYesterdayFee();
        $data['yesterdayin'] = $this->getYesterdayIn();
        $data['yesterdaytax'] = $this->getYesterdayTax();
        return json($data);
    }

    //数据统计
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

    //统计下级代理个数
    private function getProxyNum()
    {
        $teamlevelModel = new Teamlevel();
        $count = $teamlevelModel->getCount(['parent_id' => session('code')]);
        return $count;
    }
    //统计注册玩家个数
    private function getPlayerNum()
    {
        $playerModel = new Player();
        $count = $playerModel->getCount(['proxy_id' => session('code')]);
        return $count;
    }

    //统计总充值
    private function getTotalFee()
    {
        $paytimeModel = new Paytime();
        $info = intval($paytimeModel->getValue(['proxy_id' => session('code')], 'sum(totalfee) totalfee'));
        return $info;
    }
    
    //统计总业绩
    private function getTotalTax()
    {
        $playerorderModel = new Playerorder();
        $info = intval($playerorderModel->getValue(['proxy_id' => session('code')], 'sum(total_tax) total_tax'));
        return $info;
    }

    //获取总利润
    private function getTotalIn()
    {
        $proxyModel = new Proxy();
        $info = $proxyModel->getValue(['id' => session('id')], 'historyin');
        return $info;
    }

    //统计昨日充值
    private function getYesterdayFee()
    {
        $paytimeModel = new Paytime();
        $day  = date('Y-m-d', strtotime('-1 day'));
        $where = [
            ['proxy_id', '=', session('code')],
            ['addtime', 'like', $day.'%']
        ];
        $info = intval($paytimeModel->getValue($where, 'sum(totalfee) totalfee'));
        return $info;
    }

    //统计昨日业绩
    private function getYesterdayTax()
    {
        $playerorderModel = new Playerorder();
        $day  = date('Y-m-d', strtotime('-1 day'));
        $where = [
            ['proxy_id', '=', session('code')],
            ['createtime', 'like', $day.'%']
        ];
        $info = intval($playerorderModel->getValue($where, 'sum(total_tax) total_tax'));
        return $info;
    }

    //统计昨日利润
    private function getYesterdayIn()
    {
        $incomelogModel = new Incomelog();
        $day  = date('Ymd', strtotime('-1 day'));
        $where = ['proxy_id' => session('id'), 'createday' => $day];
        $info = intval($incomelogModel->getValue($where, 'sum(changmoney) changmoney'));
        return $info;
    }
}
