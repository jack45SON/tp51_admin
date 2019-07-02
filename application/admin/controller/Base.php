<?php

namespace app\admin\controller;

use app\admin\lib\Auth;
use app\admin\model\GroupMenu;
use think\Controller;
use think\facade\Lang;
use think\facade\View;

class Base extends Controller
{
    /**
     * 管理员id
     * @var
     */
    protected $adminId;

    /**
     * 管理员详情
     * @var
     */
    protected $adminUser;

    /**
     * 管理员分组
     * @var
     */
    protected $adminGroup;

    /**
     * 是否开启权限限制
     * @var bool
     */
    private   $auth = true;

    /**
     * 模型层
     * @var
     */
    protected  $model;

    /**
     * 服务层
     * @var
     */
    protected  $service;

    /**
     * 引入后台控制器的traits
     */
    use \app\admin\library\traits\Backend;

    public function initialize()
    {
        parent::initialize();
        // 定义应用目录
        define('APP_PATH', \Env::get('root_path') . 'application/');
        //定义模型、控制器、方法
        define('MODULE_NAME', request()->module());
        define('CONTROLLER_NAME', request()->controller());
        define('ACTION_NAME', request()->action());

        $redis = redis(config('admin.admin_redis_select'));
        if (!$this->isLogin($redis)) {
            $this->redirect('/'.MODULE_NAME.'/login/index');
        }

        $menus = [];
        //如果权限存在则不进行判断
        if ($redis->exists(config('admin.session_admin_auth') . $this->adminId)) {
            if ($redis->exists(config('admin.session_admin_menu') . $this->adminId)) {
                $menus = json_decode($redis->get(config('admin.session_admin_menu') . $this->adminId),true);
            }
        } else {
            $Auth = new Auth($this->adminId,$redis);
            $GroupMenuModel = new GroupMenu();
            if ($this->adminId != 1 && $this->auth) {
                $auth = $Auth::checkAuth($GroupMenuModel);
                if ($auth['flag']) {
                    $menus = $auth['data'];
                } else {
                    if (strtolower(CONTROLLER_NAME) == 'Index') {
                        $this->redirect('/'.MODULE_NAME.'/Index/index');
                    }
                    if (request()->isAjax() && request()->isPost()) {
                        die(json_encode([
                            'status' => -1,
                            'message' => $auth['msg']
                        ]));
                    } else {
                        $this->error($auth['msg']);
                    }
                }
            } else {
                $GroupMenu = $GroupMenuModel
                    ->column('menu_id');
                $redis->set(config('admin.session_admin_auth') . $this->adminId, json_encode($GroupMenu), config('admin.redis_expire'));
                $menus = $Auth::getMenus();
            }
        }

        if(!request()->isAjax()){
            //全局变量输出
            View::share('menus', $menus);
            View::share('adminUser', $this->adminUser);
            View::share('select_url', $this->getUrl());
        }


        //加载多语言相应控制器对应字段
        if (isset($_COOKIE['think_var']) && $_COOKIE['think_var']) {
            $langField = $_COOKIE['think_var'];
        } else {
            $langField = config('default_lang');
        }
        Lang::load(APP_PATH .MODULE_NAME.'/lang/' . $langField . '/' . CONTROLLER_NAME . '.php');
    }


    /**
     * @Title: _isLogin
     * @Description: todo(判断登录状态)
     * @Author: liu tao
     */
    protected function isLogin($redis)
    {
        $this->adminId = session(config('admin.session_admin_id'), '', config('admin.session_admin_scope'));
        $this->adminUser = session(config('admin.session_admin_user'), '', config('admin.session_admin_scope'));
        if ($this->adminId) {
            if($redis->exists(config('admin.session_admin_id') . $this->adminId)){
                return true;
            }
        }
        return false;
    }


    /**
     * @Title: getUrl
     * @Description: todo(获取当前url)
     * @Author: liu tao
     * @Time: xxx
     * @return string
     */
    protected function getUrl(){

        $url = request()->domain().request()->baseUrl().'?';
        $param = request()->param();
        foreach ($param as $k=>$v){
            if($k != '_pagination'){
                $url.= '&'.$k.'='.$v;
            }
        }
        return $url;
    }
}
