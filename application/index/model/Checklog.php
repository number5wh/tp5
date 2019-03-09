<?php
/**
 * 提现记录
 * User: Administrator
 * Date: 2019/3/5
 * Time: 17:04
 */
namespace app\index\model;
use think\Db;
use think\Model;
class Checklog extends Model
{
    protected $table = 'checklog';

    //根据proxyId查提现记录
    public function getByProxyId($proxyId, $page=1, $limit=10, $field='*')
    {
        $info = Db::table($this->table)
            ->where('proxy_id', $proxyId)
            ->field($field)
            ->page($page, $limit)
            ->select();
        return $info;
    }
    //查询总额
    public function getAmountByProxyId($proxyId)
    {
        $info = Db::table($this->table)
            ->field('sum(amount) amount')
            ->where('proxy_id', $proxyId)
            ->find();
        return $info;
    }
    //查询总数
    public function getCountByProxyId($proxyId)
    {
        $info = Db::table($this->table)
            ->where('proxy_id', $proxyId)
            ->count();
        return $info;
    }
    //添加提现记录
    public function add($data)
    {
        $info = Db::table($this->table)->data($data)->insert();
        return $info;
    }
}