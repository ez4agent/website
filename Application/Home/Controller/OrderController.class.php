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

    public function hl()
    {
        //if(IS_AJAX) {
            $hl = get_hl();
            if ($hl === false) {
                echo $this->ajaxReturn(array('status' => 'no', 'msg' => '汇率转换失败'));
            } else {
                $price = I('post.price', 0);
                $price = $hl * $price;
                echo $this->ajaxReturn(array('status' => 'yes', 'price' => $price,'hl'=>$hl,'hashtime'=>time()));
            }
        //}
        exit;
    }

    public function visa(){

        $apply_id = I('get.apply_id','0','intval');
        $info = $this->apply_mod->get_apply_info($apply_id);

        if(!$info)
        {
            $this->error('改申请记录不存在！');
            exit();
        }

        if($info['status'] != ApplyModel::VISA_WAIT){
            $this->error('该申请不能进行此操作！');
            exit();
        }

        $contact = D('Member')->get_Member_Info($this->member_id);
        $this->assign('contact',$contact);

        $college_info = M('college')->where(array('college_id' => $info['college_id']))->find();
        $visa_info = M('visa_service')->where(array('country_id' => $college_info['country_id'],'member_id'=>$info['receive_member']))->find();


        $hl = get_hl();
        if ($hl === false) {
            $this->error('汇率获取失败！');
            exit;
        }

        $price = $hl * (float)$visa_info['visa_price'];

        $order = array(
            'pay_price' => $price + $visa_info['service_price']
        );

        $this->assign('hl',$hl);
        $this->assign('rmb_price',$price);
        $this->assign('order',$order);
        $this->assign('visa_info',$visa_info);
        $this->assign('apply_info',$info);
        $this->display('Order/info');
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