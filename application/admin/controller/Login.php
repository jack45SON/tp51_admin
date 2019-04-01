<?php

namespace app\admin\controller;

use think\captcha\Captcha;
use \think\Controller;
use app\common\lib\IAuth as IAuth;

$path =\Env::get('root_path') . 'application/lang/zh-cn/Admin.php';
\Lang::load($path);
class Login extends Controller
{
    /**
     * @Title: index
     * @Description: todo(登录页面)
     * @Author: liu tao
     * @return mixed|\think\response\Json
     */
    public function index()
    {
        if (session('?'.config('admin.session_admin_id'),'',config('admin.session_admin_scope'))) {
            $this->redirect('Index/index');
        }
        if (request()->isPost()) {
            $data = input('post.');
            $validate = new \app\admin\validate\admin();
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
            //密码校验
            if ($admin['password'] !== IAuth::setPassword($data['password'],$admin->encrypt)) {
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
        return $captcha->entry();
    }

    /**
     * @Title: _logOut
     * @Description: todo(退出登录)
     * @Author: liu tao
     */
    public function layout()
    {
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
        $upData = [
            'login_count' => $admin['login_count'] + 1,
            'last_time'   => time(),
            'last_ip'     => ipToInt(request()->ip())
        ];
        $AdminModel = new \app\admin\model\Admin();
        $AdminModel->allowField(true)
            ->save($upData, [
                'id' => $admin['id']
            ]);
        $admin = $AdminModel->with('adminInfo')->find($admin['id']);

        session(config('admin.session_admin_id'), $admin['id'],config('admin.session_admin_scope'));
        session(config('admin.session_admin_user'), $admin,config('admin.session_admin_scope'));
    }
}
