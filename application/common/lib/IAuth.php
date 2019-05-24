<?php

namespace app\common\lib;

/**
 * IAuth相关
 * Class IAuth
 */
class IAuth
{

    /**
     * 设置密码
     * @param string $data
     * @return string
     */
    public static function setPassword($password, $encrypt)
    {
        return md5($password . md5($encrypt));
    }
}