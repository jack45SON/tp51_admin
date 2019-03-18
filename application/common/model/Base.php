<?php
namespace app\common\model;

use think\Model;

class Base extends Model
{
    public function addEdit($data, $id, $is_update = true)
    {
        $this->isUpdate($is_update)->allowField(true)->save($data);
        return $this->$id;
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
}