<?php
/**
 * Created by PhpStorm.
 * Name: common.php
 * User: jackson
 * Date: 2018/4/9
 * Time: 下午1:23
 */

if (!function_exists('authAction')) {
    /**
     * @Title: authTopBtn
     * @Description: todo(操作按钮权限)
     * @param string $rule
     * @param string $cationType
     * @param string || array $param
     * @return string
     * @author duqiu
     * @date 2016-5-14
     */
    function authAction($rule, $title, $actionType = 'create', $isPageTable = true, $param = '', $height = 600)
    {
        if ($isPageTable) {
            $actionTypes = [
                'create'        => "<a onclick='pageTable(\"$title\",\"$rule\",\"$param\",$height);'>" . lang('add') . "</a>",
                'import'        => "<a onclick='pageTable(\"$title\",\"$rule\",\"$param\",$height);'>" . lang('import') . "</a>",
                'edit'          => "<a class='btn btn-info btn-xs' onclick='pageTable(\"$title\",\"$rule\",\"$param\",$height);'>" . lang('edit') . "</a>",
                'detail'        => "<a class='btn btn-info btn-xs' onclick='pageTable(\"$title\",\"$rule\",\"$param\",$height);'>" . lang('detail') . "</a>",
                'setPrivilege'  => "<a class='btn btn-info btn-xs' onclick='pageTable(\"$title\",\"$rule\",\"$param\",$height);'>" . lang('setPrivilege') . "</a>",
                'delete'        => "<a class='btn btn-danger btn-xs' onclick='deleteData(\"$rule\",$param);'>" . lang('delete') . "</a>",
                'restore'       => "<a class='btn btn-success btn-xs' onclick='deleteData(\"$rule\",$param);'>" . lang('restore') . "</a>",

                'content'       => "<a class='btn btn-info btn-xs' onclick='pageTable(\"$title\",\"$rule\",\"$param\",$height);'>" . lang('content') . "</a>",
            ];
        } else {
            $actionTypes = [
                'create'           => "<a href='" . url($rule, $param) . "'>" . lang('add') . "</a>",
                'recycle'       => "<a href='" . url($rule, $param) . "'>" . lang('recycle') . "</a>",
                'list'          => "<a href='" . url($rule, $param) . "'>" . lang('list') . "</a>",
                'import'        => "<a href='" . url($rule, $param) . "'>" . lang('import') . "</a>",
                'edit'          => "<a class='btn btn-info btn-xs' href='" . url($rule, $param) . "'>" . lang('edit') . "</a>",
                'detail'        => "<a class='btn btn-primary btn-xs' href='" . url($rule, $param) . "'>" . lang('detail') . "</a>",
                'setPrivilege'  => "<a class='btn btn-info btn-xs' href='" . url($rule, $param) . "'>" . lang('setPrivilege') . "</a>",
            ];
        }
        $adminId = session(config('admin.session_admin_id'), '', config('admin.session_admin_scope'));
        if ($adminId == 1 || authCheck($rule)) {
            $result = $actionTypes[$actionType];
        } else {
            $result = '';
        }
        return $result;
    }
}


if (!function_exists('authCheck')) {

    /**
     * @Title: authCheck
     * @Description: todo(操作权限判断)
     * @Author: liu tao
     * @param $rule
     * @param $adminId
     * @return bool
     */
    function authCheck($rule)
    {
        $data = explode('/', explode('?', $rule)[0]);
        $where = [
            'module'        => $data[1],
            'controller'    => $data[2],
            'action'        => $data[3],
            'status'        => 1,
        ];
        $adminId = session(config('admin.session_admin_id'), '', config('admin.session_admin_scope'));
        $redis = redis(config('admin.admin_redis_select'));
        $Auth = new \app\admin\lib\Auth($adminId,$redis);
        $GroupMenu = json_decode($redis->get(config('admin.session_admin_auth') . $adminId),true);
        return $Auth::authCheck($GroupMenu,$where);
    }
}

if (!function_exists('getNavP')) {
    /**
     * @Title: getNavP
     * @Description: todo(一级菜单)
     * @Author: liu tao
     * @Time: xxx
     * @param $parent_id
     * @param int $is_article
     * @return string
     */
    function getNavP($parent_id)
    {
        $where = [
            'module'        => strtolower(request()->module()),
            'controller'    => strtolower(request()->controller()),
            'action'        => strtolower(request()->action()),
        ];

        $adminId = session(config('admin.session_admin_id'), '', config('admin.session_admin_scope'));
        $redis = redis(config('admin.admin_redis_select'));
        $Auth = new \app\admin\lib\Auth($adminId,$redis);
        return $Auth::getNavP($parent_id,$where);
    }
}

