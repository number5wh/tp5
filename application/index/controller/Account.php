<?php
/**
 * 账号管理
 * User: Administrator
 * Date: 2019/3/7
 * Time: 15:00
 */
namespace app\index\controller;
use app\index\model\Playerorder;
use app\index\model\Proxy;
use think\Controller;
use app\index\model\Player;
use app\index\model\Teamlevel;
use app\index\model\Paytime;
class Account extends Controller
{
    //玩家列表
    public function playerList()
    {
        return view('playerList');
    }

    /**
     * @todo 玩家的房间等信息还没有
     * @return \think\response\Json
     */
    public function playerListData()
    {

        $data = [
            'code' => 0,
            'msg' => '',
            'count' => 0,
            'data' => []
        ];

        $page  = (isset($this->request->page) && intval($this->request->page) > 0) ? intval($this->request->page) : 1;
        $limit = (isset($this->request->limit) && intval($this->request->limit) > 0) ? intval($this->request->limit) : 10;
        $playerModel = new Player();
        //总数
        $where = ['proxy_id' => session('code')];
        $data['count']  = $playerModel->getCount($where);
        if ($data['count'] <=0) {
            return json($data);
        }
        //获取所属玩家
        $playerList = $playerModel->getList($where, $page, $limit);
        if ($playerList) {
            //获取总充值和总业绩数据
            $this->handlePlayer($playerList);
        }
        $data['data'] = $playerList;
        return json($data);
    }
    
    //按名称搜索玩家
    public function searchPlayer()
    {
        $data = [
            'code' => 0,
            'msg' => '',
            'count' => 0,
            'data' => []
        ];
        $userId = strval($this->request->userid);
        $page  = (isset($this->request->page) && intval($this->request->page) > 0) ? intval($this->request->page) : 1;
        $limit = (isset($this->request->limit) && intval($this->request->limit) > 0) ? intval($this->request->limit) : 10;
        $playerModel = new Player();
        $where = [['proxy_id' ,'=', session('code')]];
        if ($userId) {
            $where[] = ['userid', 'like', "%$userId%"];
        }
        $data['count']  = $playerModel->getCount($where);
        if ($data['count'] <=0) {
            return json($data);
        }
        $playerList = $playerModel->getList($where, $page, $limit);
        if ($playerList) {
            //获取总充值和总业绩数据
            $this->handlePlayer($playerList);
        }
        $data['data'] = $playerList;
        return json($data);
    }

    //获取玩家额外数据（总充值，总业绩）
    private function handlePlayer(&$playerList)
    {
        $userId = array_column($playerList, 'userid');
        //查询总充值
        $paytimeModel = new Paytime();
        $totalFee = $paytimeModel->getListAll(
            ['userid' => $userId],
            'sum(`totalfee`) as totalfee, userid',
            [],
            'userid'
        );
        //查询总业绩
        $playerorderModel = new Playerorder();
        $totalTax = $playerorderModel->getListAll(
            ['userid' => $userId],
            'sum(`total_tax`) as total_tax, userid',
            [],
            'userid'
        );
        //处理数据
        foreach ($playerList as &$player) {
            $player['total_tax'] = $player['totalfee'] = 0;
            foreach ($totalFee as $fee) {
                $player['totalfee'] = 0;
                if ($fee['userid'] == $player['userid']) {
                    $player['totalfee'] = $fee['totalfee'];
                    break;
                }
            }
            foreach ($totalTax as $tax) {
                $player['total_tax'] = 0;
                if ($tax['userid'] == $player['userid']) {
                    $player['total_tax'] = $tax['total_tax'];
                    break;
                }
            }
        }
        unset($player);
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
            'count' => 0,
            'data' => []
        ];
        $page  = (isset($this->request->page) && intval($this->request->page) > 0) ? intval($this->request->page) : 1;
        $limit = (isset($this->request->limit) && intval($this->request->limit) > 0) ? intval($this->request->limit) : 10;
        $teamlevelModel = new Teamlevel();
        //获取总数
        $count = $teamlevelModel->getCount(['parent_id' => session('code')]);
        $data['count'] = $count;
        if (!$count) {
            return json($data);
        }

