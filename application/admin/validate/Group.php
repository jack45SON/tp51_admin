<?php
/**
 * Created by PhpStorm.
 * Name: AdminUser.php
 * User: jackson
 * Date: 2018/3/21
 * Time: 上午10:30
 */
namespace app\admin\validate;

use think\Validate;

class Group extends Validate
{
    protected $rule=[
        'name'        =>'require|max:20|unique:Group',
    ];

    protected $message = [
        'name.require'         => '{%name_require}',
        'name.unique'          => '{%name_unique}',
        'name.max'             => '{%name_max}',
    ];

    protected $scene = [
        'add'       => ['name'],
        'edit'      => ['id']
    ];
}