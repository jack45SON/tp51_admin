<?php
namespace app\admin\model;

use think\model\concern\SoftDelete;

class Tag extends Base
{
    use SoftDelete;
    // 表名
    protected $table    = 'tag';

    protected $auto     = [];
    protected $insert   = ['uuid'];
    protected $update   = [];

    protected function setUuidAttr()
    {
        return createUuid($this);
    }
}