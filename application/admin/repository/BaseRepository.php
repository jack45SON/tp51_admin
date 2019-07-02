<?php

namespace app\admin\repository;

class BaseRepository
{
    private $model;

    public function __construct($model)
    {
        $this->model = $model;
    }

    public function getDetail($id){
        $result = $this->model->find($id);
        return $result;
    }
}
