<?php
/**
 * 账号管理
 * User: Administrator
 * Date: 2019/3/7
 * Time: 15:00
 */

namespace app\index\controller;

use apiData\PlayerData;
use app\model\Playerorder;
use app\model\Proxy;
use qrCode\Code;
use think\Controller;
use app\model\Player;
use app\model\Teamlevel;
use app\model\Paytime;
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
        $data       = [
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
        $num        = 1;
        foreach ($onlineList->data as $v) {
//            $v->balance = change_to_yuan($v->balance);
            $v            = json_decode(json_encode($v), true);
            $v['id']      = $num;
            $playerList[] = $v;
            $num++;
        }

        $userId = isset($this->request->userid) ? strval($this->request->userid) : '';

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
        //查询备注
        $playerModel = new Player();
        $desc = $playerModel->getListAll(['userid' => $userId], 'userid, descript');

        //处理数据
        foreach ($playerList as &$player) {
            $player['total_tax'] = $player['totalfee'] = 0;
            $player['descript'] = '';
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

            foreach ($desc as $d) {
                if ($d['userid'] == $player['userid']) {
                    $player['descript'] = htmlspecialchars($d['descript']);
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
        $where          = [
            ['parent_id', '=', session('code')],
            ['level', '=', 1]
        ];
        if ($username) {
            $where[] = ['username', 'like', "%$username%"];
        }
        //获取总数
        $count         = $teamlevelModel->getCount($where);
        $data['count'] = $count;
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
            'historyin, percent, code as proxy_id, username, descript'
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
                    $proxy['code']      = $in['proxy_id'];
                    $proxy['historyin'] = $in['historyin'];
                    $proxy['username']  = $in['username'];
                    $proxy['percent']   = $in['percent'];
                    $proxy['descript']  = htmlspecialchars($in['descript']);
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
            Code::qrcode($code);//生成二维码
        } catch (\Exception $e) {
            $data['code'] = 5;
            $data['msg']  = config('msg.add_proxy_4');
            save_log('proxy/add', "status:0,username:{$username},currentcode:" . session('code') . ",msg:{$e->getMessage()}");
            return json($data);
        }
        save_log('proxy/add', "status:1,username:{$username},currentcode:" . session('code'));
        return json($data);
    }


    //编辑代理页
    public function edit()
    {
        $data    = ['code' => 0, 'msg' => '', 'data' => [], 'percent' => []];
        $proxyId = $this->request->proxyid;
        if (!$proxyId) {
            $data['code'] = 1;
            $data['msg']  = config('msg.edit_proxy_1');
            return json($data);
        }
        //判断是否是下级代理并获取代理信息
        $proxyModel = new Proxy();
        $proxy      = $proxyModel->getRow(['code' => $proxyId, 'parent_id' => session('code')], 'code, username, allow_addproxy, percent, descript');
        if (!$proxy) {
            $data['code'] = 2;
            $data['msg']  = config('msg.edit_proxy_1');
            return json($data);
        }
        //获取自生的分成比例
        $selfPercent = intval($proxyModel->getValue(['id' => session('id')], 'percent'));

        //查询当前代理是否有下级代理，并获取其最大分成比例
        $childPercent = intval($proxyModel->getValue(['parent_id' => $proxyId], 'max(percent) percent'));
        //生成可调整的分成比例
        $percentList = [];
        for ($i = $childPercent + config('config.percent_diff'); $i < $selfPercent; $i += config('config.percent_diff')) {
            $percentList[] = $i;
        }
        $percentList     = array_reverse($percentList);
        $data['percent'] = $percentList;
        $data['data']    = $proxy;
        return json($data);
    }

    //编辑代理处理
    public function doEdit()
    {
        //判断参数
        $data   = ['code' => 0, 'msg' => config('msg.edit_proxy_0')];
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

        //判断当前代理是否是编辑人下级
        $proxyModel = new Proxy();
        $proxyInfo  = $proxyModel->getRow(['username' => $username, 'parent_id' => session('code')]);
        if (!$proxyInfo) {
            $data['code'] = 2;
            $data['msg']  = config('msg.edit_proxy_1');
            return json($data);
        }

        //判断分成比例
        //获取自生的分成比例
        $selfPercent = intval($proxyModel->getValue(['id' => session('id')], 'percent'));
        //查询当前代理是否有下级代理，并获取其最大分成比例
        $childPercent = intval($proxyModel->getValue(['parent_id' => $proxyInfo['code']], 'max(percent) percent'));
        if ($percent >= $selfPercent || $percent <= $childPercent) {
            $data['code'] = 3;
            $data['msg']  = config('msg.edit_proxy_2');
            return json($data);
        }

        //生成盐
        $salt = random_str(6);
        //生成密码
        $pwd        = md5($salt . $password);
        $updateData = [
            'password'       => $pwd,
            'salt'           => $salt,
            'allow_addproxy' => $allowAddproxy,
            'percent'        => $percent,
            'descript'       => $descript,
            'updatetime'     => time()
        ];
        $res        = $proxyModel->updateByWhere(['code' => $proxyInfo['code']], $updateData);
        if (!$res) {
            $data['code'] = 4;
            $data['msg']  = config('msg.edit_proxy_3');
            return json($data);
        }
        return json($data);
    }

    //编辑玩家备注
    public function doPlayerEdit()
    {
        //判断参数
        $data   = ['code' => 0, 'msg' => config('msg.edit_player_0')];
        $result = $this->validate($this->request->post(), 'app\index\validate\EditPlayer');
        if (true !== $result) {
            $data['code'] = 1;
            $data['msg']  = $result;
            return json($data);
        }
        $userid     = $this->request->userid;
        $descript   = $this->request->descript;
        //判断当前编辑人是不是玩家上属代理
        $playerModel = new Player();
        $playerInfo = $playerModel->getRow(['userid' => $userid, 'proxy_id' => session('code')]);
        if (!$playerInfo) {
            $data['code'] = 2;
            $data['msg']  = config('msg.edit_player_1');
            return json($data);
        }
        if ($playerInfo['descript'] == $descript) {
            $data['code'] = 3;
            $data['msg']  = config('msg.edit_player_2');
            return json($data);
        }

        //更新备注
        $res = $playerModel->updateByWhere(['userid' => $userid, 'proxy_id' => session('code')], ['descript' => $descript, 'updatetime' => date('YmdHis')]);
        if (!$res) {
            $data['code'] = 4;
            $data['msg']  = config('msg.edit_player_3');
            return json($data);
        }
        return json($data);
    }
}