<?php

namespace app\admin\controller;

use app\admin\model\AdminGroup;
use app\admin\model\GroupMenu;
use app\admin\service\GroupService;
use think\Db;

class Group extends Base
{
    public function initialize()
    {
        parent::initialize();
        $this->model = new \app\admin\model\Group();
        $this->service = new GroupService($this->model);
    }

    /**
     * @Title: index
     * @Description: todo(管理组列表)
     * @Author: liu tao
     * @return mixed
     */
    public function index()
    {
//        $data = $this->model->alias('pg')
//            ->field('pg.*')
//            ->with(['adminGroup' => function ($query) {
//                $query->alias('ag')->field('ag.*,ai.nickname')
//                    ->join('admin_info ai','ai.admin_id = ag.admin_id','left');
//            }])
//            ->select();

        $data = $this->model->alias('pg')
            ->field('pg.*')
            ->with(['adminGroup' => function ($query) {
                $query->alias('ag')->field('ag.*,a.name,a.nickname')
                    ->join('admin a','a.id = ag.admin_id','left');
            }])
            ->select();
        return $this->fetch('',compact('data'));
    }

    /**
     * @Title: add
     * @Description: todo(管理组添加页面)
     * @Author: liu tao
     * @return mixed
     */
    public function create()
    {
        if (request()->isAjax() && request()->isPost()) {
            $data = input('post.');
            $result = $this->service->add($data);
            return show($result['status'],$result['message']);
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
            $data = input('post.');
            $result = $this->service->edit($data);

            //删除改组下的用户权限缓存
            $this->clear($data['id']);
            return show($result['status'],$result['message']);
        }
        $id = request()->param('id');
        if ($id) {
            $data = $this->model->find($id);
            return $this->fetch('',compact('data'));
        } else {
            exception(lang('request_illegal'));
        }
    }

    /**
     * @Title: setPrivilege
     * @Description: todo(设置管理组权限)
     * @Author: liu tao
     */
    public function setPrivilege()
    {
        $GroupMenuModel = new GroupMenu();
        if(request()->isAjax() && request()->isPost()){
            $id  = input('post.id');
            $ids    = input('post.ids');
            if($ids && $id){
                Db::startTrans();
                $del = $GroupMenuModel->where('group_id','eq',$id)->delete();
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
                    $res = $GroupMenuModel->saveAll($data);
                } catch (\Exception $e) {
                    Db::rollback();
                    return show(-1, $e->getMessage());
                }

                if ($res) {
                    Db::commit();

                    //删除改组下的用户权限缓存
                    $this->clear($id);
                    return show(1, lang('action_success'));
                } else {
                    Db::rollback();
                    return show(-1, lang('action_fail'));
                }
            }else{
                return exception(lang('request_illegal'));
            }
        }else{
            $id         = request()->param('id');
            $MenuModel  = new \app\admin\model\Menu();
            $menu_ids   = $GroupMenuModel->where('group_id','eq',$id)->column('menu_id');
            $menu       = $MenuModel->select();
            $priMenus   = $MenuModel->getTree($menu);
            return $this->fetch('set',[
                'priMenus'         => $priMenus,
                'group_id'         => $id,
                'menu_ids'         => $menu_ids,
            ]);
        }
    }

    private function clear($group_id){
        //删除改组下的用户权限缓存
        $AdminGroupModel = new AdminGroup();
        $adminIds = $AdminGroupModel->where('group_id','eq',$group_id)->column('admin_id');
        $AdminModel = new \app\admin\model\Admin();
        $redis = redis(config('admin.admin_redis_select'));
        $AdminModel->clearRedis($adminIds,$redis);
        return true;
    }
}
