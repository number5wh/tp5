<?php
/**
 * 账号管理
 * User: Administrator
 * Date: 2019/3/7
 * Time: 15:00
 */

namespace app\index\controller;

use apiData\PlayerData;
use app\index\model\Playerorder;
use app\index\model\Proxy;
use think\Controller;
use app\index\model\Player;
use app\index\model\Teamlevel;
use app\index\model\Paytime;
use think\Db;

class Account extends Controller
{
    protected $middleware = ['Auth'];

    //玩家列表
    public function playerList()
    {
        return view('playerList');
    }

    /**
     * 获取在线玩家信息
     * @return \think\response\Json
     */
    public function playerListData()
    {
        $data = [
            'code'  => 0,
            'msg'   => '',
            'count' => 20,  //先设置20
            'data'  => []
        ];
        $onlineList = PlayerData::getOnlineList(session('code'));
        if ($onlineList->code != 0 || !$onlineList->data) {
            return json($data);
        }
        $playerList = [];
        $num = 1;
        foreach ($onlineList->data as $v) {
            $v->balance = change_to_yuan($v->balance);
            $v = json_decode(json_encode($v),true);
            $v['id'] = $num;
            $playerList[] = $v;
        }

        $userId      = isset($this->request->userid) ? strval($this->request->userid) : '';

//        $page        = (isset($this->request->page) && intval($this->request->page) > 0) ? intval($this->request->page) : 1;
//        $limit       = (isset($this->request->limit) && intval($this->request->limit) > 0) ? intval($this->request->limit) : 10;
//        $playerModel = new Player();
//        //总数
//        $where         = ['proxy_id' => session('code')];
//        $data['count'] = $playerModel->getCount($where);
//        if ($data['count'] <= 0) {
//            return json($data);
//        }
//        //获取所属玩家
//        $playerList = $playerModel->getList($where, $page, $limit);
//        if ($playerList) {
//            //获取总充值和总业绩数据
//            $this->handlePlayer($playerList);
//        }

        $this->handlePlayer($playerList);
        $res = [];
        if ($userId) {
            foreach ($playerList as $p) {
                if ($p['userid'] == $userId) {
                    $res[] = $p;
                    break;
                }
            }
        } else {
            $res = $playerList;
        }
        $data['data'] = $res;
        return json($data);
    }

    //获取玩家额外数据（总充值，总业绩）
    private function handlePlayer(&$playerList)
    {
        $userId = array_column($playerList, 'userid');
        //查询总充值
        $paytimeModel = new Paytime();
        $totalFee     = $paytimeModel->getListAll(
            ['userid' => $userId],
            'sum(`totalfee`) as totalfee, userid',
            [],
            'userid'
        );
        //查询总业绩
        $playerorderModel = new Playerorder();
        $totalTax         = $playerorderModel->getListAll(
            ['userid' => $userId],
            'sum(`total_tax`) as total_tax, userid',
            [],
            'userid'
        );
        //处理数据
        foreach ($playerList as &$player) {
            $player['total_tax'] = $player['totalfee'] = 0;
            foreach ($totalFee as $fee) {
                if ($fee['userid'] == $player['userid']) {
                    $player['totalfee'] = $fee['totalfee'];
                    break;
                }
            }
            foreach ($totalTax as $tax) {
                if ($tax['userid'] == $player['userid']) {
                    $player['total_tax'] = $tax['total_tax'];
                    break;
                }
            }
        }
        unset($player);
    }

    //代理列表
    public function proxyList()
    {
        return view('proxyList');
    }

    public function proxyListData()
    {
        $data           = [
            'code'  => 0,
            'msg'   => '',
            'count' => 0,
            'data'  => []
        ];
        $username       = isset($this->request->username) ? strval($this->request->username) : '';
        $page           = (isset($this->request->page) && intval($this->request->page) > 0) ? intval($this->request->page) : 1;
        $limit          = (isset($this->request->limit) && intval($this->request->limit) > 0) ? intval($this->request->limit) : 10;
        $teamlevelModel = new Teamlevel();
        $where          = [['parent_id', '=', session('code')]];
        if ($username) {
            $where[] = ['username', 'like', "%$username%"];
        }
        //获取总数
        $count         = $teamlevelModel->getCount($where);
        $data['count'] = 500;
        if (!$count) {
            return json($data);
        }

        //获取下级代理列表
        $proxyList = $teamlevelModel->getList($where, $page, $limit);
        if ($proxyList) {
            $this->handleProxy($proxyList);
        }
        $data['data'] = $proxyList;
        return json($data);
    }

