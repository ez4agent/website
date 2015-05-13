<?php
/**
 *  CRM系统前台基类 
 */

namespace Common\Controller;
use Think\Controller;

class FrontbaseController extends Controller
{
    public function __construct()
    {
        parent::__construct();  
        //验证是否永久登陆  
        $this->check_cookie();
        if(!session('member_id'))
        {
            $this->redirect('Home/Login/index');
            exit();
        }
        //获取CRM系统栏目
        $this->assign('meun',$this->meun(CONTROLLER_NAME));
    }
    
    /**
     *  验证永久登陆 
     */
    public function check_cookie()
    {        
        $cookie = cookie('auth');
        $flag = strstr($cookie,':');
        if($flag)
        {
            $array = explode(':', $cookie);
            $map['identifier']=$array[0];
            $map['token']=$array[1];
            $_info = M('member')->where($map)->field('member_id')->find();
        }
        else 
        {
            $_info = M('member')->where(array('member_id='.$cookie))->field('member_id')->find();
        }
        
        session('member_id',$_info['member_id']);
        
        return true;
    }
    
    /**
     *  获取CRM系统栏目导航 
     */
    public function meun($controller)
    {
        $meun = D('Home/Menu')->get_menu();
        
        foreach ($meun as $key=>$val)
        {
            if($val['controller']==$controller)
            {
                $meun[$key]['select'] = 1;
            }
        }
        return $meun;
    }
    
}