<?php
namespace Common\Model;
use Common\Model\BaseModel;
/**
 * ModelName
 */
class UserModel extends BaseModel{

    protected $_auto=array(
        array('user_psw','md5',1,'function'),
        array('user_psw','',2,'ignore'),
        array('status','get_default_status',1,'callback')
    );

    /**
     * 根据条件获取用户列表
     */
    public function getUserList($map){
        $data = $this
            ->where($map)
            ->select();
        return $data;
    }

    /**
     * 获取用户详细信息
     */
    public function getUserInfo($map){
        $data = $this
            ->alias('u')
            ->join('__AUTH_GROUP_ACCESS__ aga ON aga.uid = u.id','LEFT')
            ->join('__COMPANY__ c ON c.id = u.company_id')
            ->field('u.*,aga.*,c.company_name')
            ->where($map)
            ->select();
        return $data;
    }

    /**
     * 根据条件获取用户信息
     */
    public function getUserData($map){
        $data = $this
            ->where($map)
            ->find();
        return $data;
    }
}
