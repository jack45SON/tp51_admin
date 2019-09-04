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

class Type extends Validate
{
    protected $rule=[
        'name'        =>'require',
    ];

    protected $message = [
        'name.require'        => '{%name_require}',
    ];

    protected $scene = [
        'add'       => ['name'],
        'edit'       => ['id']
    ];
}