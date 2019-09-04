<?php

namespace app\admin\controller;

use app\admin\service\TagService;

class Tag extends Base
{

    public function initialize()
    {
        parent::initialize();
        $this->model = new \app\admin\model\Tag();
        $this->service = new TagService($this->model);
    }
}
