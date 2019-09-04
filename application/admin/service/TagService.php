<?php

namespace app\admin\service;

use app\admin\action\BaseAction;
use app\admin\repository\BaseRepository;
use app\admin\validate\Tag;

class TagService extends BaseService
{
    /**
     * QuestionService constructor.
     * @param $model
     * @param array $data
     */
    public function __construct($model)
    {
        $validate           = new Tag();
        $this->action       = new BaseAction($model, $validate);
        $this->repository   = new BaseRepository($model);
        parent::__construct($this->repository,$this->action);
    }
}
