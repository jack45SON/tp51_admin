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

    public static function __encrypt($string, $operation, $key = '')
    {
        if (empty($key)) {
            $key = 'mine_blog';
        }
        $key = md5($key);
        $key_length = strlen($key);
        $string = $operation == 'D' ? base64_decode($string) : substr(md5($string . $key), 0, 8) . $string;

        $string_length = strlen($string);
        $rnd_key = $box = array();
        $result = '';
        for ($i = 0; $i <= 255; $i++) {
            $rnd_key[$i] = ord($key[$i % $key_length]);
            $box[$i] = $i;
        }
        for ($j = $i = 0; $i < 256; $i++) {
            $j = ($j + $box[$i] + $rnd_key[$i]) % 256;
            $tmp = $box[$i];
            $box[$i] = $box[$j];
            $box[$j] = $tmp;
        }

        for ($a = $j = $i = 0; $i < $string_length; $i++) {
            $a = ($a + 1) % 256;
            $j = ($j + $box[$a]) % 256;
            $tmp = $box[$a];
            $box[$a] = $box[$j];
            $box[$j] = $tmp;
            $result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
        }
        if ($operation == 'D') {
            if (substr($result, 0, 8) == substr(md5(substr($result, 8) . $key), 0, 8)) {
                return substr($result, 8);
            } else {
                return '';
            }
        } else {
            return str_replace('=', '', base64_encode($result));
        }
    }

}