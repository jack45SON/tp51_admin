<?php

namespace app\admin\controller;

use app\common\lib\IAuth as IAuth;
use think\Db;

class Admin extends Base
{
    public static $model;

    public function initialize()
    {
        parent::initialize();
        self::$model = model('admin');
    }

    /**
     * @Title: index
     * @Description: todo(管理员列表)
     * @Author: liu tao
     * @return mixed
     */
    public function index()
    {
        $admin = self::$model->paginate(20);
        return $this->fetch('', [
            'data'      => $admin,
            'count'     => $admin->total(),
            'page'      => $admin->render(),
        ]);
    }

    /**
     * @Title: add
     * @Description: todo(管理员添加页面)
     * @Author: liu tao
     * @return mixed
     */
    public function add()
    {
        if (request()->isAjax() && request()->isPost()) {
            return $this->save();
        }

        $group = model('group')->select();
        return $this->fetch('',['group'=>$group]);

    }

    /**
     * @Title: edit
     * @Description: todo(管理员编辑页面)
     * @Author: liu tao
     * @return mixed
     */
    public function edit()
    {
        if (request()->isAjax() && request()->isPost()) {
            return $this->save();
        }

        $id = request()->param('id');
        if ($id) {
            $admin = self::$model->with('adminInfo')->find($id);
            $adminGroup = $admin->adminGroup()->where('admin_id','eq',$id)->column('group_id');
            $group = model('Group')->select();
            return $this->fetch('', [
                'data'          => $admin,
                'group'         => $group,
                'adminGroup'    => $adminGroup
            ]);
        } else {
            exception(lang('request_illegal'));
        }
    }

    /**
     * @Title: detail
     * @Description: todo(管理员详细信息页面)
     * @Author: liu tao
     * @return mixed|\think\response\Json
     */
    public function detail()
    {
        if (request()->isAjax() && request()->isPost()) {
            $data = input('post.');
            $is_update = true;
            //id存在且不为0，则是修改密码，否则新增管理用户
            if (isset($data['id']) && $data['id'] > 0) {
                $data['update_admin']    = $this->adminId;
            } else {
                $is_update = false;
                $data['create_admin']    = $this->adminId;
            }

            try {
                $id = model('AdminInfo')->addEdit($data,'id' ,$is_update);
            } catch (\Exception $e) {
                return show(-1, $e->getMessage());
            }

            if ($id) {
                return show(1, lang('action_success'));
            } else {
                return show(-1, lang('action_fail'));
            }
        } else {
            $id = request()->param('id');
            if ($id) {
                $adminInfo = model('AdminInfo')->where('id', $id)->find();
                return $this->fetch('', [
                    'data' => $adminInfo,
                    'id' => $id
                ]);
            } else {
                exception(lang('request_illegal'));
            }
        }
    }

    /**
     * @Title: save
     * @Description: todo(提交创建、修改管理员账号信息)
     * @Author: liu tao
     */
    private function save()
    {
        if (request()->isAjax() && request()->isPost()) {
            $data = input('post.');
            $validate = validate('Admin');
            $is_update = true;
            $group_ids = $data['group_ids'];
            //id存在且不为0，则是修改密码，否则新增管理用户
            if (isset($data['id']) && $data['id'] > 0) {
                $up_data = [
                    'id'                => $data['id'],
                    'name'              => trim($data['name']),
                    'password'          => trim($data['password']),
                    'res_password'      => trim($data['res_password']),
                    'old_password'      => trim($data['old_password']),
                    'status'            => $data['status'],
                ];

                if($up_data['id'] == 1 && $up_data['status'] !=config('admin.admin_status_normal')){
                    return show(-1, lang('supper_stop'));
                }

                if ($up_data['name'] || $up_data['password']) {
                    $up_data['update_admin']  = $this->adminId;
                    //修改用户名
                    if ($up_data['name']) {
                        $admin = self::$model->where([
                            'name' => $up_data['name']
                        ])->select();
                        //判断用户名是否唯一
                        if (count($admin) != 1 || $admin[0]['id'] != $up_data['id']) {
                            return show(-1, lang('name_unique'));
                        }
                    } else {
                        unset($up_data['name']);
                    }

                    //修改密码
                    if ($up_data['password']) {
                        //判断2次密码是否一样
                        if ($up_data['password'] !== $up_data['res_password']) {
                            return show(-1, lang('res_password_confirm'));
                        }
                        $admin  = self::$model->find($up_data['id']);
                        $pwd    = IAuth::setPassword($up_data['old_password'],$admin['encrypt']);
                        //判断旧密码是否正确
                        if ($pwd != $admin['password']) {
                            return show(-1, lang('old_password_right'));
                        }
                        $up_data['encrypt']       = createRandomStr();
                        $up_data['password']      = IAuth::setPassword($up_data['password'],$up_data['encrypt']);
                    } else {
                        unset($up_data['password']);
                    }
                    $data = $up_data;
                } else {
                    return exception(lang('request_illegal'));
                }


            } else {
                $is_update = false;
                if (!$validate->scene('add')->check($data)) {
                    return show(-1, $validate->getError());
                }
                //添加插入数据
                $data['encrypt']      = createRandomStr();
                $data['password']     = IAuth::setPassword($data['password'],$data['encrypt']);
                $data['create_admin'] = $this->adminId;
                $data['status']       = config('admin.admin_status_normal');
                $data['reg_ip']       = ipToInt(request()->ip());
            }


            Db::startTrans();
            try {
                $id = self::$model->addEdit($data,'id',$is_update);
            } catch (\Exception $e) {
                return show(-1, $e->getMessage());
            }

            //添加或者编辑成功进行管理员分组处理
            if ($id) {
                $id = $is_update?$data['id']:$id;
                $del = model('AdminGroup')->where('admin_id','eq',$id)->delete();

                if($del === false){
                    Db::rollback();
                    return show(-1, lang('action_fail'));
                }
                $adminGroup =[];
                foreach ($group_ids as $v){
                    $adminGroup[]=[
                        'group_id'      =>$v,
                        'admin_id'      =>$id
                    ];
                }
                try {
                    $id = model('AdminGroup')->saveAll($adminGroup);
                } catch (\Exception $e) {
                    Db::rollback();
                    return show(-1, $e->getMessage());
                }

                if ($id) {
                    Db::commit();
                    return show(1, lang('action_success'));
                } else {
                    Db::rollback();
                    return show(-1, lang('action_fail'));
                }
            } else {
                Db::rollback();
                return show(-1, lang('action_fail'));
            }
        } else {
            return exception(lang('request_illegal'));
        }
    }
}
