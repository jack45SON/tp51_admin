<?php

namespace app\common\service;

class BaseService
{
    private $repository;
    private $action;

    /**
     * Base constructor.
     * @param $repository
     * @param $action
     * @param $validate
     * @param array $data
     */
    public function __construct($repository,$action)
    {
        $this->action       = $action;
        $this->repository   = $repository;
    }

    /**
     * @Title: create
     * @Description: todo(创建)
     * @Author: liu tao
     * @Time: 2019/3/22 下午6:34
     * @return array
     */
    public function create($data){
        return $this->action->createOrEdit($data);
    }

    /**
     * @Title: edit
     * @Description: todo(编辑)
     * @Author: liu tao
     * @Time: 2019/3/22 下午6:34
     * @return array
     */
    public function edit($data){
        return $this->action->createOrEdit($data,'edit',true);
    }

    /**
     * @Title: getDetail
     * @Description: todo(详情)
     * @Author: liu tao
     * @Time: 2019/3/22 下午6:34
     * @return mixed
     */
    public function getDetail($data){
        return $this->repository->getDetail($data);
    }

    /**
     * @Title: allSet
     * @Description: todo(批量设置)
     * @Author: liu tao
     * @Time: xxx
     * @return \think\response\Json
     */
    public function allSet($data){
        return $this->action->allSet($data);
    }
}
