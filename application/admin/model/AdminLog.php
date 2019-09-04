<?php

namespace app\admin\model;

class AdminLog extends Base
{
    //自定义日志标题
    private static $title = '';
    //自定义日志内容
    private static $content = '';

    public static function setTitle($title)
    {
        self::$title = $title;
    }

    public static function setContent($content)
    {
        self::$content = $content;
    }

    public static function record()
    {
        $adminUser  = session(config('admin.session_admin_user'), '', config('admin.session_admin_scope'));
        $admin_id   = $adminUser ? $adminUser->id : 0;
        $username   = $adminUser ? $adminUser->name : lang('Unknown');
        $content    = self::$content;
        $title      = self::$title;

        if (!$content)
        {
            $content = request()->param();
            foreach ($content as $k => $v)
            {
                if (is_string($v) && strlen($v) > 200 || stripos($k, 'password') !== false)
                {
                    unset($content[$k]);
                }
            }
        }

        if (!$title)
        {
            $where = [
                'module'        => MODULE_NAME,
                'controller'    => CONTROLLER_NAME,
                'action'        => ACTION_NAME,
            ];
            $name = self::getBreadcrumb($where);
            $title = implode(' ', array_reverse($name));
        }

        self::create([
            'title'         => $title,
            'content'       => !is_scalar($content) ? json_encode($content) : $content,
            'url'           => request()->url(),
            'admin_id'      => $admin_id,
            'username'      => $username,
            'user_agent'    => request()->server('HTTP_USER_AGENT'),
            'ip'            => ipToInt(request()->ip())
        ]);
    }


    public static function getBreadcrumb($where){
        static $ret = array();
        $MenuModel = new Menu();
        $auth = $MenuModel->field('id,parent_id,name,params')->where($where)->select()->toArray();
        if (count($auth) > 1) {
            $getParams = request()->param();
            $arr = [];
            foreach ($auth as $key => $val) {
                $param = array_filter(explode('=', str_replace('&','',$val['params'])));
                if(isset($getParams[$param[0]])&& $getParams[$param[0]] == $param[1]){
                    $arr = $val;
                }
            }
        } else {
            $arr = $auth?$auth[0]:[];
        }

        if($arr){
            $ret[] = $arr['name'];
        }
        if ($arr && $arr['parent_id']) {
            $where = [
                'id' => $arr['parent_id']
            ];
            return self::getBreadcrumb($where);
        }
        return $ret;
    }

    public function getIpAttr($value)
    {
        return long2ip($value);
    }

    public function getContentAttr($value)
    {
        return json_decode($value, true) ? json_decode($value, true) : $value;
    }
}
