<?php

namespace app\admin\controller;

use app\admin\service\TypeService;

class Type extends Base
{
    public function initialize()
    {
        parent::initialize();
        $this->model = new \app\admin\model\Type();
        $this->service = new TypeService($this->model);
    }
}
