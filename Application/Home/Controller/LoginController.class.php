<?php

/**
 *  CRM登陆页面 
 */
namespace Home\Controller;
use Common\Controller\BaseController;

class LoginController extends BaseController
{
    var $member_mod;  
    public function __construct()
    {
        parent::__construct();
        //实例化会员模型
        $this->member_mod=D('Member');
    }
    
    /**
     *  登陆页面 
     */
    public function index()
    {
        if($this->auth()->isGuest())
        {
           $this->display(); 
        }
        else
        {
            $this->redirect('Home/School/index');
        }
    }
    
    /**
     *  验证用户登陆 
     */
    public function checkLogin()
    {
        if(IS_AJAX)
        {
            $username = trim($_POST['username']);
            $pwd = trim($_POST['pwd']);
            $checked = I('post.checked','0','intval');
            if(empty($username) || empty($pwd))
            {
                $this->ajaxReturn(array('status'=>'no','msg'=>'请输入用户名或者密码！'));
                exit();
            }
            
            $_info = $this->member_mod->check_login($username);

            if($_info)
            {
                if($_info['pwd']!=md5($pwd))
                {
                    $this->ajaxReturn(array('status'=>'no','msg'=>'你输入的密码错误,请重新输入！'));
                    exit();
                }
                elseif ($_info['is_open']==0)
                {
                    $this->ajaxReturn(array('status'=>'no','msg'=>'该用户名已被锁定,请联系管理员解锁！'));
                    exit();

                }elseif($_info['certifiemail']==0){

                    $_SESSION['signup_user'] = array(
                        'username' => $_info['username'],
                        'password' => $pwd
                    );

                    $this->ajaxReturn(array('status'=>'yes','msg'=>'','url'=>'/index.php?m=Home&c=Auth&a=certifiemail'));
                    exit();
                }
                else
                {

                    $this->auth()->logging($_info['member_id'],$checked);

                    //登陆信息
                    $update=array('login_time'=>time(),'login_ip'=> get_client_ip());
                
                    $this->member_mod->update_data('member',$update,$_info['member_id']);
                    M('member')->where('member_id='.$_info['member_id'])->setInc('login_times');
                   
                    $this->ajaxReturn(array('status'=>'yes','msg'=>'','url'=>U('Home/School/index')));
                    exit();
                } 
            }
            else 
            {
                $this->ajaxReturn(array('status'=>'no','msg'=>'你输入的用户名不存在,请注册！'));
                exit();
            }
        }
    }
    
    /**
     *  退出 
     */
    public function logout()
    {
        $this->auth()->logout();
        $this->redirect('Home/Login/index');
    }
}




?>