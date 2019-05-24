<?php

namespace app\admin\service;

class BaseService
{
    protected $repository;
    protected $action;

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
    public function add($data, $scene = 'add', $alone  = false)
    {
        return $this->action->addOrEdit($data, $scene, false, $alone);
    }

    /**
     * @Title: edit
     * @Description: todo(编辑)
     * @Author: liu tao
     * @Time: 2019/3/22 下午6:34
     * @return array
     */
    public function edit($data, $scene = 'edit', $alone)
    {
        return $this->action->addOrEdit($data, $scene, true, $alone);
    }

    /**
     * @Title: getDetail
     * @Description: todo(详情)
     * @Author: liu tao
     * @Time: 2019/3/22 下午6:34
     * @return mixed
     */
    public function getDetail($data)
    {
        return $this->repository->getDetail($data);
    }

    /**
     * @Title: allSet
     * @Description: todo(批量设置)
     * @Author: liu tao
     * @Time: xxx
     * @return \think\response\Json
     */
    public function allSet($data)
    {
        return $this->action->allSet($data);
    }
}
