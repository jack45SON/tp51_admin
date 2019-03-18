<?php

namespace app\admin\controller;

use think\Db;

class Group extends Base
{
    public static $model;

    public function initialize()
    {
        parent::initialize();
        self::$model = model('Group');
    }

    /**
     * @Title: index
     * @Description: todo(管理组列表)
     * @Author: liu tao
     * @return mixed
     */
    public function index()
    {
        $data = self::$model->alias('pg')
            ->field('pg.*')
            ->with(['adminGroup' => function ($query) {
                $query->alias('ag')->field('ag.*,ai.nickname')
                    ->join('admin_info ai','ai.admin_id = ag.admin_id','left');
            }])
            ->select();
        return $this->fetch('', [
            'data'      => $data
        ]);
    }

    /**
     * @Title: add
     * @Description: todo(管理组添加页面)
     * @Author: liu tao
     * @return mixed
     */
    public function add()
    {
        if (request()->isAjax() && request()->isPost()) {
            return $this->save();
        }
        return $this->fetch();
    }

    /**
     * @Title: edit
     * @Description: todo(管理组编辑页面)
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
            $data = self::$model->find($id);
            return $this->fetch('', [
                'data'      => $data
            ]);
        } else {
            exception(lang('request_illegal'));
        }
    }

    /**
     * @Title: save
     * @Description: todo(提交创建、修改管理组信息)
     * @Author: liu tao
     */
    private function save()
    {
        if (request()->isAjax() && request()->isPost()) {
            $data = input('post.');
            $validate = validate('Group');
            $is_update = true;

            if (isset($data['id']) && $data['id'] > 0) {
                $data['update_time']  = time();
                $data['update_admin'] = $this->adminId;

            } else {
                $is_update = false;
                $data['create_time']  = time();
                $data['create_admin'] = $this->adminId;
            }

            //验证字段属性
            if (!$validate->scene('add')->check($data)) {
                return show(-1, $validate->getError());
            }

            try {
                $id = self::$model->addEdit($data,'id', $is_update);
            } catch (\Exception $e) {
                return show(-1, $e->getMessage());
            }

            if ($id) {
                return show(1, lang('action_success'));
            } else {
                return show(-1, lang('action_fail'));
            }
        } else {
            return exception(lang('request_illegal'));
        }
    }


    /**
     * @Title: setPrivilege
     * @Description: todo(设置管理组权限)
     * @Author: liu tao
     */
    public function setPrivilege()
    {
        if(request()->isAjax() && request()->isPost()){
            $id  = input('post.id');
            $ids    = input('post.ids');
            if($ids && $id){
                Db::startTrans();
                $del = model('GroupMenu')->where('group_id','eq',$id)->delete();

                if($del === false){
                    Db::rollback();
                    return show(-1, lang('action_fail'));
                }
                $data =[];
                foreach (explode(',',$ids) as $v){
                    $data[]=[
                        'group_id'  =>$id,
                        'menu_id'   =>$v
                    ];
                }


                try {
                    $id = model('GroupMenu')->saveAll($data);
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
            }else{
                return exception(lang('request_illegal'));
            }
        }else{
            $id = request()->param('id');
            $menu_ids = model('GroupMenu')->where('group_id','eq',$id)->column('menu_id');
            $menu = model("Menu")->select();
            $priMenus = model("Menu")->getTree($menu);
            return $this->fetch('set',[
                'priMenus'      => $priMenus,
                'group_id'         => $id,
                'menu_ids'         => $menu_ids,
            ]);
        }
    }
}
