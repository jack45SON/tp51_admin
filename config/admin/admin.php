<?php
return [
    //后台管理员用户状态
    'admin_status_delete'       => -1,
    'admin_status_normal'       => 1,
    'admin_status_disable'      => 0,


    //url地址
    'layout_url'                =>'/admin/login/layout',

    //session
    'session_admin_scope'               => 'admin',
    'session_admin_user'                => 'adminUser',
    'session_admin_id'                  => 'adminId',


    'session_admin_menu'                => 'adminMenu',
    'session_admin_auth'                => 'adminAuth',
    'session_admin_auth_check'          => 'adminAuthCheck',
    'session_admin_auth_check_navP'     => 'adminAuthCheckNavP',
    'session_admin_auth_check_nav'      => 'adminAuthCheckNav',

    'redis_expire'                      =>  10*86500,
];