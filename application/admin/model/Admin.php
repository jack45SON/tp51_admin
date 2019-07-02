<?php
namespace app\admin\model;

class Admin extends Base
{
    // 表名
    protected $table = 'admin';

    public function adminInfo()
    {
        return $this->hasOne('AdminInfo', 'admin_id')->field('id,admin_id,nickname,avatar');
    }


    public function adminGroup()
    {
        return $this->hasMany('AdminGroup', 'admin_id');
    }

    /**
     * @Title: clearRedis
     * @Description: todo(清除缓存)
     * @Author: liu tao
     * @Time: xxx
     * @param $ids
     * @return bool
     */
    public function clearRedis($ids,$redis){
        if(!is_array($ids)){
            $ids = array_filter(explode(',',$ids));
        }
        foreach ($ids as $v){

            $redis->delete(config('admin.session_admin_auth') . $v);
            $redis->delete(config('admin.session_admin_menu') . $v);

            $keys = $redis->keys(config('admin.session_admin_auth_check') . $v);
            $redis->del($keys);
            $keys = $redis->keys(config('admin.session_admin_auth_check_navP') . $v);
            $redis->del($keys);
            $keys = $redis->keys(config('admin.session_admin_auth_check_nav') . $v);
            $redis->del($keys);
        }
        return true;
    }
}