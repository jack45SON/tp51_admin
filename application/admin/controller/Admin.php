<?php

namespace app\admin\controller;

use app\admin\service\AdminService;
use app\common\lib\IAuth as IAuth;
use think\Db;
use think\Exception;
use think\exception\PDOException;

class Admin extends Base
{
    

    public function initialize()
    {
        parent::initialize();
        $this->model = new \app\admin\model\Admin();
        $this->service = new AdminService($this->model);
    }


    /**
     * @Title: add
     * @Description: todo(管理员添加页面)
     * @Author: liu tao
     * @return mixed
     */
    public function create()
    {
        if (request()->isAjax() && request()->isPost()) {
            return $this->save();
        }

        $GroupModel = new \app\admin\model\Group();
        $group = $GroupModel->select();
        return $this->fetch('',compact('group'));

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
            $admin = $this->model->find($id);
            $adminGroup = $admin->adminGroup()->where('admin_id','eq',$id)->column('group_id');
            $GroupModel = new \app\admin\model\Group();
            $group = $GroupModel->select();
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
     * @Title: save
     * @Description: todo(提交创建、修改管理员账号信息)
     * @Author: liu tao
     */
    private function save()
    {
        if (request()->isAjax() && request()->isPost()) {
            $data = input('post.');
            $is_update = true;
            $scene = 'add';
            $alone = false;
            //id存在且不为0，则是修改密码，否则新增管理用户
            if (isset($data['id']) && $data['id'] > 0) {

                //单独设置修改时
                if(isset($data['field'])&&$data['field']){
                    $data[$data['field']]=$data['value'];
                    $alone = true;
                }
                //超级管理员禁止操作
                if($data['id'] == 1 && $data['status'] != config('admin.admin_status_normal')){
                    return show(-1, lang('supper_stop'));
                }

                if($data['status'] != config('admin.admin_status_normal')){
                    $redis = redis(config('admin.admin_redis_select'));
                    $redis->delete(config('admin.session_admin_id') . $data['id']);
                }

                //修改密码
                if (isset($data['password']) && $data['password']) {
                    $data['encrypt']       = createRandomStr();
                    IAuth::setPassword($data['password'],$data['encrypt']);
                    $data['password']      = IAuth::getPassword();
                    IAuth::setPassword($data['res_password'],$data['encrypt']);
                    $data['res_password']  = IAuth::getPassword();
                } else {
                    $scene = 'edit';
                    unset($data['password']);
                }
            } else {
                $is_update = false;
                //添加插入数据
                $data['encrypt']      = createRandomStr();
                IAuth::setPassword($data['password'],$data['encrypt']);
                $data['password']      = IAuth::getPassword();
                IAuth::setPassword($data['res_password'],$data['encrypt']);
                $data['res_password']  = IAuth::getPassword();
                $data['status']       = config('admin.admin_status_normal');
                $data['create_ip']    = ipToInt(request()->ip());
            }

            Db::startTrans();
            if($is_update){
                $result = $this->service->edit($data,$scene,$alone);
            }else{
                $result = $this->service->add($data,$scene,$alone);
            }
            //添加或者编辑成功进行管理员分组处理
            if ($result['status'] > 0) {
                if(isset($data['group_ids']) && $data['group_ids'] && !$alone){
                    $result = $this->adminGroup($result['data']['id'],$data['group_ids']);
                    if($result['status'] < 1){
                        Db::rollback();
                    }else{
                        Db::commit();
                    }
                    return show($result['status'],$result['message']);
                }else{
                    Db::commit();
                    return show(1,lang('action_success'));
                }
            } else {
                Db::rollback();
                return show(-1, $result['message']);
            }
        } else {
            return exception(lang('request_illegal'));
        }
    }

    /**
     * @Title: adminGroup
     * @Description: todo(管理员的管理组操作)
     * @Author: liu tao
     * @Time: 2019/4/1 下午4:25
     * @param $id
     * @param $group_ids
     * @return array
     */
    private function adminGroup($id,$group_ids){
        $result = [
            'status'  => -1,
            'message' => lang('action_fail'),
        ];
        //删除管理员所有分组
        $AdminGroupModel = new \app\admin\model\AdminGroup();
        $del = $AdminGroupModel->where('admin_id','eq',$id)->delete();
        if($del !== false){
            $adminGroup =[];
            foreach ($group_ids as $v){
                $adminGroup[]=[
                    'group_id'      =>$v,
                    'admin_id'      =>$id
                ];
            }
            try {
                $res = $AdminGroupModel->isUpdate(false)->allowField(true)->saveAll($adminGroup);
                if ($res) {

                    $redis = redis(config('admin.admin_redis_select'));
                    $this->model->clearRedis($id,$redis);
                    $result = ['status'  => 1, 'message'  => lang('action_success')];
                }
            } catch (Exception $e) {
                $result['message' ] = $e->getMessage();
            } catch (PDOException $e) {
                $result['message' ] = $e->getMessage();
            }
        }
        return $result;
    }

}
