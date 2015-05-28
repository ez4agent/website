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

        $act = isset($_POST['act']) ? $_POST['act'] : '';
        $errors = array();

        if(IS_AJAX && $act == 'sendmail') {

            $email = isset($_POST['email']) ? $_POST['email'] : '';
            if(empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)!==false){
                $errors = array('message'=>'邮箱地址格式不正确','label' => 'usermail');
            }else if(checkfield('member_info','email1',$email)){
                $errors = array('message'=>'该邮箱没有被注册','label' => 'usermail');
            }

            if(!empty($errors)){
                $this->ajaxReturn(array('error'=>101,'response'=>$errors));
                exit();
            }

            $member_info = D('Member')->get_member_bymail($email);

            $code = generate_key(6,true);
            $token = md5($member_info['email1'].$member_info['pwd'].session_id());

            session('findpass',array(
                'code' => $code,
                'token'=>$token,
                'uid' => $member_info['member_id'],
                'email' => $member_info['email1']
            ));

            $html = "您的验证码是".$code."，在5分钟内有效";
            send_email($member_info['email1'],'ez4agent邮件验证码',$html,array(),'noreply');


            $this->ajaxReturn(array('error'=>0,'response'=>array('usermail'=>$member_info['email1'])));
            exit();
        }elseif(IS_AJAX && $act == 'resendmail'){

            $findpass = session('findpass');

            if($findpass){
                $code = generate_key(6,true);

                $html = "您的验证码是".$code."，在5分钟内有效";
                send_email($findpass['email'],'ez4agent邮件验证码',$html,array(),'noreply');

                $findpass['code'] = $code;
                session('findpass',$findpass);

                $this->ajaxReturn(array('error'=>0,'response'=>array()));
                exit();

            }

            $this->ajaxReturn(array('error'=>0,'response'=>array('message'=>'操作已超时，请返回重新找回密码')));
            exit();

        }elseif(IS_AJAX && $act == 'checkcode'){
            $mailcode = isset($_POST['mailcode']) ? $_POST['mailcode'] : '';

            $findpass = session('findpass');
            if( $findpass['code'] != $mailcode){
                $this->ajaxReturn(array('error'=>101,'response'=>array('message'=>'验证码不正确')));
                exit();
            }

            $this->ajaxReturn(array('error'=>0,'response'=>array('token'=>$findpass['token'])));
            exit();

        }elseif(IS_AJAX && $act == 'changepass'){
            $newpass = isset($_POST['newpass']) ? trim($_POST['newpass']) : '';
            $newpass_cfm = isset($_POST['newpass_cfm']) ? $_POST['newpass_cfm'] : '';
            $onice = isset($_POST['onice']) ? $_POST['onice'] : '';

            $findpass = session('findpass');

            if (empty($newpass) || strlen($newpass) < 6 || strlen($newpass) > 15){
                $errors = array('message'=>'密码应该大于6位小于15位','label' => 'newpass');
            }else if ($newpass_cfm != $newpass){
                $errors = array('message'=>'二次确认密码不一致','label' => 'newpass_cfm');
            }else if($findpass['token'] != $onice){
                $errors = array('message'=>'无效请求');
            }

            if(!empty($errors)){
                $this->ajaxReturn(array('error'=>101,'response'=>$errors));
                exit();
            }

            $member_info = D('Member')->get_member_bymail($findpass['email']);
            if(!empty($member_info) && $member_info['member_id'] == $findpass['uid']){
                M('member')->where(array('member_id'=> $member_info['member_id']))->setField('pwd',md5($newpass));

                session('findpass',null);
                
                $this->ajaxReturn(array('error'=>0,'response'=>''));
                exit();
            }

            $this->ajaxReturn(array('error'=>101,'response'=>array('message'=>'充值密码失败')));
            exit();

        }

        $this->display();
    }
}