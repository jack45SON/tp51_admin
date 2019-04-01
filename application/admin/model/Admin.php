<?php
namespace app\admin\model;

class Admin extends Base
{

    protected $pk = 'id';

    public function adminInfo()
    {
        return $this->hasOne('AdminInfo', 'admin_id')->field('id,admin_id,nickname,avatar');
    }


    public function adminGroup()
    {
        return $this->hasMany('AdminGroup', 'admin_id');
    }
}