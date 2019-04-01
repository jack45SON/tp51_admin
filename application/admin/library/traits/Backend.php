<?php

namespace app\admin\library\traits;

trait Backend
{

    /**
     * @Title: index
     * @Description: todo(列表)
     * @Author: liu tao
     * @return mixed
     */
    public function index()
    {
        $data = $this->model
            ->paginate(50,false,['query'=>request()->param()]);

        return $this->fetch('', [
            'data'          => $data,
            'count'         => $data->total(),
            'page'          => $data->render()
        ]);
    }

    /**
     * @Title: add
     * @Description: todo(添加)
     * @Author: liu tao
     * @Time: xxx
     * @return mixed|\think\response\Json
     */
    public function add(){
       
        if (request()->isAjax() && request()->isPost()) {
            return $this->service->create();
        }
        return $this->fetch('');
    }

    /**
     * @Title: edit
     * @Description: todo(编辑)
     * @Author: liu tao
     * @Time: xxx
     * @return mixed|\think\response\Json
     */
    public function edit()
    {
        if (request()->isAjax() && request()->isPost()) {
            return $this->service->edit();
        }

        $id = request()->param('id');
        if ($id) {
            $data = $this->service->getDetail();
            return $this->fetch('',[
                'data'          => $data
            ]);
        } else {
            exception(lang('request_illegal'));
        }
    }

    /**
     * 批量下架/上架
     */
    public function allSet()
    {
        if (request()->isAjax() && request()->isPost()) {
            return $this->service->allSet();
        } else {
            return exception(lang('request_illegal'));
        }
    }
}
