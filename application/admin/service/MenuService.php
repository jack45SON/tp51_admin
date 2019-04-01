<?php

namespace app\admin\service;

use app\admin\action\MenuAction;
use app\admin\repository\MenuRepository;
use app\admin\validate\Menu;

class MenuService extends BaseService
{
    /**
     * QuestionService constructor.
     * @param $model
     * @param array $data
     */
    public function __construct($model)
    {
        $validate           = new Menu();
        $this->action       = new MenuAction($model, $validate);
        $this->repository   = new MenuRepository($model);
        parent::__construct($this->repository,$this->action);
    }
}
