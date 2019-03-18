<?php
/**
 * Created by PhpStorm.
 * Name: AdminUser.php
 * User: jackson
 * Date: 2018/3/21
 * Time: 上午10:42
 */

namespace app\common\model;

class Group extends Base
{

    protected $pk = 'id';

    public function adminGroup()
    {
        return $this->hasMany('AdminGroup', 'group_id');
    }
}