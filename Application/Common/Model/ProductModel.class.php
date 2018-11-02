<?php
namespace Common\Model;
use Common\Model\BaseModel;
class ProductModel extends BaseModel{

    protected $_auto=array(
        array('status','get_default_status',1,'callback'),
        array('create_at','get_time',1,'callback'),
        array('update_at','get_time',3,'callback'),
    );

    public function getProductData($map){
        $data = $this
            ->alias('p')
            ->join('__T_PRODUCT__ tp ON tp.id = p.product_type_id')
            ->field('p.*,tp.product_type_name')
            ->where(array_filter($map))
            ->select();
        foreach ($data as $key => $value) {
            $data[$key]['product_detail'] = htmlspecialchars_decode($value['product_detail']);
        }
        return $data;
    }

    public function getProductNumber($map){
        $data = $this
            ->alias('p')
            ->join('__T_PRODUCT__ tp ON tp.id = p.product_type_id')
            ->where(array_filter($map))
            ->count();
        return $data;
    }

    public function getProductDetail($map){
        $data = $this
            ->alias('p')
            ->join('__T_PRODUCT__ tp ON tp.id = p.product_type_id')
            ->field('p.*,tp.product_type_name')
            ->where(array_filter($map))
            ->find();
        $data['product_detail'] = htmlspecialchars_decode($data['product_detail']);
        return $data;
    }

}
