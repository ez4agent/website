<?php

namespace Home\Controller;
use Common\Controller\FrontbaseController;
use Home\Model\ApplyModel;

class OrderController extends FrontbaseController
{

    public function __construct()
    {
        parent::__construct();
        //会员ID
        $this->member_id =$this->auth()->member_id;
        $this->apply_mod=D('Apply');
        $this->log_mod=D('Log');
    }

    public function pay(){

        $order_no = I('get.order_no', '');

        //更改状态
        M('stu_apply')->where('stu_apply_id='.$order_no)->setField(
            array('status'=>ApplyModel::VISA_CONFIRM)
        );

        //更新日志
        $log = array(
            'apply_id'=>$order_no,
            'operate_user_id'=>$this->member_id,
            'update_status'=>ApplyModel::VISA_CONFIRM,
            'type'=>0,
            'operate_content'=>"",
        );

        D('Log')->add_log($order_no,$log);

        $this->assign('order_no',$order_no);
        $this->display();
    }
}

?>