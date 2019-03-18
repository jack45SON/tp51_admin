<?php
/**
 * Created by PhpStorm.
 * Name: AdminUser.php
 * User: jackson
 * Date: 2018/3/21
 * Time: 上午10:30
 */
namespace app\common\validate;

use think\Validate;

class Admin extends Validate
{
    protected $rule=[
        'name'              =>'require|max:50|unique:admin',
        'password'          =>'require',
        'old_password'      =>'require',
        'res_password'      =>'require|confirm:password',
        'captcha'           =>'require|captcha',
    ];

    protected $message = [
        'name.require'              => '{%name_require}',
        'name.max'                  => '{%name_max}',
        'name.unique'               => '{%name_unique}',
        'password.require'          => '{%password_require}',
        'old_password.require'      => '{%old_password_require}',
        'res_password.require'      => '{%res_password_require}',
        'res_password.confirm'      => '{%res_password_confirm}',
        'captcha.require'           => '{%captcha_require}',
        'captcha.captcha'           => '{%captcha_error}',
    ];

    protected $scene = [
        'add'       => ['name', 'password', 'res_password'],
        'edit'      => ['old_password', 'password', 'res_password']
    ];

    public function sceneLogin()
    {
        return $this->only(['name','password','captcha'])
            ->remove('name', 'unique');
    }
}