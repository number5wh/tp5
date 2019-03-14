<?php
/**
 * 明细查询
 * User: Administrator
 * Date: 2019/3/8
 * Time: 11:52
 */
namespace app\index\controller;
use think\Controller;
use app\index\model\Playerorder;
class Detail extends Controller
{
    protected $middleware = ['Auth'];
    public function index()
    {
        return view('index');
    }

    public function getData()
    {
        $data = [
            'code' => 0,
            'msg' => '',
            'count' => 0,
            'data' => []
        ];
        $page  = (isset($this->request->page) && intval($this->request->page) > 0) ? intval($this->request->page) : 1;
        $limit = (isset($this->request->limit) && intval($this->request->limit) > 0) ? intval($this->request->limit) : 10;

//        $playerorderModel = new Playerorder();
//        $count = $playerorderModel->getCount(['proxy_id' => session('code')]);
//        $data['count'] = $count;
//        if (!$count) {
//            return json($data);
//        }
//        $list = $playerorderModel->getList(['proxy_id' => session('code')], $page, $limit, 'id, userid, game, total_money, createtime');
//        $data['data'] = $list;
        return json($data);
    }
}