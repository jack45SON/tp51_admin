<?php
/**
 * Created by PhpStorm.
 * Name: AdminUser.php
 * User: jackson
 * Date: 2018/3/21
 * Time: 上午10:42
 */

namespace app\admin\model;

class WxInfo extends Base
{

    // 表名
    protected $table = 'wx_info';

    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'date';
    protected $dateFormat = 'Y-m-d H:i:s';

    protected $pk = 'id';
}