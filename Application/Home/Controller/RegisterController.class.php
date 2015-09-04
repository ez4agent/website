<?php

/**
 *  CRM注册页面 
 */
namespace Home\Controller;
use Common\Controller\BaseController;
use Common\Util\Auth;

class RegisterController extends BaseController
{
    var $member_mod;
    
    public function __construct()
    {
        parent::__construct();
        //实例化会员模型
        $this->member_mod=D('Member');
    }

    public $invite_codes = array(
        '38375873@qq.com',
        '768842306@qq.com',
        '619343631@qq.com',
        '993766477@qq.com',
        '805922289@qq.com',
        '376894471@qq.com',
        '1321251777@qq.com',
        '1145412247@qq.com',
        '806007489@qq.com',
        '63431058@qq.com',
        '150046557@qq.com',
        '279918923@qq.com',
        '282651986@qq.com',
        '86547450@qq.com',
        '448661685@qq.com',
        '49785845@qq.com',
        '9715634@qq.com'
    );

    /**
     *  注册页面 
     */
    public function index()
    {
        if(!$this->auth()->member_id){
            $invite_code = isset($_GET['invite_code']) ? trim($_GET['invite_code']) : '';

            if(!$invite_code || !in_array($invite_code,$this->invite_codes)){
                $this->error('无效的邀请码');
            }

            $this->assign('invite_code',$invite_code);
            $this->assign('country',country());
            $this->display();
        }else{
            $this->redirect('Home/Schedule/index');
        }
    }

    public function checkInviteCode(){

        $invite_code = isset($_REQUEST['invite_code']) ? trim($_REQUEST['invite_code']) : '';

        if($invite_code && in_array($invite_code,$this->invite_codes)){
            $this->ajaxReturn(array('error'=>0,'response'=>U('Home/Register/index',array('invite_code'=>$invite_code))));
            exit();
        }

        $this->ajaxReturn(array('error'=>1,'response'=>'无效的邀请码'));
        exit();
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
            $realname = isset($_POST['realname']) ? trim($_POST['realname']): '';
            $email = isset($_POST['email']) ? trim($_POST['email']): '';

            $member_type = isset($_POST['member_type']) && $_POST['member_type'] == 2 ? intval($_POST['member_type']): 1;
            $country_id = isset($_POST['country_id']) ? intval($_POST['country_id']): 0;
            $area_id = isset($_POST['area_id']) ? intval($_POST['area_id']): 0;
            $city_id = isset($_POST['city_id']) ? intval($_POST['city_id']): 0;
            //$is_show = isset($_POST['is_show']) && in_array($_POST['is_show'],array(0,1,2)) ? intval($_POST['is_show']): 0;
            $is_show = 1;
            $country_num = isset($_POST['country_num']) ? trim($_POST['country_num']): '';
            $qu_num = isset($_POST['qu_num']) ? trim($_POST['qu_num']): '';
            $phone = isset($_POST['phone']) ? trim($_POST['phone']): '';

            $mobile_country_num = isset($_POST['mobile_country_num']) ? trim($_POST['mobile_country_num']): '';
            $mobile_num = isset($_POST['mobile_num']) ? trim($_POST['mobile_num']): '';
            $address = isset($_POST['address']) ? trim($_POST['address']): '';

            $introduction = isset($_POST['introduction']) ? trim($_POST['introduction']): '';
            $invite_code = isset($_POST['invite_code']) ? trim($_POST['invite_code']): '';

            $agree_terms = isset($_POST['agree_terms']) && $_POST['agree_terms'] ? 1: 0;

            if(!$invite_code || !in_array($invite_code,$this->invite_codes)){
                $this->ajaxReturn(array('error'=>102,'response'=>'无效的邀请码'));
                exit();
            }

            $errors = array();

            if(!$username || preg_match('/^\s*$|^c:\\con\\con$|[%,\*\"\s\t\<\>\&\'\(\)]|\xA1\xA1|\xAC\xA3|^Guest|^\xD3\xCE\xBF\xCD|\xB9\x43\xAB\xC8/is',$username)){
                $errors[] = array('message'=>'用户名包含非法字符','label' => 'username');
            }else if(!preg_match('/^[a-zA-Z0-9_]{5,19}$/',$username)){ 
                $errors[] = array('message'=>'只允许6-20位字母数字和下划线组成','label' => 'username');
            }else if(!checkfield('member', 'username', $username)){
                $errors[] = array('message'=>'该用户名已被注册','label' => 'username');
            }

            if (empty($pwd) || strlen($pwd) < 6 || strlen($pwd) > 15){
                $errors[] = array('message'=>'密码应该大于6位小于15位','label' => 'pwd');
            }else if ($pwd_confirm != $pwd){
                $errors[] = array('message'=>'二次确认密码不一致','label' => 'pwd');
            }

            if(!$country_id || !$area_id || !$city_id){
                $errors[] = array('message'=>'请选择地区','label' => 'from');
            }

            if(empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)!==false){
                $errors[] = array('message'=>'邮箱地址格式不正确','label' => 'email');
            }else if(!checkfield('member_info','email1',$email)){
                $errors[] = array('message'=>'该邮箱已被注册','label' => 'email');
            }

            if($member_type == 2 && empty($realname)){
                $errors[] = array('message'=>'请输入真实姓名','label' => 'realname');
            }

            if($member_type == 1 && empty($phone)){
                $errors[] = array('message'=>'请输入固定电话','label' => 'phone');
            }elseif($member_type == 2 && empty($mobile_num)){
                $errors[] = array('message'=>'请输入移动电话','label' => 'mobile_num');
            }

            if(!$agree_terms){
                $errors[] = array('message'=>'请接受服务条款','label' => 'agree_terms');
            }

            if(!empty($errors)){
                $this->ajaxReturn(array('error'=>101,'response'=>$errors));
            }


            $member_data = array(
                'username'=> $username,
                'pwd'=>$pwd,
                'member_type'=>$member_type,
                'company'=>htmlspecialchars($company),
                'contact'=> $member_type == 2 ? htmlspecialchars($realname) : htmlspecialchars($contact),
                'telephone'=>$country_num.'-'.$qu_num.'-'.$phone,
                'mobile' => $mobile_country_num.'-'.$mobile_num,
                'email_jiban'=>$email,
                'country_id'=>$country_id,
                'area_id'=>$area_id,
                'city_id'=>$city_id,
                'address'=>htmlspecialchars($address),
                'is_show'=>$is_show,
                'introduction'=>htmlspecialchars($introduction),
                'login_time'=>time(),
                'login_ip'=> get_client_ip(),
                'login_times' => 1,
                'invite_code' => $invite_code
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



                if($invite_code){
                    $this->ajaxReturn(array('error'=>0,'invite_confirm'=>1,'response'=>'账户审核需大约1-3个工作日， 谢谢您的注册'));
                }else{
                    session('signup_user', array(
                        'username' => $username,
                        'password' => $pwd
                    ));
                    $this->ajaxReturn(array('error'=>0,'response'=>'/index.php?m=Home&c=Auth&a=certifiemail'));
                }

                exit();
                /*
                //登陆信息
                $auth = new Auth();
                $auth->logging($_info['member_id'],$checked);

                M('member')->where('member_id='.$result['data'])->setInc('login_times');

                $this->ajaxReturn(array('error'=>0,'response'=>'/index.php?m=Home&c=Schedule&a=index'));
                exit();
                */
            }

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
        
        if($this->auth()->member_id)
        {
            $_email =M('member_info')->where('member_id='.$this->auth()->member_id)->getfield('email1');
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