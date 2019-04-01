<?php

namespace app\admin\service;

use app\admin\action\GroupAction;
use app\admin\repository\GroupRepository;
use app\admin\validate\Group;

class GroupService extends BaseService
{
    /**
     * QuestionService constructor.
     * @param $model
     * @param array $data
     */
    public function __construct($model)
    {
        $validate           = new Group();
        $this->action       = new GroupAction($model, $validate);
        $this->repository   = new GroupRepository($model);
        parent::__construct($this->repository,$this->action);
    }
}
