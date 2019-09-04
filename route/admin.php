<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------


//登录
Route::group('admin/login', function () {
    Route::rule('index', 'admin/login/index');
    Route::rule('layout', 'admin/login/layout');
    Route::rule('chkCode', 'admin/login/chkCode');
});

//首页
Route::group('admin/index', function () {
    Route::rule('index', 'admin/index/index');
});

//上传图片
Route::group('admin/upload', function () {
    Route::rule('image', 'admin/upload/image');
    Route::rule('uploadBase64', 'admin/upload/uploadBase64');
});

//管理员
Route::group('admin/admin', function () {
    Route::rule('index', 'admin/admin/index');
    Route::rule('create', 'admin/admin/create');
    Route::rule('edit', 'admin/admin/edit');
});

//管理组
Route::group('admin/group', function () {
    Route::rule('index', 'admin/group/index');
    Route::rule('create', 'admin/group/create');
    Route::rule('edit', 'admin/group/edit');
    Route::rule('setPrivilege', 'admin/group/setPrivilege');
});

//菜单
Route::group('admin/menu', function () {
    Route::rule('index', 'admin/menu/index');
    Route::rule('create', 'admin/menu/create');
    Route::rule('edit', 'admin/menu/edit');
});

//管理员日志
Route::group('admin/adminLog', function () {
    Route::rule('index', 'admin/adminLog/index');
    Route::rule('content', 'admin/adminLog/content');
});