<?php

namespace app\common\model;

class Attach extends Base
{

    protected $pk='id';

    // 表名
    protected $table = 'attach';

    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'date';
    protected $dateFormat = 'Y-m-d H:i:s';
}