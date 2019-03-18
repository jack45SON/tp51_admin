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
    function authAction($rule, $title, $actionType = 'add', $isPageTable = true, $param = '', $height = 600)
    {
        if ($isPageTable) {
            $actionTypes = [
                'add'           => "<a onclick='pageTable(\"$title\",\"$rule\",\"$param\",$height);'>" . lang('add') . "</a>",
                'import'        => "<a onclick='pageTable(\"$title\",\"$rule\",\"$param\",$height);'>" . lang('import') . "</a>",
                'edit'          => "<a class='btn btn-info btn-xs' onclick='pageTable(\"$title\",\"$rule\",\"$param\",$height);'>" . lang('edit') . "</a>",
                'delete'        => "<a class='btn btn-danger btn-xs' onclick='deleteData(\"$rule\",$param);'>" . lang('delete') . "</a>",
                'restore'       => "<a class='btn btn-success btn-xs' onclick='deleteData(\"$rule\",$param);'>" . lang('restore') . "</a>",

            ];
        } else {
            $actionTypes = [
                'add'           => "<a href='" . url($rule, $param) . "'>" . lang('add') . "</a>",
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
        $GroupMenu = session(config('admin.session_admin_auth') . $adminId, '', config('admin.session_admin_scope'));
        $auth = model('Menu')->where($where)->select()->toArray();
        if (count($auth) > 1) {
            $flag = commonAuth($auth, $GroupMenu);
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
}

if (!function_exists('getNavP')) {
    /**
     * @Title: getNavP
     * @Description: todo()
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

        $auth = model('Menu')->where($where)->select()->toArray();

        if (count($auth) > 1) {
            $flag = commonAuth($auth, $parent_id, 2);
            if ($flag) {
                return '';
            }
            return 'active';
        }

        if (count($auth) > 0) {
            if ($auth[0]['level'] == 3) {
                $menu = model('Menu')->field('parent_id')->find($auth[0]['parent_id']);
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
}

if (!function_exists('getNav')) {
    /**
     * @Title: getNav
     * @Description: todo()
     * @Author: liu tao
     * @Time: xxx
     * @param $id
     * @param int $is_article
     * @return string
     */
    function getNav($id)
    {
        $auth = model('Menu')->find($id);
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
        }

        if ($auth['level'] === 2) {
            $where = [
                'module' => strtolower(request()->module()),
                'controller' => strtolower(request()->controller()),
                'action' => strtolower(request()->action()),
                'parent_id' => $id
            ];
            $_auth = model('Menu')->where($where)->select()->toArray();
            $adminId = session(config('admin.session_admin_id'), '', config('admin.session_admin_scope'));
            if (count($_auth) == 1) {
                $GroupMenu = session(config('admin.session_admin_auth') . $adminId, '', config('admin.session_admin_scope'));

                if (in_array($_auth[0]['id'], $GroupMenu)) {
                    return 'current-page';
                }
            }
        }
        return '';
    }
}

if (!function_exists('commonAuth')) {
    /**
     * @Title: commonAuth
     * @Description: todo()
     * @Author: liu tao
     * @param $auth
     * @param $GroupMenu
     * @param int $status
     * @return bool
     */
    function commonAuth($auth, $GroupMenu, $status = 1)
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

if (!function_exists('insertToDb')) {
    /**
     * @Title: insertToDb
     * @Description: todo(excel 导入数据库)
     * @Author: liu tao
     * @param $fields
     * @param $gp_id
     * @param $admin_id
     * @return array
     */
    function insertToDb($fields, $Model, $table)
    {
        $file = request()->file('file');
        //移到/public/uploads/excel/下
        $path = '/public/uploads/excel';
        try {
            if (!$file) {
                exception(lang('request_illegal'));
            }
            $info = $file->validate(['size' => 1024 * 1024 * 10, 'ext' => 'xlsx,xls'])->move(\Env::get('root_path') . $path);
        } catch (\Exception $e) {
            return ['status' => -1, 'message' => $e->getMessage()];
        }
        if ($info) {
            //获取上传后的文件名
            $fileName = $info->getSaveName();
            //文件路径
            $filePath = $path . '/' . $fileName;
            //获取后缀
            $extension = $info->getExtension();
            //实例化PHPExcel类
            //读取excel文件
            if ($extension == 'xlsx') {
                $objReader = \PHPExcel_IOFactory::createReader('Excel2007');
                $objPHPReader = $objReader->load(\Env::get('root_path') . $filePath, $encode = 'utf-8');
            } else if ($extension == 'xls') {
                $objReader = \PHPExcel_IOFactory::createReader('Excel5');
                $objPHPReader = $objReader->load(\Env::get('root_path') . $filePath, $encode = 'utf-8');
            }

            //读取excel文件中的第一个工作表
            $sheet = $objPHPReader->getSheet(0);
            $allRow = $sheet->getHighestRow();  //取得总行数
            $allColumn = $sheet->getHighestColumn();  //取得总列数

            $insert = [];
            $now = time();
            $flag = true;
            //从第一行开始读取数据
            for ($j = 2; $j <= $allRow; $j++) {
                //从A列读取数据
                $i = 0;
                for ($k = 'A'; $k <= $allColumn; $k++) {
                    $val = $objPHPReader->getActiveSheet()->getCell("$k$j")->getValue();
                    $data[$fields[$i]] = $val ? $val : '';

                    //年级
                    if ($fields[$i] == 'grade') {
                        $data['grade_id'] = model('grade')->where('name', '=', $val)->value('id');
                    }

                    //标签
                    if ($fields[$i] == 'tag') {
                        $data['tag_id'] = model('tag')->where('name', '=', $val)->value('id');
                    }

                    $i++;
                }
                if ($flag) {
                    $insert[] = $data;
                }
                $flag = true;
            }
            if (empty($insert)) {
                return ['status' => -1, 'message' => '导入数据为空'];
            }
            collection($insert)->chunk(2000)->each(function ($item) use ($Model) {
                $Model->isUpdate(false)->allowField(true)->saveAll($item);
            });
            return ['status' => 1, 'message' => "共导入成功：" . count($insert) . "条数据"];
        } else {
            return ['status' => -1, 'message' => '写入数据库失败'];
        }
    }
}


if (!function_exists('yesOrNo')) {
    /**
     * @Title: yesOrNo
     * @Description: todo()
     * @Author: liu tao
     * @Time: 2019/3/18 下午2:29
     * @param $column
     * @param $value
     * @param $id
     * @param bool $flag
     * @return string
     */
    function yesOrNo($column, $value, $id, $flag = true)
    {
        if ($flag) {
            if ($value) {
                $class = 'btn-success';
                $name = '是';
            } else {
                $class = 'btn-danger';
                $name = '否';
            }
            $btn = '<button type="button" class="btn ' . $class . ' btn-sm" data-id="' . $id . '" data-column="' . $column . '" data-value="' . $value . '">' . $name . '</button>';
            $str = '<p class="edit_radio_btn">' . $btn . '</p>';
        } else {
            $str = '<p class="editor_column" data-id="' . $id . '" data-column="' . $column . '" data-value="' . $value . '">' . $value . '</p>';
        }
        return $str;
    }
}

