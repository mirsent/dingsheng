<?php
namespace Common\Model;
use Common\Model\BaseModel;
class NewsModel extends BaseModel{

    protected $_auto=array(
        array('status','get_default_status',1,'callback'),
        array('create_at','get_time',1,'callback'),
        array('update_at','get_time',3,'callback'),
    );

    public function getNewsData($map){
        $data = $this
            ->alias('n')
            ->join('__T_NEWS__ tn ON tn.id = n.news_type_id')
            ->field('n.*,tn.news_type_name')
            ->order('create_at desc')
            ->where(array_filter($map))
            ->select();
        foreach ($data as $key => $value) {
            $data[$key]['news_detail'] = htmlspecialchars_decode($value['news_detail']);
        }
        return $data;
    }

    public function getNewsNumber($map){
        $data = $this
            ->alias('n')
            ->join('__T_NEWS__ tn ON tn.id = n.news_type_id')
            ->where(array_filter($map))
            ->count();
        return $data;
    }

    public function getNewsDetail($map){
        $data = $this
            ->alias('n')
            ->join('__T_NEWS__ tn ON tn.id = n.news_type_id')
            ->field('n.*,tn.news_type_name')
            ->where(array_filter($map))
            ->find();
        $data['news_detail'] = htmlspecialchars_decode($data['news_detail']);
        return $data;
    }
}
