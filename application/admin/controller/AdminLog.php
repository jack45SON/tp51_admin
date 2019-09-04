<?php

namespace app\admin\controller;


class AdminLog extends Base
{
    public function initialize()
    {
        parent::initialize();
        $this->model = new \app\admin\model\AdminLog();
    }

    public function content(){
        $id = request()->param('id');
        if ($id) {
            $data = $this->model->field('content')->find($id);
            return $this->fetch('',[
                'data'          => $data
            ]);
        } else {
            exception(lang('request_illegal'));
        }
    }
}
