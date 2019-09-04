<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件

/**
 * @Title: is_debug
 * @Description: todo(调试)
 * @Author: liu tao
 * @Time: 2019/3/14 下午3:11
 * @return bool
 */
if (!function_exists('isDebug')) {
    function isDebug()
    {
        if ($_SERVER['SERVER_NAME'] == 'tp51_admin.com') {
            return true;
        } else {
            return false;
        }
    }
}

/**
 * @Title: redis
 * @Description: todo(redis配置)
 * @Author: liu tao
 * @Time: xxx
 * @param $select
 * @return Redis
 */
if (!function_exists('redis')) {

    function redis($select)
    {
        $redis = new \Redis();
        $redis->connect(\Env::get('redis.hostname', '127.0.0.1'), 6379);
        $redis->auth(\Env::get('redis.password', 12345));
        $redis->select($select);
        return $redis;
    }
}

/**
 * @Title: show
 * @Description: todo(json输出数据)
 * @Author: liu tao
 * @Time: 2019/3/14 下午3:11
 * @param $status
 * @param $message
 * @param array $data
 * @param string $url
 * @return \think\response\Json
 */
if (!function_exists('show')) {
    function show($status, $message, $data = [], $url = '')
    {
        $result = [
            'status'    => $status,
            'message'   => $message,
            'data'      => $data,
            'url'       => $url
        ];
        return json($result);
    }
}

/**
 * @Title: showApi
 * @Description: todo(通用化Api输出数据)
 * @Author: liu tao
 * @param $status
 * @param $message
 * @param array $data
 * @param int $httpCode
 * @return \think\response\Json
 */
if (!function_exists('showApi')) {
    function showApi($status, $message, $data = [], $httpCode = 200)
    {
        $result = [
            'status' => $status,
            'message' => $message,
            'data' => $data,
        ];
        return json($result, $httpCode);
    }
}

/**
 * @Title: ipToInt
 * @Description: todo(将ip转为整形，可用long2ip转为ip)
 * @Author: liu tao
 * @Time: xxx
 * @param $ip
 * @return float|int
 */
if (!function_exists('ipToInt')) {
    function ipToInt($ip)
    {
        $ipArr = explode('.', $ip);
        $num = 0;
        for ($i = 0; $i < count($ipArr); $i++) {
            $num += intval($ipArr[$i]) * pow(256, count($ipArr) - ($i + 1));
        }
        return $num;
    }
}


/**
 * @Title: random
 * @Description: todo(产生数字随机字符串)
 * @Author: liu tao
 * @Time: xxx
 * @param $length
 * @param string $chars
 * @return string
 */
if (!function_exists('random')) {
    function random($length, $chars = '0123456789')
    {
        $hash = '';
        $max = strlen($chars) - 1;
        for ($i = 0; $i < $length; $i++) {
            $hash .= $chars[mt_rand(0, $max)];
        }
        return $hash;
    }
}

/**
 * @Title: createRandomStr
 * @Description: todo(生成随机字符串)
 * @Author: liu tao
 * @Time: xxx
 * @param int $length
 * @return string
 */
if (!function_exists('createRandomStr')) {
    function createRandomStr($length = 6)
    {
        return random($length, '123456789abcdefghijklmnpqrstuvwxyzABCDEFGHIJKLMNPQRSTUVWXYZ');
    }
}

/**
 * @Title: createSn
 * @Description: todo(生成订单流水号)
 * @Author: liu tao
 * @return string
 */
if (!function_exists('createSn')) {
    function createSn()
    {
        mt_srand((double )microtime() * 1000000);
        return date("YmdHis") . str_pad(mt_rand(1, 99999), 5, "0", STR_PAD_LEFT);
    }
}

/**
 * @Title: subStrLength
 * @Description: todo(截取字符串长度)
 * @Author: liu tao
 * @Time: xxx
 * @param $str
 * @param int $length
 * @param string $default_value
 * @return string
 */
if (!function_exists('subStrLength')) {
    function subStrLength($str, $length = 10, $default_value = '...')
    {
        $str_length = strlen($str);
        if ($str_length > 0) {
            return $str_length > $length ? mb_substr($str, 0, $length, 'utf-8') . $default_value : $str;
        }
        return '';
    }
}

/**
 * @Title: getLimit
 * @Description: todo(获取分页条件)
 * @Author: liu tao
 * @Time: xxx
 * @param int $size
 * @return string
 */
if (!function_exists('getLimit')) {
    function getLimit($size = 0)
    {
        $pageNo = input('pageNo') ? input('pageNo') : 1;
        $pageSize = input('pageSize') ? input('pageSize') : ($size > 0 ? $size : 10);
        $pageSize = $pageSize > 50 ? 0 : $pageSize;
        $start = ($pageNo - 1) * $pageSize;
        $end = $pageSize;
        $limit = $start . ',' . $end;
        return $limit;
    }
}


/**
 * @Title: buildMasterKey
 * @Description: todo(生产唯一key)
 * @Author: liu tao
 * @Time: xxx
 * @return string
 */
if (!function_exists('buildMasterKey')) {
    function buildMasterKey()
    {
        return date('Ymd') . substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 8);
    }
}

/**
 * @Title: isMobile
 * @Description: todo(判断是不是手机端)
 * @Author: liu tao
 * @Time: xxx
 * @return bool
 */
if (!function_exists('isMobile')) {
    function isMobile()
    {
        if (isset ($_SERVER['HTTP_X_WAP_PROFILE'])) {
            return true;
        }
        if (isset ($_SERVER['HTTP_VIA'])) {
            return stristr($_SERVER['HTTP_VIA'], "wap") ? true : false;
        }
        if (isset ($_SERVER['HTTP_USER_AGENT'])) {
            $client_key_words = array('nokia',
                'sony',
                'ericsson',
                'mot',
                'samsung',
                'htc',
                'sgh',
                'lg',
                'sharp',
                'sie-',
                'philips',
                'panasonic',
                'alcatel',
                'lenovo',
                'iphone',
                'ipod',
                'blackberry',
                'meizu',
                'android',
                'netfront',
                'symbian',
                'ucweb',
                'windowsce',
                'palm',
                'operamini',
                'operamobi',
                'openwave',
                'nexusone',
                'cldc',
                'midp',
                'wap',
                'mobile'
            );
            if (preg_match("/(" . implode('|', $client_key_words) . ")/i", strtolower($_SERVER['HTTP_USER_AGENT']))) {
                return true;
            }
        }
        if (isset ($_SERVER['HTTP_ACCEPT'])) {
            if ((strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') !== false) && (strpos($_SERVER['HTTP_ACCEPT'], 'text/html') === false || (strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') < strpos($_SERVER['HTTP_ACCEPT'], 'text/html')))) {
                return true;
            }
        }
        return false;
    }
}
if (!function_exists('httpsRequest')) {

/**
 * @Title: https_request
 * @Description: todo(curl请求)
 * @Author: liu tao
 * @Time: xxx
 * @param $url
 * @param null $data
 * @return mixed
 */
    function httpsRequest($url, $data = null)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
        if (!empty($data)) {
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        }
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($curl);
        curl_close($curl);
        return $output;
    }
}
