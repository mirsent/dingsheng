<?php
namespace Admin\Controller;
use Common\Controller\AdminBaseController;
class BannerController extends AdminBaseController{

    public function getBannerInfo(){
        $cond['status'] = array('neq',C('STATUS_N'));
        $infos = D('Banner')
            ->where($cond)
            ->order('turn is null')
            ->select();
        echo json_encode([
            "data" => $infos
        ], JSON_UNESCAPED_UNICODE);
    }

    /**
     * 添加、修改 banner
     */
    public function addBanner(){
        $banner = D('Banner');
        $banner->create();

        $id = I('id');
        if ($id) {
            $cond['id'] = $id;
            $banner->where($cond)->save();
        } else {
            $res = $banner->add();
        }

        if ($res === false) {
            ajax_return(0,'添加banner出错');
        }
        ajax_return(1);
    }

    /**
     * 排序 banner
     */
    public function orderBanner(){
        $cond['id'] = I('id');
        $data['turn'] = I("turn");
        $res = M('banner')->where($cond)->save($data);

        if ($res === false) {
            ajax_return(0,'banner排序出错');
        }
        ajax_return(1);
    }

    /**
     * 删除 banner
     */
    public function deleteBanner(){
        $cond['id'] = I('id');
        $res = M('banner')->where($cond)->save(['status'=>C('status_N')]);

        if ($res === false) {
            ajax_return(0,'删除Banner出错');
        }
        ajax_return(1);
    }

    /**
     * 上传图片
     */
    public function upload(){
        $config = array(
            'rootPath' => './UploadImages/',
            'subName' => date(Y).'/'.date(m).'/'.date(d),
        );
        $upload = new \Think\Upload($config);// 实例化上传类
        $info = $upload -> upload();

        $siteHost = 'http://'.$_SERVER['HTTP_HOST'].__ROOT__;
        if ($info) {
            foreach ($info as $key => $value) {
                $imagePath = $upload -> rootPath.$value['savepath'].$value['savename'];
                $fullImagePath .= $siteHost.ltrim($imagePath,'.');
                $imageName .= $value['savename'];
            }
            $imgUrl = array(
                'image_path' => $fullImagePath,
                'image_name' => $imageName,
            );
            ajax_return(1,'图片上传成功',$imgUrl);
        }else{
            return false;
        }
    }

    /**
     * 根据ID删除图片
     */
    public function deletePic(){
        ajax_return(1);
    }
    public function deletePicById(){
        $cond['id'] = I('id');
        $data['banner_url'] = '';
        $res = M('banner')->where($cond)->save($data);
        if ($res === false) {
            ajax_return(0, '删除出错');
        }
        ajax_return(1, '删除成功');
    }





    //////////////
    // 产品banner //
    //////////////

    public function getProBannerInfo(){
        $cond['status'] = array('neq',C('STATUS_N'));
        $infos = D('ProBanner')
            ->where($cond)
            ->order('turn is null')
            ->select();
        echo json_encode([
            "data" => $infos
        ], JSON_UNESCAPED_UNICODE);
    }

    /**
     * 添加、修改 banner
     */
    public function addProBanner(){
        $banner = D('ProBanner');
        $banner->create();

        $id = I('id');
        if ($id) {
            $cond['id'] = $id;
            $banner->where($cond)->save();
        } else {
            $res = $banner->add();
        }

        if ($res === false) {
            ajax_return(0,'添加banner出错');
        }
        ajax_return(1);
    }

    /**
     * 排序 banner
     */
    public function orderProBanner(){
        $cond['id'] = I('id');
        $data['turn'] = I("turn");
        $res = M('pro_banner')->where($cond)->save($data);

        if ($res === false) {
            ajax_return(0,'banner排序出错');
        }
        ajax_return(1);
    }

    /**
     * 删除 banner
     */
    public function deleteProBanner(){
        $cond['id'] = I('id');
        $res = M('pro_banner')->where($cond)->save(['status'=>C('status_N')]);

        if ($res === false) {
            ajax_return(0,'删除Banner出错');
        }
        ajax_return(1);
    }

    /**
     * 根据ID删除图片
     */
    public function deleteProPic(){
        ajax_return(1);
    }
    public function deleteProPicById(){
        $cond['id'] = I('id');
        $data['banner_url'] = '';
        $res = M('pro_banner')->where($cond)->save($data);
        if ($res === false) {
            ajax_return(0, '删除出错');
        }
        ajax_return(1, '删除成功');
    }

}
