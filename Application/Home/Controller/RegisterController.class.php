<?php

/**
 *  CRM注册页面 
 */
namespace Home\Controller;
use Think\Controller;

class RegisterController extends Controller
{
    var $member_mod;
    
    public function __construct()
    {
        parent::__construct();
        //实例化会员模型
        $this->member_mod=D('Member');
    }
    
    /**
     *  注册页面 
     */
    public function index()
    {
        if(!session('member_id')){
            $this->assign('country',country());
            $this->display();
        }else{
            $this->redirect('Home/Schedule/index');
        }
    }
    
    /**
     *  注册操作 
     */
    public function regact()
    {
        if(IS_AJAX)
        {
            //处理用户数据
            $data = $this->get_member_data($_POST);
            $result=$this->member_mod->reg_Member_Info($data);  
            if($result['error'])
            {
                $this->ajaxReturn(array('status'=>'no','msg'=>$result['error']));
                exit();
            } 
            else 
            {
                //登陆信息
                $update=array('login_time'=>time(),'login_ip'=> get_client_ip());
                $this->member_mod->update_data('member',$update,$result['data']);
                M('member')->where('member_id='.$result['data'])->setInc('login_times');
                session('member_id',$result['data']);
            }
            $this->ajaxReturn(array('status'=>'yes','msg'=>'注册成功！','url'=>U('Home/Schedule/index')));
            exit();
        }
    }
    
    /** 
     *  处理数据 
     */
    public function get_member_data($data=array())
    {
        $member_data = array();
        if(!is_array($data))
        {
            return false;
        }
        
        if($data['member_type']==1) //企业
        {
            $member_data = array(
                'username'=>trim($data['username']),
                'pwd'=>trim($data['pwd']),
                'member_type'=>intval($data['member_type']),
                'company'=>trim($data['company']),
                'contact'=>trim($data['contact']),
                'telephone'=>trim($data['country_num_qiye']).'-'.trim($data['qu_num_qiye']).'-'.trim($data['phone_qiye']),
                'email_jiban'=>trim($data['email']),
                'country_id'=>intval($data['country_id']),
                'area_id'=>intval($data['area_id']),
                'city_id'=>intval($data['city_id']),
                'address'=>trim($data['address']),
                'is_show'=>intval($data['is_show']),
                'introduction'=>trim($data['introduction']),
            );
            
            if($data['country_num_qiye1']!='国家编号' && $data['moblie_num_qiye1'])
            {
                $member_data['mobile']=trim($data['country_num_qiye1']).'-'.trim($data['moblie_num_qiye1']);
            }
        }
        elseif($data['member_type']==2) //个人
        { 
            $member_data = array(
                'username'=>trim($data['username']),
                'pwd'=>trim($data['pwd']),
                'member_type'=>intval($data['member_type']),
                'contact'=>trim($data['contact']),
                'mobile'=>trim($data['country_num_geren1']).'-'.trim($data['moblie_num_geren1']),
                'email_jiban'=>trim($data['email']),
                'country_id'=>intval($data['country_id']),
                'area_id'=>intval($data['area_id']),
                'city_id'=>intval($data['city_id']),
                'address'=>trim($data['address']),
                'is_show'=>intval($data['is_show']),
                'introduction'=>trim($data['introduction']),
            );
            
            if($data['country_num_geren']!='国家编号' && $data['qu_num_geren']!='区号' && $data['phone_geren'])
            {
                $member_data['telephone']=trim($data['country_num_geren']).'-'.trim($data['qu_num_geren']).'-'.trim($data['phone_geren']);
            }
        }
        
        return $member_data;
    }
    
    /**
     *  验证用户名重名 
     */
    public function check_unique_Name()
    {
        if(IS_AJAX)
        {
            $username =I('post.username','','trim');
            if(!checkfield('member', 'username', $username))
            {
                $this->ajaxReturn(array('status'=>'no'));
                exit();
            }
            $this->ajaxReturn(array('status'=>'yes'));
            exit();
        }
    }
    
    /**
     *  验证会员邮箱是否重复 
     */
    public function check_unique_Email()
    {
        $email =I('post.email','','trim');
        
        if(session('member_id'))
        {
            $_email =M('member_info')->where('member_id='.session('member_id'))->getfield('email1');
            if($email != $_email)
            {
                if(!checkfield('member_info','email1',$email))
                {
                    $this->ajaxReturn(array('status'=>'no'));
                    exit();
                }        
            }
        }
        else 
        {
            if(!checkfield('member_info','email1',$email))
            {
                $this->ajaxReturn(array('status'=>'no'));
                exit();
            }
        }
         
        $this->ajaxReturn(array('status'=>'yes'));
        exit();
    }
}
?>