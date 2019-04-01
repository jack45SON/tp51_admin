<?php

namespace app\common\repository;

class BaseRepository
{
    private $model;

    public function __construct($model)
    {
        $this->model = $model;
    }

    public function getDetail($data){
        $id = $data['id'];
        $result = $this->model->find($id);
        return $result;
    }
}