    //获取代理额外数据
    private function handleProxy(&$proxyList)
    {
        $proxyListId = array_column($proxyList, 'proxy_id');
        //查询总充值
        $paytimeModel = new Paytime();
        $totalFee     = $paytimeModel->getListAll(
            ['proxy_id' => $proxyListId],
            'sum(`totalfee`) as totalfee, proxy_id',
            [],
            'proxy_id'
        );
        //查询总业绩
        $playerorderModel = new Playerorder();
        $totalTax         = $playerorderModel->getListAll(
            ['proxy_id' => $proxyListId],
            'sum(`total_tax`) as total_tax, proxy_id',
            [],
            'proxy_id'
        );
        //查询总利润和分成
        $proxyModel = new Proxy();
        $totalIn    = $proxyModel->getListAll(
            ['code' => $proxyListId],
            'historyin, percent, code as proxy_id, username'
        );
        //查询玩家余额
        $playerModel = new Player();
        $totalLeft   = $playerModel->getListAll(
            ['proxy_id' => $proxyListId],
            'sum(`leftmoney`) as leftmoney, proxy_id',
            [],
            'proxy_id'
        );
        //处理数据
        foreach ($proxyList as &$proxy) {
            $proxy['totalfee'] = $proxy['total_tax'] = $proxy['percent'] = $proxy['historyin'] = $proxy['leftmoney'] = 0;
            $proxy['username'] = '';
            foreach ($totalFee as $fee) {
                if ($fee['proxy_id'] == $proxy['proxy_id']) {
                    $proxy['totalfee'] = $fee['totalfee'];
                    break;
                }
            }
            foreach ($totalTax as $tax) {
                if ($tax['proxy_id'] == $proxy['proxy_id']) {
                    $proxy['total_tax'] = $tax['total_tax'];
                    break;
                }
            }
            foreach ($totalIn as $in) {
                if ($in['proxy_id'] == $proxy['proxy_id']) {
                    $proxy['historyin'] = $in['historyin'];
                    $proxy['username']  = $in['username'];
                    $proxy['percent']   = $in['percent'];
                    break;
                }
            }
            foreach ($totalLeft as $left) {
                if ($left['proxy_id'] == $proxy['proxy_id']) {
                    $proxy['leftmoney'] = $left['leftmoney'];
                    break;
                }
            }
        }
        unset($proxy);
    }

    //新增代理
    public function addProxy()
    {
        return view('addProxy');
    }

    //获取分成比例列表
    public function getPercent()
    {
        $proxyModel  = new Proxy();
        $proxyInfo   = $proxyModel->getRowById(session('id'), 'percent');
        $percentList = generate_percent($proxyInfo['percent']);
        return json(['code' => 0, 'data' => $percentList]);
    }

    //处理新增
    public function doAddProxy()
    {
        //判断参数
        $data   = [
            'code' => 0,
            'msg'  => config('msg.add_proxy_0')
        ];
        $result = $this->validate($this->request->post(), 'app\index\validate\AddProxy');
        if (true !== $result) {
            $data['code'] = 1;
            $data['msg']  = $result;
            return json($data);
        }

        $username      = $this->request->username;
        $password      = $this->request->password;
        $percent       = $this->request->percent;
        $allowAddproxy = $this->request->allow_addproxy;
        $descript      = $this->request->descript;


        $proxyModel = new Proxy();
        $proxyInfo  = $proxyModel->getRowById(session('id'));
        //是否有权限添加代理
        if ($proxyInfo['allow_addproxy'] == 0) {
            $data['code'] = 2;
            $data['msg']  = config('msg.add_proxy_1');
            return json($data);
        }
        //判断分成比例
        $percentList = generate_percent($proxyInfo['percent']);
        if (!in_array($percent, $percentList)) {
            $data['code'] = 3;
            $data['msg']  = config('msg.add_proxy_2');
            return json($data);
        }

        //检查账号名
        if ($proxyModel->getRow(['username' => $username])) {
            $data['code'] = 4;
            $data['msg']  = config('msg.add_proxy_3');
            return json($data);
        }

        //生成代理账号
        $code = get_proxy_code();
        //生成盐
        $salt = random_str(6);
        //生成密码
        $pwd = md5($salt . $password);
        //要增加proxy表的数据
        $insertData = [
            'code'           => $code,
            'username'       => $username,
            'password'       => $pwd,
            'salt'           => $salt,
            'allow_addproxy' => $allowAddproxy,
            'parent_id'      => session('code'),
            'percent'        => $percent,
            'regtime'        => date('Y-m-d H:i:s'),
            'descript'       => $descript
        ];

        $teamlevelModel = new Teamlevel();
        //获取当前用户的分销级别
        $level = $teamlevelModel->getRow(['proxy_id' => session('code')], 'max(level) level');
        //获取当前用户分销关系
        $fxList      = $teamlevelModel->getListAll(['proxy_id' => session('code')]);
        $insertData2 = [['username' => $username, 'proxy_id' => $code, 'parent_id' => session('code'), 'level' => 1]];
        //组装插入分销关系表数据
        foreach ($fxList as $fx) {
            if ($fx['level'] == 1) {
                $insertData2[] = ['username' => $username, 'proxy_id' => $code, 'parent_id' => $fx['parent_id'], 'level' => intval($fx['level']) + 1];
            }
        }

        //开启事务
        Db::startTrans();
        try {
            $proxyModel->add($insertData);
            $teamlevelModel->addAll($insertData2);
            Db::commit();
        } catch (\Exception $e) {
            $data['code'] = 5;
            $data['msg']  = config('msg.add_proxy_4');
            save_log('proxy/add', "status:0,username:{$username},currentcode:" . session('code') . ",msg:{$e->getMessage()}");
            return json($data);
        }
        save_log('proxy/add', "status:1,username:{$username},currentcode:" . session('code'));
        return json($data);
    }

}