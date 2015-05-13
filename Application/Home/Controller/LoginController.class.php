<?php

/**
 *  CRM登陆页面 
 */
namespace Home\Controller;
use Think\Controller;

class LoginController extends Controller
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
        if(!session('member_id'))
        {
           $this->display(); 
        }
        else
        {
            $this->redirect('Home/Schedule/index');    
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
                }
                else
                {
                    //登陆信息
                    $update=array('login_time'=>time(),'login_ip'=> get_client_ip());
                
                    if($checked==1)
                    {
                        //建立cookie
                        $update['identifier'] = md5(C('SALT') . md5($_info['username'] . C('SALT')));
                        $update['token'] = md5(uniqid(rand(), TRUE));
                        $update['timeout'] = time() + 60 * 60 * 24 * 7;
                        cookie('auth', $update['identifier'].':'.$update['token'], $update['timeout']);
                    }
                    else 
                    {
                        $update['timeout'] = time() + 60 * 60 * 24 * 7;
                        cookie('auth',$_info['member_id'],$update['timeout']);
                    }
                    $this->member_mod->update_data('member',$update,$_info['member_id']);
                    M('member')->where('member_id='.$_info['member_id'])->setInc('login_times');
                   
                    $this->ajaxReturn(array('status'=>'yes','msg'=>'','url'=>U('Home/Schedule/index')));
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
        session('member_id',null);
        cookie('auth',null);
        $this->redirect('Home/Login/index');
    }
}




?>