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


Route::group('admin/login', function () {
    Route::rule('index', 'admin/login/index');
    Route::rule('layout', 'admin/login/layout');
    Route::rule('chkCode', 'admin/login/chkCode');
});


Route::group('admin/index', function () {
    Route::rule('index', 'admin/index/index');
});

Route::group('admin/upload', function () {
    Route::rule('image', 'admin/upload/image');
    Route::rule('uploadBase64', 'admin/upload/uploadBase64');
});



Route::group('admin/admin', function () {
    Route::rule('index', 'admin/admin/index');
    Route::rule('create', 'admin/admin/create');
    Route::rule('edit', 'admin/admin/edit');
    Route::rule('detail', 'admin/admin/detail');
});

Route::group('admin/group', function () {
    Route::rule('index', 'admin/group/index');
    Route::rule('create', 'admin/group/create');
    Route::rule('edit', 'admin/group/edit');
    Route::rule('setPrivilege', 'admin/group/setPrivilege');
});

Route::group('admin/menu', function () {
    Route::rule('index', 'admin/menu/index');
    Route::rule('create', 'admin/menu/create');
    Route::rule('edit', 'admin/menu/edit');
});