if (!function_exists('getNav')) {
    /**
     * @Title: getNav
     * @Description: todo(二级，三级菜单)
     * @Author: liu tao
     * @Time: xxx
     * @param $id
     * @param int $is_article
     * @return string
     */
    function getNav($id)
    {

        $adminId = session(config('admin.session_admin_id'), '', config('admin.session_admin_scope'));
        $redis = redis(config('admin.admin_redis_select'));
        //判断该权限是否存在
        if($redis->exists(config('admin.session_admin_auth_check_nav') . $adminId . $id)){
            $auth =json_decode($redis->get(config('admin.session_admin_auth_check_nav')  . $adminId. $id),true);
        }else{
            $model =  new \app\admin\model\Menu();
            $auth = $model->find($id);
            $redis->set(config('admin.session_admin_auth_check_nav') . $adminId . $id,json_encode($auth), config('admin.redis_expire'));
        }

        $getParams = request()->param();
        if ($auth['level'] === 2) {

            if ($auth['module'] === strtolower(request()->module())
                && $auth['controller'] === strtolower(request()->controller())
                && $auth['action'] === strtolower(request()->action())) {

                if ($auth['params']) {
                    $flag = true;
                    $params = array_filter(explode('&', $auth['params']));
                    foreach ($params as $v) {
                        $param = array_filter(explode('=', $v));
                        if (isset($param[0]) && isset($param[1]) && isset($getParams[$param[0]]) && $getParams[$param[0]] == $param[1]) {
                            $flag = false;
                            break;
                        }
                    }
                    if ($flag) {
                        return '';
                    }
                }
                return 'current-page';
            }

            //level = 3
            $where = [
                'module' => strtolower(request()->module()),
                'controller' => strtolower(request()->controller()),
                'action' => strtolower(request()->action()),
                'parent_id' => $id
            ];

            //判断该权限是否存在
            if($redis->exists(config('admin.session_admin_auth_check_nav') . $adminId . http_build_query($where))){
                $_auth =json_decode($redis->get(config('admin.session_admin_auth_check_nav')  . $adminId. http_build_query($where)),true);
            }else{
                $model =  new \app\admin\model\Menu();
                $_auth = $model->where($where)->select()->toArray();
                $redis->set(config('admin.session_admin_auth_check_nav') . $adminId . http_build_query($where),json_encode($auth), config('admin.redis_expire'));
            }

            if (count($_auth) == 1) {
                $GroupMenu = json_decode($redis->get(config('admin.session_admin_auth') . $adminId),true);
                if (in_array($_auth[0]['id'], $GroupMenu)) {
                    return 'current-page';
                }
            }

        }
        return '';
    }
}



if(!function_exists('SearchWhere')) {
    function SearchWhere($data=[],$no_keys=[]) {
        $return['order'] = '';
        $return['where'] = [];
        $default_keys = ['export_type', 'limit', '_pagination', 'page', '_sort', 'is_export'];
        $no_keys = $no_keys ? array_unique(array_merge($no_keys, $default_keys)) : $default_keys;
        if ($data) {
            foreach($data as $w_k=>$w_v){
                if(strstr($w_k,'other^')){
                    $value = explode('=',$w_v);
                    if(count($value) == 2){
                        $return = SearchWhereFields($value[0],$value[1],$return);
                    }
                }else{
                    if(!in_array($w_k,$no_keys)) {

                        $w_v = is_array($w_v)?$w_v:trim($w_v);
                        if ($w_v) {
                            if ($w_k == 'field') {
                                //排序
                                $return['order'] = [$w_v => $data['_sort']];
                            } else {
                                //查询条件
                                $return = SearchWhereFields($w_k,$w_v,$return);
                            }
                        }
                    }
                }
            }
        }
        return $return;
    }
}
if(!function_exists('SearchWhereFields')) {
    function SearchWhereFields($w_k,$w_v,$return = []) {
        $op         = 'eq';
        $condition  = '';
        $value_type = explode('__', $w_k);
        if (isset($value_type[1])) {
            $value = str_replace('^', '.', $value_type[0]);
            $field = $value;
            if (in_array($value_type[1], ['lt', 'gt', 'elt', 'egt', 'in', 'not in', 'eq'])) {
                $op         = $value_type[1];
                $condition  = $w_v;
            } else if (in_array($value_type[1], ['like%', '%like', '%like%', 'like'])) {
                $op = 'like';
                switch ($value_type[1]) {
                    case 'like%':
                        $condition = $w_v . '%';
                        break;
                    case '%like':
                        $condition = '%' . $w_v;
                        break;
                    default:
                        $condition = '%' . $w_v . '%';
                        break;
                }
            }
        } else {
            $value = str_replace('^', '.', $w_k);
            $return['where'][] = $w_v;
            $field      = $value;
            $op         = 'eq';
            $condition  = $w_v;
        }
        $return['where'][] = [$field, $op, $condition];
        return $return;
    }
}



if (!function_exists('yesOrNo')) {
    /**
     * @Title: yesOrNo
     * @Description: todo()
     * @Author: liu tao
     * @Time: 2019/3/18 下午2:29
     * @param $field
     * @param $value
     * @param $id
     * @param bool $flag
     * @return string
     */
    function yesOrNo($field, $value, $id, $flag = true)
    {
        if ($flag) {
            if ($value) {
                $class = 'btn-success';
                $name = $field == 'status'?'正常':'是';
            } else {
                $class = 'btn-danger';
                $name = $field == 'status'?'禁止':'否';
            }
            $btn = '<button type="button" class="btn ' . $class . ' btn-xs" data-id="' . $id . '" data-field="' . $field . '" data-value="' . $value . '">' . $name . '</button>';
            $str = '<span class="edit_radio_btn">' . $btn . '</span>';
        } else {
            $str = '<span class="editor_field" data-id="' . $id . '" data-field="' . $field . '" data-value="' . $value . '">' . $value . '</span>';
        }
        return $str;
    }
}


if (!function_exists('fieldSort')) {
    /**
     * @Title: fieldSort
     * @Description: todo()
     * @Author: liu tao
     * @Time: xxx
     * @param $field
     * @return string
     */
    function fieldSort($field){
        $sort = '';
        if($field == trim(input('get.field'))){
            if(trim(input('get._sort')) == 'asc'){
                $sort .= 'asc';
            }else{
                $sort .= 'desc';
            }
        }
        return 'class="sortable both '.$sort.'" data-field="'.$field.'"';
    }
}



