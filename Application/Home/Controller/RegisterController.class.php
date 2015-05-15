<?php

/**
 *  CRM注册页面 
 */
namespace Home\Controller;
use Common\Controller\BaseController;

class RegisterController extends BaseController
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
            $username = isset($_POST['username']) ? trim($_POST['username']): '';
            $pwd = isset($_POST['username']) ? trim($_POST['pwd']): '';
            $pwd_confirm = isset($_POST['pwd_confirm']) ? trim($_POST['pwd_confirm']): '';
            $company = isset($_POST['company']) ? trim($_POST['company']): '';
            $contact = isset($_POST['contact']) ? trim($_POST['contact']): '';
            $email = isset($_POST['email']) ? trim($_POST['email']): '';

            $member_type = isset($_POST['member_type']) && $_POST['member_type'] == 2 ? intval($_POST['member_type']): 1;
            $country_id = isset($_POST['country_id']) ? intval($_POST['country_id']): 0;
            $area_id = isset($_POST['area_id']) ? intval($_POST['area_id']): 0;
            $city_id = isset($_POST['city_id']) ? intval($_POST['city_id']): 0;
            $is_show = isset($_POST['is_show']) && in_array($_POST['is_show'],array(0,1,2)) ? intval($_POST['is_show']): 0;

            $country_num = isset($_POST['country_num']) ? trim($_POST['country_num']): '';
            $qu_num = isset($_POST['qu_num']) ? trim($_POST['qu_num']): '';
            $phone = isset($_POST['phone']) ? trim($_POST['phone']): '';

            $mobile_country_num = isset($_POST['mobile_country_num']) ? trim($_POST['mobile_country_num']): '';
            $mobile_num = isset($_POST['mobile_num']) ? trim($_POST['mobile_num']): '';
            $address = isset($_POST['address']) ? trim($_POST['address']): '';

            $introduction = isset($_POST['introduction']) ? trim($_POST['introduction']): '';


            $errors = array();

            if(!$username || preg_match('/^\s*$|^c:\\con\\con$|[%,\*\"\s\t\<\>\&\'\(\)]|\xA1\xA1|\xAC\xA3|^Guest|^\xD3\xCE\xBF\xCD|\xB9\x43\xAB\xC8/is',$username)){
                $errors[] = array('message'=>'用户名包含非法字符','label' => 'username');
            }else if(!preg_match('/^[a-zA-Z][wd_]{5,19}/',$username)){ 
                $errors[] = array('message'=>'只允许6-20位字母数字和下划线组成','label' => 'username');
            }else if(!checkfield('member', 'username', $username)){
                $errors[] = array('message'=>'该用户名已被注册','label' => 'username');
            }

            if (empty($pwd) || strlen($pwd) < 6 || strlen($pwd) > 15){
                $errors[] = array('message'=>'密码应该大于6位小于15位','label' => 'pwd');
            }else if ($pwd_confirm != $pwd){
                $errors[] = array('message'=>'二次确认密码不一致','label' => 'pwd');
            }

            if(!checkfield('member_info','email1',$email)) {
                $errors[] = array('message'=>'该邮箱已被注册','label' => 'email');
            }

            if(empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)!==false){
                $errors[] = array('message'=>'邮箱地址格式不正确','label' => 'email');
            }

            if(!empty($errors)){
                $this->ajaxReturn(array('error'=>101,'response'=>$errors));
            }


            $member_data = array(
                'username'=> $username,
                'pwd'=>$pwd,
                'member_type'=>$member_type,
                'company'=>htmlspecialchars($company),
                'contact'=>htmlspecialchars($contact),
                'telephone'=>$country_num.'-'.$qu_num.'-'.$phone,
                'mobile' => $mobile_country_num.'-'.$mobile_num,
                'email_jiban'=>$email,
                'country_id'=>$country_id,
                'area_id'=>$area_id,
                'city_id'=>$city_id,
                'address'=>htmlspecialchars($address),
                'is_show'=>$is_show,
                'introduction'=>htmlspecialchars($introduction),
            );

            //处理用户数据
            $result=$this->member_mod->reg_Member_Info($member_data);  
            if($result['error'])
            {
                $this->ajaxReturn(array('error'=>102,'response'=>'注册失败'));
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
            $this->ajaxReturn(array('error'=>0,'response'=>'Home/Schedule/index'));
            exit();
        }
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