<?php
namespace Admin\Controller;
use Common\Controller\AdminBaseController;
/**
 * 系统配置controller
 */
class SysController extends AdminBaseController{

    public function company(){
        $assign = [
            'table' => 'Company',
            'name' => 'company_name',
            'title' => '公司'
        ];
        $this->assign($assign);
        $this->display();
    }



    /*************************************** 导航管理 *********************************************/

    public function getNavInfo(){
        $ms = M('nav');
        $map['status'] = array('neq', C('STATUS_N'));
        $infos = $ms->where($map)->order('turn is null')->select();

        echo json_encode([
            "data" => $infos
        ], JSON_UNESCAPED_UNICODE);
    }

    /**
     * 新增/编辑 导航
     */
    public function inputNav(){
        $nav = D('Nav');
        $nav->create();
        $id = I('id');

        if ($id) { // 编辑
            $res = $nav->where(['id'=>$id])->save();
        } else {
            $res = $nav->add();
        }

        if ($res === false) {
            ajax_return(0, '录入导航出错');
        }
        ajax_return(1);
    }

    /**
     * 导航排序
     */
    public function orderNav(){
        $data['turn'] = I('turn');
        $res = M('nav')->where(['id'=>I('id')])->save($data);
        if ($res === false) {
            ajax_return(0, '排序出错');
        }
        ajax_return(1);
    }




    /*************************************** 菜单管理 *********************************************/

    public function admin_nav(){
        $map = array(
            'status' => array('neq',C('STATUS_N')),
            'pid' => 0
        );
        $navs = M('admin_nav')->where($map)->select();
        $assign = array(
            'table' => 'AdminNav',
            'navs' => $navs
        );
        $this->assign($assign);
        $this->display();
    }

    /**
     * 获取菜单列表
     */
    public function getAdminNavInfo(){
        $ms = D('AdminNav');
        $map['status'] = array('neq', C('STATUS_N'));
        $infos = $ms->where($map)->getTreeData();

        echo json_encode([
            "data" => $infos
        ], JSON_UNESCAPED_UNICODE);
    }

    /**
     * 新增/编辑 菜单
     */
    public function inputAdminNav(){
        $adminNav = D('AdminNav');
        $adminNav->create();
        $id = I('id');

        if ($id) { // 编辑
            $res = $adminNav->where(['id'=>$id])->save();
        } else {
            $res = $adminNav->add();
        }

        if ($res === false) {
            ajax_return(0, '录入菜单出错');
        }
        ajax_return(1);
    }

    /**
     * 菜单排序
     */
    public function orderAdminNav(){
        $data['order_num'] = I('order_num');
        $res = M('admin_nav')->where(['id'=>I('id')])->save($data);
        if ($res === false) {
            ajax_return(0, '排序出错');
        }
        ajax_return(1);
    }





    /************************************************************************
     类型管理通用方法
    *************************************************************************/

    /**
     * 获取dt数据
     */
    public function getDtInfo(){
        $ms = D(I('table'));
        $infos = $ms->getDataForDt();
        echo json_encode([
            "data" => $infos
        ], JSON_UNESCAPED_UNICODE);
    }

    /**
     * 新增/编辑
     */
    public function addAndEdit(){
        $ms = D(I('table'));
        $ms->create();
        $id = I('id');
        if ($id) {
            $map['id'] = $id;
            $res = $ms->where($map)->save();
        } else {
            $res = $ms->add();
        }

        if ($res === false) {
            ajax_return(0, '新增/编辑 出错');
        }
        ajax_return(1);
    }

    /**
     * 设置状态
     */
    public function setStatus(){
        $ms = D(I('table'));
        $ms->create();
        $map['id'] = I('id');
        $res = $ms->where($map)->save();

        if ($res === false) {
            ajax_return(0, '设置状态出错');
        }
        ajax_return(1);
    }
}
