<?php

return [
    //用户表
    'name'                          => '用户名',
    'password'                      => '密码',
    'old_password'                  => '旧密码',
    'res_password'                  => '确认密码',
    'captcha'                       => '验证码',
    'group'                         => '管理组',

    //用户信息表
    'nickname'                   => '昵称',
    'sex'                        => '性别',
    'email'                      => '邮箱',
    'mobile'                     => '手机',
    'qq'                         => 'QQ',
    'avatar'                     => '头像',
    'avatar_id'                  => '头像',
    'birthday'                   => '生日',
    'info'                       => '信息',

    //数据验证提示
    'name_max'                  => '名称长度不能超过50个字符',
    'name_require'              => '名称不能为空',
    'name_unique'               => '名称已重复',
    'password_require'          => '密码不能为空',
    'old_password_require'      => '旧密码不能为空',
    'old_password_right'        => '旧密码输入错误',
    'res_password_require'      => '确认密码不能为空',
    'res_password_confirm'      => '两次密码输入不一致',
    'captcha_require'           => '验证码不能为空',
    'captcha_error'             => '验证码错误',
    'nickname_max'              => '昵称长度不能超过64个字符',
    'nickname_unique'           => '昵称已重复',
    'mobile_max'                => '手机号码长度不能超过16个字符',
    'mobile_mobile'             => '手机号码有误',
    'email_max'                 => '邮箱长度不能超过32个字符',
    'email_email'               => '邮箱格式错误',


    'supper_stop'               => 'Admin管理员禁止禁用',

    'admin_no_exist'            =>'管理员不存在',
    'admin_stop'                =>'管理员被禁用',
];