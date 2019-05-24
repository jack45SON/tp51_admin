<?php
namespace app\admin\model;

class Admin extends Base
{
    // 表名
    protected $table = 'admin';

    public function adminInfo()
    {
        return $this->hasOne('AdminInfo', 'admin_id')->field('id,admin_id,nickname,avatar');
    }


    public function adminGroup()
    {
        return $this->hasMany('AdminGroup', 'admin_id');
    }
}