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

class Admin extends Validate
{
    protected $rule=[
        'name'              =>'require|max:50|unique:admin',
        'nickname'          =>'max:64|unique:admin',
        'mobile'            =>'max:16|mobile',
        'email'             =>'max:32|email',
        'password'          =>'require',
        'res_password'      =>'require|confirm:password',
        'captcha'           =>'require|captcha',
    ];

    protected $message = [
        'name.require'              => '{%name_require}',
        'name.max'                  => '{%name_max}',
        'name.unique'               => '{%name_unique}',
        'password.require'          => '{%password_require}',
        'res_password.require'      => '{%res_password_require}',
        'res_password.confirm'      => '{%res_password_confirm}',
        'captcha.require'           => '{%captcha_require}',
        'captcha.captcha'           => '{%captcha_error}',
        'nickname.max'              => '{%nickname_max}',
        'nickname.unique'           => '{%nickname_unique}',
        'mobile.max'                => '{%mobile_max}',
        'mobile.mobile'             => '{%mobile_mobile}',
        'email.max'                 => '{%email_max}',
        'email.email'               => '{%email_email}',
    ];

    protected $scene = [
        'add'       => ['name', 'password', 'res_password', 'nickname', 'mobile', 'email'],
        'edit'      => ['id','name', 'nickname', 'mobile', 'email']
    ];

    public function sceneLogin()
    {
        return $this->only(['name','password','captcha'])
            ->remove('name', 'unique');
    }
}