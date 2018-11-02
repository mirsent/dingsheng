<?php
namespace Admin\Controller;
use Common\Controller\AdminBaseController;
class NewsController extends AdminBaseController{

    /**
     * 新闻类型
     */
    public function news_type(){
        $assign = [
            'table' => 'TNews',
            'name'  => 'news_type_name',
            'title' => '新闻类型'
        ];
        $this->assign($assign);
        $this->display();
    }

    /**
     * 新闻详情
     */
    public function news_detail(){
        $cond['n.id'] = I('id');
        $news = D('News')->getNewsDetail($cond);
        $this->assign('news',$news);
        $this->display();
    }


    /**
     * 新闻列表
     */
    public function news_list(){
        $cond['status'] = C('STATUS_Y');
        $newsType = M('t_news')->where($cond)->select();
        $this->assign('newsType',$newsType);
        $this->display();
    }

    /**
     * 获取咨询列表
     */
    public function getNewsInfo(){
        $ms = D('News');
        $cond = [
            'n.status' => array('neq',C('STATUS_N'))
        ];

        $recordsTotal = $ms->alias('n')->where($cond)->count();

        // 搜索
        $search = I('search');
        if (strlen($search)>0) {
            $cond['news_title|news_type_name|news_brief'] = array('like', '%'.$search.'%');
        }
        $cond['news_title'] = I('news_title');
        $cond['news_type_id'] = I('news_type_id');
        $day = I('day');
        if ($day) {
            $cond['create_at'] = array('between', [$day.' 00:00:01', $day.' 23:59:59']);
        }

        $recordsFiltered = $ms
            ->alias('n')
            ->join('__T_NEWS__ tn ON tn.id = n.news_type_id')
            ->where(array_filter($cond))
            ->count();

        // 排序
        $orderObj = I('order')[0];
        $orderColumn = $orderObj['column']; // 排序列，从0开始
        $orderDir = $orderObj['dir'];       // ase desc
        if(isset(I('order')[0])){
            $i = intval($orderColumn);
            switch($i){
                case 0: $ms->order('news_title '.$orderDir); break;
                case 1: $ms->order('news_type_name '.$orderDir); break;
                case 3: $ms->order('create_at '.$orderDir); break;
                case 4: $ms->order('n.status '.$orderDir); break;
                default: break;
            }
        } else {
            $ms->order('create_at desc');
        }

        // 分页
        $start = I('start');  // 开始的记录序号
        $limit = I('limit');  // 每页显示条数
        $page = I('page');    // 第几页

        $infos = $ms->page($page, $limit)->getNewsData($cond);

        echo json_encode(array(
            "draw" => intval(I('draw')),
            "recordsTotal" => intval($recordsTotal),
            "recordsFiltered" => intval($recordsFiltered),
            "data" => $infos
        ), JSON_UNESCAPED_UNICODE);
    }

    /**
     * 新增、编辑咨询
     */
    public function addNews(){
        $news = D('News');
        $news->create();
        $id = I('id');
        if ($id) {
            $cond['id'] = $id;
            $res = $news->where($cond)->save();
        } else {
            $res = $news->add();
        }

        if ($res === false) {
            ajax_return(0,'修改新闻信息出错');
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
        $data['news_url'] = '';
        $res = M('news')->where($cond)->save($data);
        if ($res === false) {
            ajax_return(0, '删除出错');
        }
        ajax_return(1, '删除成功');
    }

}
