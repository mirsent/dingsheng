<?php
namespace Common\Controller;
use Common\Controller\BaseController;
class HomeBaseController extends BaseController{
    public function _initialize(){
        parent::_initialize();

        $cond['status'] = C('STATUS_Y');
        $nav = M('nav')->where($cond)->order('turn is null, turn')->select(); // 导航
        $newsType = M('t_news')->where($cond)->select(); // 新闻类型
        $proType = M('t_product')->where($cond)->select(); // 产品类型
        $banner = M('banner')->where($cond)->order('turn is null, turn')->select(); // Banner
        $company = M('company')->where(['id'=>1])->find(); // 公司

        $assign = [
            'nav'      => $nav,
            'newsType' => $newsType,
            'proType'  => $proType,
            'banner'   => $banner,
            'company'  => $company,
        ];
        $this->assign($assign);
    }
}
