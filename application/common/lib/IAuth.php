<?php
/**
 * Created by PhpStorm.
 * User: baidu
 * Date: 17/7/28
 * Time: 上午12:27
 */

namespace app\common\lib;

/**
 * IAuth相关
 * Class IAuth
 */
class IAuth
{

    private static $password;
    private static $sign;
    private static $break_sign;
    private static $key;

    public function __construct($key = '')
    {
        self::$key = $key;
    }

    /**
     * @Title: setPassword
     * @Description: todo(设置密码)
     * @Author: liu tao
     * @Time: xxx
     * @param string $pwd
     * @param string $salt
     *
     */
    public static function setPassword(string $pwd, string $salt)
    {
        self::$password = md5($pwd . md5($salt));
    }

    /**
     * @Title: getPassword
     * @Description: todo(获得密码)
     * @Author: liu tao
     * @Time: xxx
     * @return string
     */
    public static function getPassword(): string
    {
        return self::$password;
    }

    /**
     * @Title: setSign
     * @Description: todo(设置签名)
     * @Author: liu tao
     * @Time: 2019/6/6 上午10:43
     * @param $data
     */
    public static function setSign($data)
    {
        if (is_array($data)) {
            // 按字段排序
            ksort($data);
            // 拼接字符串数据  &
            $string = http_build_query($data);
        } else {
            $string = $data;
        }
        //通过aes来加密
        $sign = self::encrypt($string, 'E');
        //因为加密后字符串中有+在传参过程中会自动转为空白，这里做替换处理，解密之前再替换回来
        $sign = str_replace("+","%2B",$sign);
        self::$sign = $sign;
    }

    /**
     * @Title: getSign
     * @Description: todo(获取签名)
     * @Author: liu tao
     * @Time: xxx
     * @return mixed
     */
    public static function getSign(): string
    {
        return self::$sign;
    }

    /**
     * @Title: breakSign
     * @Description: todo(解开签名)
     * @Author: liu tao
     * @Time: xxx
     * @param $sign
     */
    public static function breakSign(string $sign)
    {
        //替换回来进行解密
        $break_sign = str_replace("%2B","+",$sign);
        self::$break_sign = self::encrypt($break_sign, 'D');
    }

    /**
     * @Title: getSign
     * @Description: todo(获取解开签名)
     * @Author: liu tao
     * @Time: xxx
     * @return mixed
     */
    public static function getBreakSign(): string
    {
        return self::$break_sign;
    }

    /**
     * @Title: encrypt
     * @Description: todo(aes)
     * @Author: liu tao
     * @Time: xxx
     * @param $string
     * @param $operation
     * @param string $key
     * @return bool|mixed|string
     */
    private static function encrypt(string $string, string $operation): string
    {
        $key = md5(self::$key);
        $key_length = strlen($key);
        $string = $operation == 'D' ? base64_decode($string) : substr(md5($string . $key), 0, 8) . $string;

        $string_length = strlen($string);
        $rndkey = $box = array();
        $result = '';
        for ($i = 0; $i <= 255; $i++) {
            $rndkey[$i] = ord($key[$i % $key_length]);
            $box[$i] = $i;
        }
        for ($j = $i = 0; $i < 256; $i++) {
            $j = ($j + $box[$i] + $rndkey[$i]) % 256;
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