<?php
namespace Admin\Controller;
use Common\Controller\AdminBaseController;
class AuthController extends AdminBaseController {

    /********************************************** 规则管理 ****************************************/

    public function rule(){
        $cond = array(
            'status' => C('STATUS_Y'),
            'pid'    => 0
        );
        $pRules = M('auth_rule')->where($cond)->select();
        $assign = array(
            'table' => 'AuthRule',
            'pRules' => $pRules
        );
        $this->assign($assign);
        $this->display();
    }

    /**
     * 获取权限规则列表
     */
    public function getAuthRuleInfo(){
        $ms = D('AuthRule');
        $cond['status'] = array('neq', C('STATUS_N'));
        $infos = $ms->where($cond)->getTreeData('tree','','title');

        echo json_encode([
            "data" => $infos
        ], JSON_UNESCAPED_UNICODE);
    }

    /**
     * 新增,编辑 权限规则
     */
    public function inputRule(){
        $authRule = D('AuthRule');
        $authRule->create();
        $id = I('id');
        if ($id) {
            $res = $authRule->where(['id'=>$id])->save();
        } else {
            $res = $authRule->add();
        }

        if ($res === false) {
            ajax_return(0, '新增,编辑权限出错');
        }
        ajax_return(1);
    }

    /**
     * 设置状态
     */
    public function setStatus(){
        $ms = D(I('table'));
        $ms->create();
        $res = $ms->where(['id'=>I('id')])->save();

        if ($res === false) {
            ajax_return(0, '设置状态出错');
        }
        ajax_return(1);
    }





    /********************************************** 用户组管理 ****************************************/

    public function group(){
        $authRule = M('auth_rule');
        // 获取一级规则
        $cond = array(
            'status' => C('STATUS_Y'),
            'pid'    => 0
        );
        $rules = $authRule
            ->where($cond)
            ->field('id, title')
            ->order('id')
            ->select();

        foreach ($rules as $key => $value) {
            $cond['pid'] = $value['id'];
            $rules[$key]['_data'] = $authRule
                ->where($cond)
                ->field('id, title')
                ->order('id')
                ->select(); // 二级规则
        }

        $assign = array(
            'table' => 'AuthGroup',
            'rules' => $rules
        );
        $this->assign($assign);
        $this->display();
    }

    /**
     * 获取权限分组信息
     */
    public function getAuthGroupInfo(){
        $ms = D('AuthGroup');
        $cond['status'] = array('neq', C('STATUS_N'));
        $infos = $ms->where($cond)->select();
        foreach ($infos as $key => $value) {
            $infos[$key]['group_id'] = $value['id'];
            $infos[$key]['rules_arr'] = $this->getMultiSelectArr($value['rules']);
            $infos[$key]['rules'] = D('AuthRule')->getAuthByIds($value['rules']);
        }

        echo json_encode([
            "data" => $infos
        ], JSON_UNESCAPED_UNICODE);
    }

    /**
     * 组合权限 （父权限id,权限id）
     */
    public function getMultiSelectArr($rules){
        $arr = [];
        foreach (explode(',', $rules) as $v) {
            $cond = array(
                'status' => array('neq', C('STATUS_N')),
                'id' => $v
            );
            $pid = M('auth_rule')->where($cond)->getField('pid');
            array_push($arr, $pid.','.$v);
        }
        return $arr;
    }

    /**
     * 新增/编辑 用户组
     */
    public function inputGroup(){
        $authGroup = D('AuthGroup');
        $authGroup->create();
        $authGroup->rules = implode(',', array_unique(explode(',', implode(',', I('rules')))));
        $id = I('id');
        if ($id) {
            $res = $authGroup->where(['id'=>$id])->save();
        } else {
            $res = $authGroup->add();
        }

        if ($res === false) {
            ajax_return(0, '新增/编辑出错');
        }
        ajax_return(1);
    }





    /********************************************** 用户管理 ****************************************/

    public function user(){
        $groups = D('AuthGroup')->getAuthGroupList();
        $this->assign('groups',$groups);
        $this->display();
    }
    public function user1(){
        $groups = D('AuthGroup')->getAuthGroupList();
        $this->assign('groups',$groups);
        $this->display();
    }

    public function getAuthUserInfo(){
        $cond['u.status'] = array('neq', C('STATUS_N'));
        $infos = D('User')->getUserInfo($cond);
        foreach ($infos as $key => $value) {
            $infos[$key]['company_auth_arr'] = explode(',', $value['company_auth']);
            $infos[$key]['company_auth_name'] = D('Company')->getCompanysByIds($value['company_auth']);
            $infos[$key]['groups_arr'] = explode(',', $value['group_id']);
            $infos[$key]['groups_name'] = D('AuthGroup')->getGroupsByIds($value['group_id']);
        }

        echo json_encode([
            "data" => $infos
        ], JSON_UNESCAPED_UNICODE);
    }

    /**
     * 修改用户信息
     */
    public function editUser(){
        $cond['id'] = I('id');
        $user = D('User');
        $user->create();
        $res = $user->where($cond)->save();

        if ($res === false) {
            ajax_return(0, '编辑用户信息出错');
        }
        ajax_return(1);
    }

    /**
     * 修改用户权限
     */
    public function editAccess(){
        $cond['id'] = I('id');
        $data['group_id'] = I('group_id');
        p($data);
        $res = M('auth_group_access')->where($cond)->save($data);

        if ($res === false) {
            ajax_return(0, '修改用户权限出错');
        }
        ajax_return(1);
    }

    /**
     * 删除用户
     */
    public function deleteUser(){
        $tran_result = true;
        $trans = M();
        $trans->startTrans();   // 开启事务

        $userId = I('uid');

        // 删除用户权限
        if ($userId) {
            $res = M('auth_group_access')->where(['uid'=>$userId])->delete();

            if ($res === false) {
                $tran_result = false;
            }
        }

        // 修改用户状态
        $cond_user['id'] = $userId;
        $data_user['status'] = C('STATUS_N');
        $userRes = D('User')->editData($cond_user,$data_user);

        if ($userRes === false) {
            $tran_result = false;
        }

        if ($tran_result === false) {
            $trans->rollback();
            ajax_return(0, '删除用户出错');
        } else {
            $trans->commit();
            ajax_return(1);
        }
    }
}
