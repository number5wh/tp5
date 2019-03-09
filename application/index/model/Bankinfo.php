<?php
/**
 * 提现账户数据
 * User: Administrator
 * Date: 2019/3/5
 * Time: 17:04
 */
namespace app\index\model;
use think\Db;
use think\Model;
class Bankinfo extends Model {
    protected $table = 'bankinfo';

    public function getByProxyId($proxyId, $field='*')
    {
        $info = Db::table($this->table)->where('proxy_id', $proxyId)->field($field)->find();
        return $info;
    }

    //新增数据
    public function add($data)
    {
        $info = Db::table($this->table)->data($data)->insert();
        return $info;
    }

    //更新数据
    public function updateByProxyId($proxyId, $data)
    {
        $info = Db::table($this->table)->where('proxy_id', $proxyId)->data($data)->update();
        return $info;
    }
}