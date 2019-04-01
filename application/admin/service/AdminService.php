<?php

namespace app\admin\service;

use app\admin\action\AdminAction;
use app\admin\repository\AdminRepository;
use app\admin\validate\Admin;

class AdminService extends BaseService
{
    /**
     * QuestionService constructor.
     * @param $model
     * @param array $data
     */
    public function __construct($model)
    {
        $validate           = new Admin();
        $this->action       = new AdminAction($model, $validate);
        $this->repository   = new AdminRepository($model);
        parent::__construct($this->repository,$this->action);
    }
}
