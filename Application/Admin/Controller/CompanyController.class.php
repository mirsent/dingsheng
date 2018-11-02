<?php
namespace Admin\Controller;
use Common\Controller\AdminBaseController;
class CompanyController extends AdminBaseController{

    /**
     * 公司简介
     */
    public function about(){
        $company = M('company')->find(1);
        $this->assign('company',$company);
        $this->display();
    }

    /**
     * 编辑公司信息
     */
    public function editCompany(){
        $company = M('company');
        $company->create();
        $res = $company->where(['id'=>1])->save();

        if ($res === false) {
            ajax_return(0,'修改公司信息出错');
        }
        ajax_return(1);
    }

    /**
     * 上传logo
     */
    public function uploadLogo(){
        $icoUrl = upload_img('Company');
        $cond['id'] = 1;
        $data['company_logo'] = $icoUrl;
        $res = M('company')->where($cond)->save($data);

        if ($res === false) {
            ajax_return(0, '上传图片出错');
        }
        ajax_return(1);
    }

    /**
     * 删除logo
     */
    public function deleteLogo(){
        $cond['id'] = 1;
        $data['company_logo'] = '';
        $res = M('company')->where($cond)->save($data);

        if ($res === false) {
            ajax_return(0, '删除图片出错');
        }
        ajax_return(1);
    }

    /**
     * 上传微信二维码
     */
    public function uploadWx(){
        $icoUrl = upload_img('Company');
        $cond['id'] = 1;
        $data['company_wx_code'] = $icoUrl;
        $res = M('company')->where($cond)->save($data);

        if ($res === false) {
            ajax_return(0, '上传图片出错');
        }
        ajax_return(1);
    }

    /**
     * 删除微信二维码
     */
    public function deleteWx(){
        $cond['id'] = 1;
        $data['company_wx_code'] = '';
        $res = M('company')->where($cond)->save($data);

        if ($res === false) {
            ajax_return(0, '删除图片出错');
        }
        ajax_return(1);
    }

    //////////
    // 开票信息 //
    //////////

    /**
     * 开票信息
     */
    public function bill(){
        $bill = M('bill')->find(1);
        $this->assign('bill',$bill);
        $this->display();
    }

    /**
     * 编辑开票信息
     */
    public function editBill(){
        $bill = M('bill');
        $bill->create();
        $res = $bill->where(['id'=>1])->save();

        if ($res === false) {
            ajax_return(0,'修改开票信息出错');
        }
        ajax_return(1);
    }


    ///////////
    // 需求人信息 //
    ///////////

    public function getDemandInfo()
    {
        $ms = M('demand');
        $map['status'] = array('neq', C('STATUS_N'));
        $infos = $ms->where($map)->select();

        echo json_encode([
            "data" => $infos
        ], JSON_UNESCAPED_UNICODE);
    }

    public function inputDemand()
    {
        $demand = D('Demand');
        $demand->create();
        $id = I('id');
        if ($id) {
            $cond['id'] = $id;
            $res = $demand->where($cond)->save();
        } else {
            $res = $demand->add();
        }

        if ($res === false) {
            ajax_return(0,'修改需求任信息出错');
        }
        ajax_return(1);
    }


    //////////
    // 订单信息 //
    //////////

    public function getOrderInfo()
    {
        $ms = M('order');
        $cond['status'] = C('STATUS_Y');

        $recordsTotal = $ms->where($cond)->count();

        // 搜索
        $search = I('search');
        if (strlen($search)>0) {
            $cond['demand|tel|location|product|standard|origin'] = array('like', '%'.$search.'%');
        }
        $cond['demand'] = I('demand');
        $cond['tel'] = I('tel');
        $cond['product'] = I('product');
        $day = I('day');
        if ($day) {
            $cond['create_at'] = array('between', [$day.' 00:00:01', $day.' 23:59:59']);
        }

        $recordsFiltered = $ms->where(array_filter($cond))->count();

        // 排序
        $orderObj = I('order')[0];
        $orderColumn = $orderObj['column']; // 排序列，从0开始
        $orderDir = $orderObj['dir'];       // ase desc
        if(isset(I('order')[0])){
            $i = intval($orderColumn);
            switch($i){
                case 0: $ms->order('demand '.$orderDir); break;
                case 0: $ms->order('tel '.$orderDir); break;
                case 0: $ms->order('location '.$orderDir); break;
                case 0: $ms->order('product '.$orderDir); break;
                case 0: $ms->order('standard '.$orderDir); break;
                case 0: $ms->order('origin '.$orderDir); break;
                case 0: $ms->order('unit '.$orderDir); break;
                case 0: $ms->order('package '.$orderDir); break;
                case 0: $ms->order('number '.$orderDir); break;
                case 0: $ms->order('unit_price '.$orderDir); break;
                case 0: $ms->order('create_at '.$orderDir); break;
                case 0: $ms->order('status '.$orderDir); break;
                default: break;
            }
        } else {
            $ms->order('create_at desc');
        }

        // 分页
        $start = I('start');  // 开始的记录序号
        $limit = I('limit');  // 每页显示条数
        $page = I('page');    // 第几页

        $infos = $ms->page($page, $limit)->where($cond)->select();

        echo json_encode(array(
            "draw" => intval(I('draw')),
            "recordsTotal" => intval($recordsTotal),
            "recordsFiltered" => intval($recordsFiltered),
            "data" => $infos
        ), JSON_UNESCAPED_UNICODE);
    }
}
