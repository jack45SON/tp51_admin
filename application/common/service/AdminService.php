<?php

namespace app\common\service;

use app\common\action\AdminAction;
use app\common\repository\AdminRepository;
use app\common\validate\Admin;

class AdminService extends BaseService
{
    /**
     * QuestionService constructor.
     * @param $model
     * @param array $data
     */
    public function __construct($model)
    {
        $validate     = new Admin();
        $action       = new AdminAction($model, $validate);
        $repository   = new AdminRepository($model);
        parent::__construct($repository,$action);
    }
}
