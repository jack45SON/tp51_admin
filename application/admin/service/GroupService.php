<?php

namespace app\admin\service;

use app\admin\action\BaseAction;
use app\admin\repository\BaseRepository;
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
        $this->action       = new BaseAction($model, $validate);
        $this->repository   = new BaseRepository($model);
        parent::__construct($this->repository,$this->action);
    }
}
