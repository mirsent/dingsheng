<?php
namespace Home\Controller;
use Think\Controller;
class MController extends Controller {

    /**
     * 按类型获取新闻列表
     */
    public function getNewsList(){
        $newsType = M('t_news')->where(['status'=>C('STATUS_Y')])->select(); // 新闻类型
        foreach ($newsType as $key => $value) {
            $cond = [
                'n.status'     => C('STATUS_Y'),
                'news_type_id' => $value['id']
            ];
            $data[] = [
                'news_type' => $value['news_type_name'],
                'data'      => D('News')->getNewsData($cond)
            ];
        }
        ajax_return(1,'新闻列表',$data);
    }

    /**
     * 获取新闻资讯
     * @param w 数量
     * @param t 新闻类型
     * @param p 第几页
     * @param limit 每页条数
     */
    public function getNewsInfo(){
        $number = I('w');
        $news = D('News');
        $cond = [
            'n.status'     => C('STATUS_Y'),
            'news_type_id' => I('t')
        ];
        if ($number)
            $news->limit($number);

        $page = I('p');
        $limit = I('limit');

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
        ajax_return(1,'新闻列表',$list);
    }


    /**
     * 新闻详情
     * @param n 新闻ID
     * @return prev 上一篇
     * @return next 下一篇
     */
    public function getNewsDetail(){
        $news = D('News');
        $cond['n.id'] = I('n');
        $data = $news->getNewsDetail($cond);
        $news->alias('n')->where($cond)->setInc('times',1); // 查看次数+1

        $cond_prev = [
            'status' => C('STATUS_Y'),
            'id'   => array('lt', I('n'))
        ];
        $prev = $news
            ->where($cond_prev)
            ->order('id desc')
            ->find();

        $cond_next = [
            'status' => C('STATUS_Y'),
            'id'   => array('gt', I('n'))
        ] ;
        $next = $news
            ->where($cond_next)
            ->find();

        $list = [
            'prev' => $prev,
            'data' => $data,
            'next' => $next
        ];
        ajax_return(1,'新闻详情',$list);
    }


    /**
     * 获取产品列表
     * @param w 数量
     * @param t 产品类型
     * @param p 第几页
     * @param limit 每页条数
     */
    public function getProductInfo(){
        $number = I('w');
        $product = D('Product');
        $cond = [
            'p.status'     => C('STATUS_Y'),
            'product_type_id' => I('t')
        ];
        if ($number)
            $product->limit($number);

        $page = I('p');
        $limit = I('limit');

        if ($page)
            $product->page($page,$limit);

        $data = $product->getProductData($cond);
        $number = $product->getProductNumber($cond);
        if ($limit)
            $total = ceil($number/$limit);

        $list = [
            'data'  => $data,
            'total' => $total
        ];
        ajax_return(1,'产品列表',$list);
    }


    /**
     * 产品详情
     * @param p 产品ID
     */
    public function getProductDetail(){
        $product = D('Product');
        $cond['p.id'] = I('p');
        $data = $product->getProductDetail($cond);
        $product->alias('p')->where($cond)->setInc('times',1); // 查看次数+1

        ajax_return(1,'产品详情',$data);
    }
}
