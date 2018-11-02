<?php
namespace Common\Model;
use Common\Model\BaseModel;
class CompanyModel extends BaseModel{

    protected $_auto=array(
        array('status','get_default_status',1,'callback')
    );

    public function getCompanyData(){
        $data = $this
            ->where(['status'=>C('STATUS_Y')])
            ->select();
        return $data;
    }

}
