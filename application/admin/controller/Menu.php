<?php

namespace app\admin\controller;

class Menu extends Base
{
    public static $model;

    public function initialize()
    {
        parent::initialize();
        self::$model = model('Menu');
    }

    /**
     * @Title: index
     * @Description: todo(菜单列表)
     * @Author: liu tao
     * @return mixed
     */
    public function index()
    {
        $menu = self::$model
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
            $child1 = self::$model
                ->where('parent_id','in',$arr)
                ->order('sort asc')
                ->select()->toArray();
            $arr1 = [];
            foreach ($child1 as $v){
                $arr1[]= $v['id'];
            }
            if($arr1){
                $child2 = self::$model
                    ->where('parent_id','in',$arr1)
                    ->order('sort asc')
                    ->select()->toArray();
            }
        }

        $_data = array_merge($parent['data'],$child1,$child2);
        $data = self::$model->getTree($_data);
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
    public function add()
    {
        if (request()->isAjax() && request()->isPost()) {
            return $this->save();
        }
        $where[] = ['level','neq',3];
        $parent = self::$model->getMenusByCondition($where);
        $parent = self::$model->getTree($parent);
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
            $parent = self::$model->getMenusByCondition($where);
            $parent = self::$model->getTree($parent);
            $data = self::$model->find($id);
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
            $validate = validate('Menu');
            $is_update = true;

            if (isset($data['id']) && $data['id'] > 0) {

                $children = $this->getChild($data['id']);
                if(in_array($data['parent_id'],$children) || $data['parent_id']==$data['id']){
                    return show(-1, '父类选择有误');
                }

                $where[] = ['id', 'neq', $data['id']];
                $data['update_admin'] = $this->adminId;

            } else {
                $is_update = false;
                $data['create_admin'] = $this->adminId;
            }

            //当方法名存在的时候，判断url地址是否已存在
            if($data['action'] && $data['params']) {
                $where[] = ['module', 'eq', $data['module']];
                $where[] = ['controller', 'eq', $data['controller']];
                $where[] = ['action', 'eq', $data['action']];
                $where[] = ['params', 'eq', $data['params']];

                $count = self::$model->where($where)->count();
                if ($count) {
                    return show(-1, lang('controller_action_unique'));
                }
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

    private function getChild($id)
    {
        $data = self::$model->select();
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
