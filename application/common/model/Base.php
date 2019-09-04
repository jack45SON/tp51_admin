<?php
namespace app\common\model;

use think\Model;

class Base extends Model
{
    // 设置当前模型的数据库连接
    protected $pk = 'id';
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'date';
    protected $dateFormat = 'Y-m-d H:i:s';

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
}