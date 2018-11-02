<?php
namespace Home\Controller;
use Think\Controller;
class WxController extends Controller {

    /**
     * 获取公司信息
     */
    public function get_company_info()
    {
        $data = M('company')->find(1);
        ajax_return(1, '公司信息', $data);
    }

    /**
     * 获取产品类型
     */
    public function get_pro_type()
    {
        $cond['status'] = C('STATUS_Y');
        $data = M('t_product')
            ->where($cond)
            ->getField('product_type_name', true);
        ajax_return(1, '产品类型', $data);
    }

    /**
     * 获取产品列表
     * @param type 搜索类型
     */
    public function get_pro_list()
    {
        $cond['status'] = C('STATUS_Y');

        $type = I('type');
        if ($type) {
            $cond_type = [
                'status'            => C('STATUS_Y'),
                'product_type_name' => $type
            ];
            $typeId = M('t_product')->where($cond_type)->getField('id');
            $cond['product_type_id'] = $typeId;
        }

        $data = M('product')
            ->where($cond)
            ->field('id,product_title,product_brief,product_url')
            ->select();
        ajax_return(1, '产品列表', $data);
    }

    /**
     * 获取开票信息
     */
    public function get_bill_info()
    {
        $data = M('bill')->find(1);
        ajax_return(1, '开票信息', $data);
    }
}