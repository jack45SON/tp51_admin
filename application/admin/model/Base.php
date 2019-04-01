<?php
namespace app\admin\model;

use think\Model;

class Base extends Model
{
    public function insertUpdate($data, $id, $is_update = true)
    {
        $adminId = session(config('admin.session_admin_id'), '', config('admin.session_admin_scope'));
        if($is_update){
            $data['update_admin'] = $adminId;
        }else{
            $data['create_admin'] = $adminId;
        }
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