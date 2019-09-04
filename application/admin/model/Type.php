<?php
namespace app\admin\model;

use think\model\concern\SoftDelete;

class Type extends Base
{
    use SoftDelete;
    // 表名
    protected $table    = 'type';

    protected $auto     = [];
    protected $insert   = ['uuid'];
    protected $update   = [];

    protected function setUuidAttr()
    {
        return createUuid($this);
    }


    protected function getTypeAttr($value)
    {
        $type = [
            1 => config('type_type_1'),
            2 => config('type_type_2'),
            3 => config('type_type_3')
        ];
        return $type[$value];
    }
}