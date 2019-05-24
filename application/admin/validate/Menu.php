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

class Menu extends Validate
{
    protected $rule=[
        'name'        =>'require',
        'module'      =>'require',
    ];

    protected $message = [
        'name.require'        => '{%name_require}',
        'module.require'      => '{%module_require}',
    ];

    protected $scene = [
        'add'       => ['name', 'module'],
        'edit'       => ['id']
    ];
}