<?php


namespace Home\Controller;
use Common\Controller\BaseController;
use Common\Util\Auth;

class AuthController extends BaseController
{
    var $member_mod;  
    public function __construct()
    {
        parent::__construct();
        //实例化会员模型
        $this->member_mod=D('Member');
    }

    public function certifiemail()
    {

        $authcode = isset($_GET['authcode']) ? trim($_GET['authcode']) : '';

        if($authcode){
            $certifie_message = '';

            $authcode = authcode(rawurldecode($authcode),'DECODE','@ez4agent-checkmail');

            $authArr = explode('-', $authcode);

            if(count($authArr) == 2){
                list($username,$password) = $authArr;

                $member = $this->member_mod->check_login($username);
                if(!$member || $member['pwd'] != $password){

                    if($member['certifiemail'] == 1) {
                        $certifie_message = '邮箱地址已验证，请直接登陆';
                    }else{

                        $this->member_mod->update_data('member',array('certifiemail'=> 1), $member['member_id']);

                        session('signup_user',null);

                        $auth = new Auth();
                        $auth->logging($member['member_id'],true);

                        M('member')->where('member_id='.$member['member_id'])->setInc('login_times');

                        $this->redirect('Home/Login/index');
                    }
                }else{
                    $certifie_message = '邮箱验证串已过期或无效';
                }
            }else{
                $certifie_message = '邮箱验证串已过期或无效';
            }

            $this->assign('title','邮箱验证');
            $this->assign('step','authcode');
            $this->assign('certifie_message',$certifie_message);
        }else{

            $signup_user = session('signup_user');
            if(!$signup_user){
                $this->redirect('Home/Login/index');
            }

            $username = $signup_user['username'];
            $member = $this->member_mod->check_login($username);
            if(!$member){
                session('signup_user',null);
                $this->redirect('Home/Login/index');
            }

            $member_info = $this->member_mod->get_Member_Info($member['member_id']);

            $authcode = authcode(join('-',array_values($signup_user)),'ENCODE','@ez4agent-checkmail',3600*24);
            $link = U('Home/Auth/certifiemail',array('authcode'=>rawurlencode($authcode)),true,true);
            $html = <<<EOT
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
  <title>感谢您注册ez4agent</title>
</head>
<body>
  <div style="width: 640px; font-family: Arial, Helvetica, sans-serif; font-size: 11px;">
    <h1>您好，{$username}</h1>
    <p>感谢您注册ez4agent ！</p>
    请点击下面的链接完成注册：<br/>
    <a href="$link" target="_blank">$link</a><br/>
    如果以上链接无法点击，请把上面网页地址复制到浏览器地址栏中打开
  </div>
</body>
</html>
EOT;
            send_email($member_info['email1'],'ez4agent 账号激活邮件',$html,array(),'noreply');

            $maildaemon = 'http://mail.'.ltrim(strstr($member_info['email1'],'@'),'@');

            $this->assign('title','邮箱验证');
            $this->assign('step','postemail');
            $this->assign('usermail',$member_info['email1']);
            $this->assign('maildaemon',$maildaemon);
        }


        $this->display();
    }


    public function findpass()
    {

        if(IS_AJAX) {

            $this->ajaxReturn(array('status'=>'yes','msg'=>'','url'=>U('Home/Schedule/index')));
            exit();
        }

        $this->display();
    }
}