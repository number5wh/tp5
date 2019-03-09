<?php
/**
 * 代理
 * User: Administrator
 * Date: 2019/3/8
 * Time: 17:46
 */
namespace app\index\model;
use think\Db;
use think\Model;
class Proxy extends Model
{
    protected $table = 'proxy';

    public function getInfoByUsername($username, $field = '*')
    {
        $user = Db::table($this->table)->where('username', $username)->field($field)->find();
        return $user;
    }
    public function getInfoByIdentifier($identifier, $field = '*')
    {
        $user = Db::table($this->table)->where('identifier', $identifier)->field($field)->find();
        return $user;
    }

    public function getInfoById($id, $field='*')
    {
        $user = Db::table($this->table)->where('id', $id)->field($field)->find();
        return $user;
    }

    public function updateById($id, $data)
    {
        $res = Db::table($this->table)->where('id', $id)->update($data);
        return $res;
    }
    public function updateByWhere($where, $data)
    {
        $res = Db::table($this->table)->where($where)->update($data);
        return $res;
    }
}