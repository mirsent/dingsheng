<?php
namespace Admin\Controller;
use Common\Controller\AdminBaseController;
class ProductController extends AdminBaseController{

    public function product_type(){
        $assign = [
            'table' => 'TProduct',
            'name'  => 'product_type_name',
            'title' => '产品类型'
        ];
        $this->assign($assign);
        $this->display();
    }

    public function standard(){
        $assign = [
            'table' => 'Standard',
            'name'  => 'standard_name',
            'title' => '产品规格'
        ];
        $this->assign($assign);
        $this->display();
    }

    public function origin(){
        $assign = [
            'table' => 'Origin',
            'name'  => 'origin_name',
            'title' => '产地'
        ];
        $this->assign($assign);
        $this->display();
    }

    public function unit(){
        $assign = [
            'table' => 'Unit',
            'name'  => 'unit_name',
            'title' => '单位'
        ];
        $this->assign($assign);
        $this->display();
    }

    /**
     * 产品详情
     */
    public function product_detail(){
        $cond['p.id'] = I('id');
        $product = D('Product')->getProductDetail($cond);
        $this->assign('product',$product);
        $this->display();
    }

    /**
     * 社会责任列表
     */
    public function product_list(){
        $cond['status'] = C('STATUS_Y');
        $productType = M('t_product')->where($cond)->select();
        $this->assign('productType',$productType);
        $this->display();
    }

    /**
     * 获取社会责任列表
     */
    public function getProductInfo(){
        $ms = D('Product');
        $cond = [
            'p.status' => array('neq',C('STATUS_N'))
        ];

        $recordsTotal = $ms->alias('p')->where($cond)->count();

        // 搜索
        $search = I('search');
        if (strlen($search)>0) {
            $cond['product_title|product_type_name|product_brief'] = array('like', '%'.$search.'%');
        }
        $cond['product_title'] = I('product_title');
        $cond['product_type_id'] = I('product_type_id');
        $day = I('day');
        if ($day) {
            $cond['create_at'] = array('between', [$day.' 00:00:01', $day.' 23:59:59']);
        }

        $recordsFiltered = $ms
            ->alias('p')
            ->join('__T_PRODUCT__ tp ON tp.id = p.product_type_id')
            ->where(array_filter($cond))
            ->count();

        // 排序
        $orderObj = I('order')[0];
        $orderColumn = $orderObj['column']; // 排序列，从0开始
        $orderDir = $orderObj['dir'];       // ase desc
        if(isset(I('order')[0])){
            $i = intval($orderColumn);
            switch($i){
                case 0: $ms->order('product_title '.$orderDir); break;
                case 1: $ms->order('product_type_name '.$orderDir); break;
                case 3: $ms->order('create_at '.$orderDir); break;
                case 4: $ms->order('p.status '.$orderDir); break;
                default: break;
            }
        } else {
            $ms->order('create_at desc');
        }

        // 分页
        $start = I('start');  // 开始的记录序号
        $limit = I('limit');  // 每页显示条数
        $page = I('page');    // 第几页

        $infos = $ms->page($page, $limit)->getProductData($cond);

        echo json_encode(array(
            "draw" => intval(I('draw')),
            "recordsTotal" => intval($recordsTotal),
            "recordsFiltered" => intval($recordsFiltered),
            "data" => $infos
        ), JSON_UNESCAPED_UNICODE);
    }

    /**
     * 新增、编辑社会责任
     */
    public function addProduct(){
        $product = D('Product');
        $product->create();
        $id = I('id');
        if ($id) {
            $cond['id'] = $id;
            $res = $product->where($cond)->save();
        } else {
            $res = $product->add();
        }

        if ($res === false) {
            ajax_return(0,'修改产品任信息出错');
        }
        ajax_return(1);
    }

    /**
     * 根据ID删除图片
     */
    public function deletePic(){
        ajax_return(1);
    }
    public function deletePicById(){
        $cond['id'] = I('id');
        $data['product_url'] = '';
        $res = M('product')->where($cond)->save($data);
        if ($res === false) {
            ajax_return(0, '删除出错');
        }
        ajax_return(1, '删除成功');
    }
    public function deleteProTypePicById(){
        $cond['id'] = I('id');
        $data['product_type_url'] = '';
        $res = M('t_product')->where($cond)->save($data);
        if ($res === false) {
            ajax_return(0, '删除出错');
        }
        ajax_return(1, '删除成功');
    }

}
