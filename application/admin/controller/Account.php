<?php
/**
 * 账号管理
 * User: Administrator
 * Date: 2019/3/7
 * Time: 15:00
 */

namespace app\admin\controller;

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
    protected $middleware = ['AdminAuth'];

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
        $proxyModel = new Proxy();
        $where          = [];
        if ($username) {
            $where[] = ['username', 'like', "%$username%"];
        }
        //获取总数
        $count         = $proxyModel->getCount($where);
        $data['count'] = $count;
        if (!$count) {
            return json($data);
        }

        //获取代理列表
        $proxyList = $proxyModel->getList($where, $page, $limit, 'historyin, percent, id, code as proxy_id, username, descript');
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
            $proxy['totalfee'] = $proxy['total_tax'] = $proxy['leftmoney'] = 0;
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
            foreach ($totalLeft as $left) {
                if ($left['proxy_id'] == $proxy['proxy_id']) {
                    $proxy['leftmoney'] = $left['leftmoney'];
                    break;
                }
            }
        }
        unset($proxy);
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
        $proxy      = $proxyModel->getRow(['code' => $proxyId], 'id,code, username, allow_addproxy, percent, descript,parent_id');
        if (!$proxy) {
            $data['code'] = 2;
            $data['msg']  = config('msg.edit_proxy_1');
            return json($data);
        }

        //查询当前代理上级的代理的比例
        if ($proxy['id'] == 1) {//第一个人100
            $parentPercent = 110;
        } else {
            $parentPercent = intval($proxyModel->getValue(['code' => $proxy['parent_id']], 'percent'));
        }

        //查询当前代理是否有下级代理，并获取其最大分成比例
        $childPercent = intval($proxyModel->getValue(['parent_id' => $proxyId], 'max(percent) percent'));
        //生成可调整的分成比例
        $percentList = [];
        for ($i = $childPercent + config('config.percent_diff'); $i < $parentPercent; $i += config('config.percent_diff')) {
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

        $proxyModel = new Proxy();
        $proxyInfo  = $proxyModel->getRow(['username' => $username]);
        if (!$proxyInfo) {
            $data['code'] = 2;
            $data['msg']  = config('msg.edit_proxy_1');
            return json($data);
        }

        //查询当前代理上级的代理的比例
        if ($proxyInfo['id'] == 1) {//第一个人100
            $parentPercent = 110;
        } else {
            $parentPercent = intval($proxyModel->getValue(['code' => $proxyInfo['parent_id']], 'percent'));
        }
        //查询当前代理是否有下级代理，并获取其最大分成比例
        $childPercent = intval($proxyModel->getValue(['parent_id' => $proxyInfo['code']], 'max(percent) percent'));
        if ($percent >= $parentPercent || $percent <= $childPercent) {
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
}