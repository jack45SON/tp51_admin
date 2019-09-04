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
        $param = input('get.');
        $where = SearchWhere($param);
        $pageNum = input('_pagination',5);
        $data = $this->model
            ->where($where['where'])
            ->order($where['order'])
            ->paginate($pageNum,false,['query'=>request()->param()]);

        return $this->fetch('', [
            'data'          => $data,
            'count'         => $data->total(),
            'page'          => $data->render(),
            'listRows'      => $data->listRows(),
            'total'         => count($data)
        ]);
    }

    /**
     * @Title: add
     * @Description: todo(添加)
     * @Author: liu tao
     * @Time: xxx
     * @return mixed|\think\response\Json
     */
    public function create(){
       
        if (request()->isAjax() && request()->isPost()) {
            $data = request()->param();
            return $this->service->add($data);
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
            $data = request()->param();
            return $this->service->edit($data);
        }

        $id = request()->param('id');
        if ($id) {
            $data = $this->service->getDetail($id);
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
