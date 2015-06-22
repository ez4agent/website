<?php

/**
 *  CRM系统 -- 用户信息管理页面 
 */
namespace Home\Controller;
use Common\Controller\FrontbaseController;

class MemberController extends FrontbaseController
{
    var $member_mod;
    var $member_id;
    
    public function __construct()
    {
        parent::__construct();
        $this->member_mod = D('Member');
        $this->member_id = $this->auth()->member_id;
    }
    
    //会员信息
    public function view()
    {
        $info = $this->member_mod->get_Member_Info($this->member_id);
        $visa_service = M('visa_service')->where(array('member_id'=>$this->member_id))->select();
        $address_list = M('member_address')->where(array('member_id'=>$this->member_id))->select();

        if(isset($_GET['visa_id'])){
            $visa_info = M('visa_service')->where(array('visa_id' => $_GET['visa_id'],'member_id'=>$this->member_id))->find();
        }

        if(isset($_GET['address'])){
            $address_info = M('member_address')->where(array('address_id' => $_GET['address'],'member_id'=>$this->member_id))->find();
        }

        $this->assign('info',$info);
        $this->assign('country',country());
        $this->assign('visa_service',$visa_service);
        $this->assign('visa_info',$visa_info);
        $this->assign('address_info',$address_info);
        $this->assign('address_list',$address_list);
        $this->display();
    }
    
    //会员信息数据设置修改
    public function save_MembeData()
    {
        if(IS_AJAX)
        {
            $member_id = I('get.member_id',0,'intval');
            $act = I('get.act','','trim');
            $data = $_POST;
            //固定电话
            if((!empty($data['country_num_qiye'])&&$data['country_num_qiye']!='国家编号')
                && (!empty($data['qu_num_qiye'])&&$data['qu_num_qiye']!='区号') && $data['phone_qiye'])
            { 
                $data['telephone'] = trim($data['country_num_qiye']).'-'.trim($data['country_num_qiye']).'-'.trim($data['phone_qiye']);
                
            }
            
            //移动电话
            if((!empty($data['country_num_qiye1'])&&$data['country_num_qiye1']!='国家编号') && 
                $data['moblie_num_qiye1']
            )
            { 
                
                $data['mobile'] = trim($data['country_num_qiye1']).'-'.trim($data['moblie_num_qiye1']);
            }
            
            $result = $this->member_mod->save_Member_Info($data,$member_id,$act);
            
            if($result['error'])
            {
                $this->ajaxReturn(array('status'=>'no','msg'=>$result['error']));
                exit();
            }
            $this->ajaxReturn(array('status'=>'yes','msg'=>'更新成功!','url'=>U('Home/Member/view')));
            exit();
        }
    }
     
    //会员附件的上传
    public function upload_MemberFile()
    {
        $type = I('get.type','','trim');
        $path = 'Member';
        $result = D('Upload')->file_upload($type,$path);
        if($result['error']==1)
        {
            $this->ajaxReturn(array('error'=>$result['error'],'message'=>$result['message']));
            exit();
        }
        else
        {
            $this->ajaxReturn(array('error'=>$result['error'],'url'=>$result['info']['imgFile']['savepath'].$result['info']['imgFile']['savename']));
            exit();
        }
    }
    
    //保存会员附件
    public function submit_file_url()
    {
        if(IS_AJAX)
        {
            $url = I('post.url','','trim');
            $title = I('post.title','','trim');
            $member_id = I('post.member_id',0,'intval');
            
            $msg = $this->member_mod->update_file($url,$title,$member_id);
            if($msg)
            {
                $this->ajaxReturn(array('status'=>'no','msg'=>$msg));
                exit();
            }
            $this->ajaxReturn(array('status'=>'yes'));
            exit();
        }
    }

    function visa(){
        $action = I('request.action','','trim');

        if($action == 'add'){
            $data = $_POST;

            $country = M('country')->where(array('countryid'=>$data['country_id']))->find();

            $visa_info = M('visa_service')->where(array('country_id' => $data['country_id'],'member_id'=>$this->member_id))->find();
            if($visa_info){
                M('visa_service')->where(array(
                    'visa_id'=>$visa_info['visa_id'],'member_id'=>$this->member_id))->save(array(
                    'country_id'=>$data['country_id'],
                    'country_name'=>$country['cname'],
                    'visa_type'=>$data['visa_type'],
                    'visa_price'=>$data['visa_price'],
                    'service_price'=>$data['service_price'],
                    'return_price_percent'=>$data['return_price_percent']));
            }else{
                M('visa_service')->add(array(
                    'member_id'=>$this->member_id,
                    'country_id'=>$data['country_id'],
                    'country_name'=>$country['cname'],
                    'visa_type'=>$data['visa_type'],
                    'visa_price'=>$data['visa_price'],
                    'service_price'=>$data['service_price'],
                    'return_price_percent'=>$data['return_price_percent'],
                    'dateline'=>time()
                ));
            }

            $this->ajaxReturn(array('status'=>'yes'));
            exit();
        }elseif($action == 'edit'){
            $data = $_POST;

            if(isset($data['visa_id']) && is_numeric($data['visa_id'])){
                $country = M('country')->where(array('countryid'=>$data['country_id']))->find();

                M('visa_service')->where(array(
                    'visa_id'=>$data['visa_id'],'member_id'=>$this->member_id))->save(array(
                    'country_id'=>$data['country_id'],
                    'country_name'=>$country['cname'],
                    'visa_type'=>$data['visa_type'],
                    'visa_price'=>$data['visa_price'],
                    'service_price'=>$data['service_price'],
                    'return_price_percent'=>$data['return_price_percent']));
            }

            $this->ajaxReturn(array('status'=>'yes'));
            exit();

        }elseif($action == 'del'){

            $data = $_GET;
            if(isset($data['visa_id']) && is_numeric($data['visa_id'])) {
                M('visa_service')->where(array('visa_id' => $data['visa_id'],'member_id'=>$this->member_id))->delete();
            }

            header("location: ".U("Home/Member/view"));
        }

        exit;
    }


    function address(){
        $action = I('request.action','','trim');

        if($action == 'add'){
            $data = $_POST;

            if(empty($data['contacter']) || empty($data['phone']) || empty($data['address']) ){
                $this->ajaxReturn(array('status'=>'no','msg' => '邮寄地址信息不完整'));
            }

            M('member_address')->add(array(
                'member_id'=>$this->member_id,
                'address'=>$data['address'],
                'contacter'=>$data['contacter'],
                'phone'=>$data['phone'],
                'dateline'=>time()
            ));

            $this->ajaxReturn(array('status'=>'yes'));
            exit();
        }elseif($action == 'edit'){
            $data = $_POST;

            if(isset($data['address_id']) && is_numeric($data['address_id'])){
                $address = M('member_address')->where(array('address_id'=>$data['address_id']))->find();

                $address && M('member_address')->where(array(
                    'address_id'=>$data['address_id'],'member_id'=>$this->member_id))->save(array(
                    'address'=>$data['address'],
                    'contacter'=>$data['contacter'],
                    'phone'=>$data['phone'],
                    'dateline'=>time()));
            }

            $this->ajaxReturn(array('status'=>'yes'));
            exit();

        }elseif($action == 'del'){

            $data = $_GET;
            if(isset($data['address']) && is_numeric($data['address'])) {
                M('member_address')->where(array('address_id' => $data['address'],'member_id'=>$this->member_id))->delete();
            }

            header("location: ".U("Home/Member/view"));
        }

        exit;
    }
}