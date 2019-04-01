<?php
/**
 * Created by PhpStorm.
 * Name: AdminUser.php
 * User: jackson
 * Date: 2018/3/21
 * Time: 上午10:42
 */

namespace app\admin\model;

class Menu extends Base
{

    protected $pk = 'id';

    public function getMenusByCondition($condition)
    {
        $result = $this->where($condition)
            ->select();
        return $result;

    }

    public function getTree($data)
    {
        return $this->_reSort($data);
    }

    private function _reSort($data, $parent_id = 0, $level = 0, $isClear = TRUE)
    {
        static $ret = array();
        if ($isClear) {
            $ret = array();
        }

        foreach ($data as $k => $v) {
            if ($v['parent_id'] == $parent_id) {
                $v['level'] = $level;
                $ret[] = $v;
                $this->_reSort($data, $v['id'], $level + 1, FALSE);
            }
        }
        return $ret;
    }

    public function getChildren($id)
    {
        $data = $this->select();
        return $this->_children($data, $id);
    }

    private function _children($data, $parent_id = 0, $isClear = TRUE)
    {
        static $ret = array();
        if ($isClear)
            $ret = array();
        foreach ($data as $k => $v) {
            if ($v['parent_id'] == $parent_id) {
                $ret[] = $v['id'];
                $this->_children($data, $v['id'], FALSE);
            }
        }
        return $ret;
    }
}