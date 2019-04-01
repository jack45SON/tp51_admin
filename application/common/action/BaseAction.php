<?php

namespace app\common\action;

use think\Exception;
use think\exception\PDOException;

class BaseAction
{
    private $model;
    private $validate;
    private $result = [
        'status'    => -1,
        'message'   => '',
        'data'      => [],
    ];

    public function __construct($model,$validate)
    {
        $this->model    = $model;
        $this->validate = $validate;
    }

    /**
     * @Title: createOrEdit
     * @Description: todo(创建或者编辑数据)
     * @Author: liu tao
     * @Time: 2019/3/22 下午6:33
     * @param $data
     * @param string $scene
     * @param bool $flag
     * @return array
     */
    public function createOrEdit($data,$scene = 'create',$flag = false){

        //单独设置修改时
        if(isset($data['field'])&&$data['field']){
            $data[$data['field']]=$data['value'];
        }
        
        if (!$this->validate->scene($scene)->check($data)) {
            $this->result['message'] = $this->validate->getError();
            return show($this->result['status'], $this->result['message'], $this->result['data']);
        }

        //操作数据
        try {
            if ($this->model->insertUpdate($data,'id',$flag)) {
                $this->result['status'] = 1;
                $this->result['message'] =lang('action_success');
            } else {
                $this->result['message'] =lang('action_fail');
            }
        } catch (Exception $e) {
            $this->result['message'] = $e->getMessage();
        } catch (PDOException $e) {
            $this->result['message'] = $e->getMessage();
        }
        return show($this->result['status'],$this->result['message'],$this->result['data']);
    }

    /**
     * @Title: delete
     * @Description: todo(删除)
     * @Author: liu tao
     * @Time: xxx
     * @param $data
     * @return \think\response\Json
     */
    public function delete($data){
        $ids = explode(',',$data['ids']);
        $list = $this->model->where('id','in',$ids)->select();
        $count = 0;
        try {
            foreach ($list as $k => $v) {
                $count +=$v->delete();
            }
            if ($count) {
                $this->result['status'] = 1;
                $this->result['message'] =lang('action_success');
            } else {
                $this->result['message'] =lang('No rows were deleted');
            }
        } catch (Exception $e) {
            $this->result['message'] = $e->getMessage();
        }catch (PDOException $e) {
            $this->result['message'] = $e->getMessage();
        }
        return show($this->result['status'],$this->result['message'],$this->result['data']);
    }

    /**
     * @Title: allSet
     * @Description: todo(批量设置)
     * @Author: liu tao
     * @Time: xxx
     * @param $data
     * @return \think\response\Json
     */
    public function allSet($data){
        $ids = explode(',',$data['ids']);
        $field = $data['field'];
        $value = $data['value'];
        try {
            if ($this->model->where('id', 'in', $ids)->setField($field, $value)) {
                $this->result['status'] = 1;
                $this->result['message'] =lang('action_success');
            } else {
                $this->result['message'] =lang('action_fail');
            }
        } catch (Exception $e) {
            $this->result['message'] = $e->getMessage();
        }catch (PDOException $e) {
            $this->result['message'] = $e->getMessage();
        }
        return show($this->result['status'],$this->result['message'],$this->result['data']);
    }
}
