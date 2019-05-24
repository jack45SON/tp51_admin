<?php
/**
 * Created by PhpStorm.
 * Name: AdminUser.php
 * User: jackson
 * Date: 2018/3/21
 * Time: 上午10:42
 */

namespace app\admin\model;

class Group extends Base
{
    // 表名
    protected $table = 'group';

    public function adminGroup()
    {
        return $this->hasMany('AdminGroup', 'group_id');
    }
}