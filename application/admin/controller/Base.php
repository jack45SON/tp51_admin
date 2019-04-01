<?php

namespace app\admin\controller;

use app\admin\model\AdminGroup;
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
    private   $auth = false;

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

        //加载多语言相应控制器对应字段
        if (isset($_COOKIE['think_var']) && $_COOKIE['think_var']) {
            $langField = $_COOKIE['think_var'];
        } else {
            $langField = config('default_lang');
        }

        Lang::load(APP_PATH . 'lang/' . $langField . '/' . CONTROLLER_NAME . '.php');

        if (!$this->isLogin()) {
            $this->redirect('/admin/login/index');
        }

        $menus = [];
        //如果权限存在则不进行判断
        if (session('?' . config('admin.session_admin_auth') . $this->adminId, '', config('admin.session_admin_scope'))) {
            if (session('?' . config('admin.session_admin_menu'), '', config('admin.session_admin_scope'))) {
                $menus = session(config('admin.session_admin_menu'), '', config('admin.session_admin_scope'));
            }

        } else {
            $GroupMenuModel = new GroupMenu();
            if ($this->adminId != 1 && $this->auth) {
                $auth = $this->checkAuth($GroupMenuModel);
                if ($auth['flag']) {
                    $menus = $auth['data'];
                } else {
                    if (strtolower(CONTROLLER_NAME) == 'index') {
                        $this->redirect('/admin/index/_index');
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
                session(config('admin.session_admin_auth') . $this->adminId, $GroupMenu, config('admin.session_admin_scope'));
                $menus = $this->getMenus();
            }
        }
        //全局变量输出
        View::share('menus', $menus);
        View::share('adminUser', $this->adminUser);

        //加载多语言相应控制器对应字段
        if (isset($_COOKIE['think_var']) && $_COOKIE['think_var']) {
            $langField = $_COOKIE['think_var'];
        } else {
            $langField = config('default_lang');
        }
        Lang::load(APP_PATH . 'lang/' . $langField . '/' . CONTROLLER_NAME . '.php');
    }

    /**
     * @Title: _isLogin
     * @Description: todo(判断登录状态)
     * @Author: liu tao
     */
    protected function isLogin()
    {
        $this->adminId = session(config('admin.session_admin_id'), '', config('admin.session_admin_scope'));
        $this->adminUser = session(config('admin.session_admin_user'), '', config('admin.session_admin_scope'));

        if ($this->adminId) {
            return true;
        }
        return false;
    }

    /**
     * @Title: checkAuth
     * @Description: todo(权限判断)
     * @Author: liu tao
     * @return array
     */
    protected function checkAuth($GroupMenuModel)
    {
        $AdminGroupModel = new AdminGroup();
        $AdminGroup = $AdminGroupModel
            ->where('admin_id', 'eq', $this->adminId)
            ->column('group_id');
        $GroupMenu = $GroupMenuModel
            ->where('group_id', 'in', $AdminGroup)
            ->column('menu_id');

        $data = [];
        if (!(strtolower(CONTROLLER_NAME) == 'index')) {
            $where = [
                'module' => MODULE_NAME,
                'controller' => CONTROLLER_NAME,
                'action' => ACTION_NAME,
            ];

            $MenuModel  = new \app\admin\model\Menu();
            $auth = $MenuModel->where($where)->select();
            $auth = $auth->toArray();
            if (count($auth) > 1) {
                $flag = commonAuth($auth, $GroupMenu);
                if ($flag) {
                    return ['flag' => false, 'msg' => lang('auth_no_exist')];
                }

            } else {
                if (empty($auth) || !in_array($auth[0]['id'], $GroupMenu)) {

                    return ['flag' => false, 'msg' => lang('auth_no_exist')];
                }
            }
        }
        if (!empty($GroupMenu)) {
            $data = $this->getMenus($GroupMenu);
        }

        session(config('admin.session_admin_auth') . $this->adminId, $GroupMenu, config('admin.session_admin_scope'));
        return ['flag' => true, 'data' => $data];
    }

    /**
     * @Title: getMenus
     * @Description: todo(获取菜单列表)
     * @Author: liu tao
     * @return array
     */
    protected function getMenus($ids = [])
    {
        $where = [];
        if ($ids) {
            $where[] = ['id', 'in', $ids];
        }
        $MenuModel  = new \app\admin\model\Menu();
        $menus = $MenuModel
            ->where('level', 'in', [1, 2])
            ->where('is_show', 'eq', 1)
            ->where('status', 'eq', 1)
            ->where($where)
            ->order('sort', 'asc')
            ->select();
        $arr = array();
        $menus = $menus->toArray();
        foreach ($menus as $key => $val) {
            //找顶级权限
            if ($val['parent_id'] == 0) {
                $val['children'] = [];
                foreach ($menus as $k => $v) {
                    if ($v['parent_id'] == $val['id']) {
                        $val['children'][] = $v;
                    }
                }

                $arr[] = $val;
            }
        }
        session(config('admin.session_admin_menu'), $arr, config('admin.session_admin_scope'));
        return $arr;
    }
}