        //获取下级代理列表
        $proxyList = $teamlevelModel->getList(['parent_id' => session('code')], $page, $limit);
        if ($proxyList) {
            $this->handleAgent($proxyList);
        }
        $data['data'] = $proxyList;
        return json($data);
    }

    //按名称搜索代理
    public function searchAgent()
    {
        //检查参数
        $data   = [
            'code' => 0,
            'msg'  => '',
            'data' => []
        ];
        $username = strval($this->request->username);
        $page  = (isset($this->request->page) && intval($this->request->page) > 0) ? intval($this->request->page) : 1;
        $limit = (isset($this->request->limit) && intval($this->request->limit) > 0) ? intval($this->request->limit) : 10;
        $teamlevelModel = new Teamlevel();
        $where = [['parent_id' ,'=', session('code')]];
        if ($username) {
            $where[] = ['username','like',"%$username%"];
        }
        //获取总数
        $count = $teamlevelModel->getCount($where);
        $data['count'] = $count;
        if (!$count) {
            return json($data);
        }
        $proxyList = $teamlevelModel->getList($where, $page, $limit);
        if ($proxyList) {
            $this->handleAgent($proxyList);
        }
        $data['data'] = $proxyList;
        return json($data);
    }

    //获取代理额外数据
    private function handleAgent(&$proxyList) {
        $proxyListId = array_column($proxyList, 'proxy_id');
        //查询总充值
        $paytimeModel = new Paytime();
        $totalFee = $paytimeModel->getListAll(
            ['proxy_id' => $proxyListId],
            'sum(`totalfee`) as totalfee, proxy_id',
            [],
            'proxy_id'
        );
        //查询总业绩
        $playerorderModel = new Playerorder();
        $totalTax = $playerorderModel->getListAll(
            ['proxy_id' => $proxyListId],
            'sum(`total_tax`) as total_tax, proxy_id',
            [],
            'proxy_id'
        );
        //查询总利润
        $proxyModel = new Proxy();
        $totalIn = $proxyModel->getListAll(
            ['code' => $proxyListId],
            'historyin, code as proxy_id, username'
        );
        //查询玩家余额
        $playerModel = new Player();
        $totalLeft = $playerModel->getListAll(
            ['proxy_id' => $proxyListId],
            'sum(`leftmoney`) as leftmoney, proxy_id',
            [],
            'proxy_id'
        );
        //处理数据
        foreach ($proxyList as &$proxy) {
            $proxy['totalfee'] = $proxy['total_tax'] = $proxy['historyin'] = $proxy['leftmoney'] = 0;
            foreach ($totalFee as $fee) {
                $proxy['totalfee'] = 0;
                if ($fee['proxy_id'] == $proxy['proxy_id']) {
                    $proxy['totalfee'] = $fee['totalfee'];
                    break;
                }
            }
            foreach ($totalTax as $tax) {
                $proxy['total_tax'] = 0;
                if ($tax['proxy_id'] == $proxy['proxy_id']) {
                    $proxy['total_tax'] = $tax['total_tax'];
                    break;
                }
            }
            foreach ($totalIn as $in) {
                $proxy['historyin'] = 0;
                if ($in['proxy_id'] == $proxy['proxy_id']) {
                    $proxy['historyin'] = $in['historyin'];
                    $proxy['username']  = $in['username'];
                    break;
                }
            }
            foreach ($totalLeft as $left) {
                $proxy['leftmoney'] = 0;
                if ($left['proxy_id'] == $proxy['proxy_id']) {
                    $proxy['leftmoney'] = $left['leftmoney'];
                    break;
                }
            }
        }
        unset($proxy);
    }

    //新增代理
    public function addAgent()
    {
        return view('addAgent');
    }

}