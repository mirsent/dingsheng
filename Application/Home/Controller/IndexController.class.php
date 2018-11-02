<?php
namespace Home\Controller;
use Common\Controller\HomeBaseController;
class IndexController extends HomeBaseController  {
    public function index(){
        $this->display();
    }
    public function profile(){
        $this->display();
    }
    public function product(){
        $id = I('id');
        if(!$id){
            $id=1;
        }
        $this->assign("id",$id);
        $this->display();
    }
    public function productDetails(){
        $product = D('Product');
        $cond['p.id'] = I('id');
        $data = $product->getProductDetail($cond);
        $product->alias('p')->where($cond)->setInc('times',1); // 查看次数+1
//        p($data);
        $this->assign("data",$data);
        $this->display();
    }
    public function news(){
        $number = I('w');
        $news = D('News');
        $cond = [
            'n.status'     => C('STATUS_Y'),
            'news_type_id' => I('t')
        ];
        if ($number)
            $news->limit($number);

        $page = I('p');
        if(empty($page)){
            $page=1;
        }
        $limit = 10;  //一页10个

        if ($page)
            $news->page($page,$limit);

        $data = $news->getNewsData($cond);
        $number = $news->getNewsNumber($cond);
        if ($limit)
            $total = ceil($number/$limit);

        $list = [
            'data'  => $data,
            'total' => $total
        ];
//        p($page);
        $this->assign("page",$page);
        $this->assign("limit",$limit);
        $this->assign("list",$list);
        $this->display();
    }
    public function newsDetails(){
        $id=I("id");
        $this->assign("id",$id);
        $this->display();
    }
}
