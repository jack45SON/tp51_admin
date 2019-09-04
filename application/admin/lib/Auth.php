<?php

namespace app\admin\lib;
use app\admin\model\AdminGroup;
use app\admin\model\Menu;

/**
 * Auth相关
 * Class IAuth
 */
class Auth
{
    private static $adminId;
    private static $redis;

    public function __construct($adminId,$redis)
    {
        self::setAdmin($adminId);
        self::setRedis($redis);
    }


    private static function setAdmin($adminId){
        self::$adminId = $adminId;
    }

    private static function setRedis($redis){
        self::$redis = $redis;
    }

    /**
     * @Title: checkAuth
     * @Description: todo(权限判断)
     * @Author: liu tao
     * @Time: xxx
     * @param $GroupMenuModel
     * @return array
     */
    public static function checkAuth($GroupMenuModel)
    {
        $AdminGroupModel = new AdminGroup();
        $AdminGroup = $AdminGroupModel->alias('ag')
            ->leftJoin('group g', 'g.id = ag.group_id')
            ->where('admin_id', 'eq', self::$adminId)
            ->where('g.status', 'eq', 1)
            ->column('group_id');

        $GroupMenu = $GroupMenuModel
            ->where('group_id', 'in', $AdminGroup)
            ->column('menu_id');

        $data = [];

        if (!(strtolower(CONTROLLER_NAME) == 'index')) {
            $where = [
                'module'        => MODULE_NAME,
                'controller'    => CONTROLLER_NAME,
                'action'        => ACTION_NAME,
            ];

            $MenuModel = new Menu();
            $auth = $MenuModel->where($where)->select();
            $auth = $auth->toArray();
            if (count($auth) > 1) {
                $flag = self::getAuth($auth, $GroupMenu);
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
            $data = self::getMenus($GroupMenu);
        }

        self::$redis->set(config('admin.session_admin_auth') . self::$adminId, json_encode($GroupMenu), config('admin.redis_expire'));
        return ['flag' => true, 'data' => $data];
    }

    /**
     * @Title: getMenus
     * @Description: todo(菜单列表)
     * @Author: liu tao
     * @Time: xxx
     * @param array $ids
     * @return array
     */
    public static function getMenus($ids = [])
    {
        $where = [];
        if ($ids) {
            $where[] = ['id', 'in', $ids];
        }
        $MenuModel  = new Menu();
        $menus = $MenuModel
            ->where('level', 'in', [1, 2])
            ->where('show', 'eq', 1)
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
        self::$redis->set(config('admin.session_admin_menu') . self::$adminId, json_encode($arr), config('admin.redis_expire'));
        return $arr;
    }

    /**
     * @Title: authCheck
     * @Description: todo(操作权限)
     * @Author: liu tao
     * @Time: xxx
     * @param $GroupMenu
     * @param $where
     * @return bool
     */
    public static function authCheck($GroupMenu,$where){
        //判断该操作权限是否存在
        if(self::$redis->exists(config('admin.session_admin_auth_check') . self::$adminId . http_build_query($where))){
            $auth =json_decode(self::$redis->get(config('admin.session_admin_auth_check')  . self::$adminId. http_build_query($where)),true);
        }else{
            $MenuModel = new Menu();
            $auth = $MenuModel->field('id,parent_id,params')->where($where)->select()->toArray();
            self::$redis->set(config('admin.session_admin_auth_check') . self::$adminId . http_build_query($where),json_encode($auth), config('admin.redis_expire'));
        }

        if (count($auth) > 1) {
            $flag = self::getAuth($auth, $GroupMenu);
            if ($flag) {
                return false;
            }
        } else {
            if (empty($auth) || !in_array($auth[0]['id'], $GroupMenu)) {
                return false;
            }
        }
        return true;
    }

    /**
     * @Title: getNavP
     * @Description: todo(一级菜单)
     * @Author: liu tao
     * @Time: xxx
     * @param $parent_id
     * @param $where
     * @return string
     */
    public static function getNavP($parent_id,$where){

        //判断该操作权限是否存在
        if(self::$redis->exists(config('admin.session_admin_auth_check_navP') . self::$adminId . http_build_query($where))){
            $auth =json_decode(self::$redis->get(config('admin.session_admin_auth_check_navP')  . self::$adminId. http_build_query($where)),true);
        }else{
            $model =  new Menu();
            $auth =$model->where($where)->select()->toArray();
            self::$redis->set(config('admin.session_admin_auth_check_navP') . self::$adminId . http_build_query($where),json_encode($auth), config('admin.redis_expire'));
        }

        if (count($auth) > 1) {
            $flag = self::getAuth($auth, $parent_id, 2);
            if ($flag) {
                return '';
            }
            return 'active';
        }

        if (count($auth) > 0) {
            if ($auth[0]['level'] == 3) {
                $model =  new Menu();
                $menu = $model->field('parent_id')->find($auth[0]['parent_id']);
                if ($parent_id == $menu->parent_id) {
                    return 'active';
                }
            }
            if ($auth[0]['level'] == 2) {
                if ($parent_id == $auth[0]['parent_id']) {
                    return 'active';
                }
            }

        }
        return '';
    }

    /**
     * @Title: getAuth
     * @Description: todo(获取权限)
     * @Author: liu tao
     * @Time: xxx
     * @param $auth
     * @param $GroupMenu
     * @param int $status
     * @return bool
     */
    private static function getAuth($auth, $GroupMenu, $status = 1)
    {
        $flag = true;
        $getParams = request()->param();
        foreach ($auth as $key => $val) {
            if (empty($val['params'])) {
                break;
            }

            $params = array_filter(explode('&', $val['params']));
            foreach ($params as $v) {
                $param = array_filter(explode('=', $v));

                if ($status === 1) {
                    $str = isset($param[0]) && isset($param[1]) && isset($getParams[$param[0]]) && $getParams[$param[0]] == $param[1] && in_array($val['id'], $GroupMenu);
                }

                if ($status === 2) {
                    $str = isset($param[0]) && isset($param[1]) && isset($getParams[$param[0]]) && $getParams[$param[0]] == $param[1] && $GroupMenu == $val['parent_id'];
                }

                if ($str) {
                    $flag = false;
                    break;
                }
            }
        }
        return $flag;
    }
}