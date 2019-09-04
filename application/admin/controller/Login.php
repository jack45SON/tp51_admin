<?php

namespace app\admin\controller;

use app\admin\model\AdminLog;
use think\captcha\Captcha;
use \think\Controller;
use app\common\lib\IAuth as IAuth;
use think\facade\Lang;

class Login extends Controller
{

    public function initialize()
    {
        parent::initialize();
        // 定义应用目录
        define('APP_PATH', \Env::get('root_path') . 'application/');
        define('MODULE_NAME', strtolower(request()->module()));
        define('CONTROLLER_NAME', strtolower(request()->controller()));
        define('ACTION_NAME', strtolower(request()->action()));

        //加载多语言相应控制器对应字段
        if (isset($_COOKIE['think_var']) && $_COOKIE['think_var']) {
            $langField = $_COOKIE['think_var'];
        } else {
            $langField = config('default_lang');
        }
        Lang::load(APP_PATH .MODULE_NAME.'/lang/' . $langField . '/Admin.php');

    }
    /**
     * @Title: index
     * @Description: todo(登录页面)
     * @Author: liu tao
     * @return mixed|\think\response\Json
     */
    public function index()
    {
        $flag = false;
        $adminId = session(config('admin.session_admin_id'), '', config('admin.session_admin_scope'));
        if ($adminId) {
            $redis = redis(config('admin.admin_redis_select'));
            if($redis->exists(config('admin.session_admin_id') . $adminId)){
                $flag =  true;
            }
        }
        if($flag){
            $this->redirect('/'.MODULE_NAME.'/index/index');
        }

        if (request()->isPost()) {
            $data = input('post.');
            $validate = new \app\admin\validate\Admin();
            if (!$validate->scene('login')->check($data)) {
                return show(-1, $validate->getError());
            }
            $AdminModel = new \app\admin\model\Admin();

            try {
                $admin = $AdminModel->get(['name' => $data['name']]);
            } catch (\Exception $e) {
                return show(-1, $e->getMessage());
            }

            //判断管理员是否存在
            if (empty($admin)) {
                return show(-1, lang('admin_no_exist'));
            }
            //判断用户是否禁止
            if ($admin->status != config('admin.admin_status_normal')) {
                return show(-1, lang('admin_stop'));
            }
            IAuth::setPassword($data['password'],$admin->encrypt);
            $password = IAuth::getPassword();
            //密码校验
            if ($admin['password'] !== $password) {
                return show(-1, lang('password_error'));
            }

            try {//登录成功操作
                $this->_loginSuccess($admin);
                return show(1, lang('login_success'),[], 'index/index');
            } catch (\Exception $e) {
                return show(-1, $e->getMessage());
            }
        } else {
            return $this->fetch();
        }

    }

    /**
     * @Title: chkCode
     * @Description: todo(验证码)
     * @Author: liu tao
     * @return \think\Response
     */
    public function chkCode()
    {
        $config =    [
            'length'    => 4,
            'useCurve'  =>false
        ];
        $captcha = new Captcha($config);
        $captcha->codeSet = '0123456789';
        return $captcha->entry();
    }

    /**
     * @Title: _logOut
     * @Description: todo(退出登录)
     * @Author: liu tao
     */
    public function layout()
    {
        $adminId = session(config('admin.session_admin_id'), '', config('admin.session_admin_scope'));
        $redis = redis(config('admin.admin_redis_select'));
        $redis->delete(config('admin.session_admin_id') . $adminId);
        $AdminModel = new \app\admin\model\Admin();
        $AdminModel->clearRedis($adminId,$redis);
        session(null,config('admin.session_admin_scope'));
        $this->redirect('Login/index');
    }

    /**
     * @Title: _loginSuccess
     * @Description: todo(登录成功操作)
     * @Author: liu tao
     * @param $admin
     */
    private function _loginSuccess($admin)
    {
        AdminLog::setTitle(Lang('Login'));
        $upData = [
            'login_count' => $admin['login_count'] + 1,
            'last_time'   => date('Y-m-d H:i:s'),
            'last_ip'     => ipToInt(request()->ip())
        ];
        $AdminModel = new \app\admin\model\Admin();
        $AdminModel->allowField(true)
            ->save($upData, [
                'id' => $admin['id']
            ]);
        $redis = redis(config('admin.admin_redis_select'));
        $redis->set(config('admin.session_admin_id') . $admin['id'], $admin['id'], config('admin.redis_expire'));
        session(config('admin.session_admin_id'), $admin['id'],config('admin.session_admin_scope'));
        session(config('admin.session_admin_user'), $admin,config('admin.session_admin_scope'));
    }
}
