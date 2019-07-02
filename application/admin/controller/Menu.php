<?php

namespace app\admin\controller;

use app\admin\service\MenuService;

class Menu extends Base
{
    public function initialize()
    {
        parent::initialize();
        $this->model = new \app\admin\model\Menu();
        $this->service = new MenuService($this->model);
    }

    /**
     * @Title: index
     * @Description: todo(菜单列表)
     * @Author: liu tao
     * @return mixed
     */
    public function index()
    {
        $menu = $this->model
            ->where('level','eq',1)
            ->order('sort asc')
            ->paginate(1,false,['query'=>request()->param()]);
        $parent = $menu->toArray();
        $arr=[];
        foreach ($parent['data'] as $v){
            $arr[]= $v['id'];
        }

        $child1 = [];
        $child2 = [];
        if($arr){
            $child1 = $this->model
                ->where('parent_id','in',$arr)
                ->order('sort asc')
                ->select()->toArray();
            $arr1 = [];
            foreach ($child1 as $v){
                $arr1[]= $v['id'];
            }
            if($arr1){
                $child2 = $this->model
                    ->where('parent_id','in',$arr1)
                    ->order('sort asc')
                    ->select()->toArray();
            }
        }

        $_data = array_merge($parent['data'],$child1,$child2);
        $data = $this->model->getTree($_data);
        return $this->fetch('', [
            'data'      => $data,
            'count'         => $menu->total(),
            'page'          => $menu->render(),
        ]);
    }

    /**
     * @Title: add
     * @Description: todo(菜单添加页面)
     * @Author: liu tao
     * @return mixed
     */
    public function create()
    {
        if (request()->isAjax() && request()->isPost()) {
            return $this->save();
        }
        $where[] = ['level','neq',3];
        $parent = $this->model->getMenusByCondition($where);
        $parent = $this->model->getTree($parent);
        return $this->fetch('',['parent'=>$parent]);
    }

    /**
     * @Title: edit
     * @Description: todo(菜单编辑页面)
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
            $where[] = ['level','neq',3];
            $parent = $this->model->getMenusByCondition($where);
            $parent = $this->model->getTree($parent);
            $data = $this->model->find($id);
            return $this->fetch('', [
                'data'      => $data,
                'parent'    => $parent,
            ]);
        } else {
            exception(lang('request_illegal'));
        }
    }

    /**
     * @Title: save
     * @Description: todo(提交创建、修改菜单信息)
     * @Author: liu tao
     */
    private function save()
    {
        if (request()->isAjax() && request()->isPost()) {
            $data = input('post.');
            $is_update = true;
            $scene = 'add';
            $alone = false;
            if (isset($data['id']) && $data['id'] > 0) {
                //单独设置修改时
                if(isset($data['field'])&&$data['field']){
                    $data[$data['field']]=$data['value'];
                    $scene = 'edit';
                    $alone = true;
                }else{
                    $children = $this->getChild($data['id']);
                    if(in_array($data['parent_id'],$children) || $data['parent_id']==$data['id']){
                        return show(-1, '父类选择有误');
                    }
                    $where[] = ['id', 'neq', $data['id']];
                }
            } else {
                $is_update = false;
            }

            //当方法名存在的时候，判断url地址是否已存在
            if(isset($data['action']) && $data['action']) {
                $where[] = ['module', 'eq', $data['module']];
                $where[] = ['controller', 'eq', $data['controller']];
                $where[] = ['action', 'eq', $data['action']];
                if(isset($data['params']) && $data['params']){
                    $where[] = ['params', 'eq', $data['params']];
                }

                $count = $this->model->where($where)->count();
                if ($count) {
                    return show(-1, lang('controller_action_unique'));
                }
            }

            if($is_update){
                $result = $this->service->edit($data,$scene,$alone);
            }else{
                $result = $this->service->add($data,$scene,$alone);
            }
            if ($result['status'] > 0) {
                session(config('admin.session_admin_auth') . $this->adminId,null,config('admin.session_admin_scope'));
                session(config('admin.session_admin_menu'),null,config('admin.session_admin_scope'));
                return show(1, lang('action_success'));
            } else {
                return show(-1, $result['message']);
            }
        } else {
            return exception(lang('request_illegal'));
        }
    }

    private function getChild($id)
    {
        $data = $this->model->select();
        return $this->_child($data, $id);
    }

    private function _child($data, $parent_id = 0, $isClear = TRUE)
    {
        static $ret = array();
        if ($isClear)
            $ret = array();
        foreach ($data as $k => $v) {
            if ($v['parent_id'] == $parent_id) {
                $ret[] = $v['id'];
                $this->_child($data, $v['id'], FALSE);
            }
        }
        return $ret;
    }
}
