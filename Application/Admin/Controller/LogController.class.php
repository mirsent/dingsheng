<?php
namespace Admin\Controller;
use Think\Controller;
class LogController extends Controller{

    public function login(){
        $this->display();
    }

    /**
     * 登录验证
     */
    public function check_user(){
        $cond['status'] = C('STATUS_Y');
        $userName = M('user')->where($cond)->getByUserName(I('user_name'));
        if ($userName) {
            echo "true";
        } else {
            echo "false";
        }
    }
    public function check_psw(){
        $userPsw = M('user')->getfieldByUserName(I('user_name'), 'user_psw');
        if ($userPsw == md5(I('user_psw'))) {
            echo "true";
        } else {
            echo "false";
        }
    }

    /**
     * 登录
     */
    public function log_in(){
        $map['user_name'] = I('user_name');
        $user = D('User')->getUserData($map);

        if ($user === false) {
            ajax_return(0, "登录出错");
        }
        session(C('USER_AUTH_KEY'), $user);
        ajax_return(1);
    }

    /**
     * 登出
     */
    public function log_out(){
        session(C('USER_AUTH_KEY'), null);
        $this->redirect('Log/login');
    }

    /**
     * 注册
     */
    public function register(){
        $company = D('Company')->getCompanyData(); // 公司
        $this->assign('company', $company);
        $this->display();
    }

    public function check_register_user(){
        $cond['status'] = C('STATUS_Y');
        $userName = M('user')->where($cond)->getByUserName(I('user_name'));
        if ($userName) {
            echo "false";
        } else {
            echo "true";
        }
    }

    /**
     * 用户注册
     */
    public function userRegister(){
        $trans = M();
        $trans->startTrans();

        $user = D('User');
        $user->create();
        $res = $user->add();

        $authGroupAccess = D('AuthGroupAccess');
        $authGroupAccess->create();
        $authGroupAccess->uid = $res;
        $groupRes = $authGroupAccess->add();

        if ($res === false || $groupRes === false) {
            $trans->rollback();
            ajax_return(0, '注册出错');
        }
        $trans->commit();
        ajax_return(1);
    }
}
