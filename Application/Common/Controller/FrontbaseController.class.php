<?php
/**
 *  CRM系统前台基类 
 */

namespace Common\Controller;
use Think\Controller;
use Common\Util\Auth;

class FrontbaseController extends Controller
{

    public $login_user = array();
    public $auth;

    public function __construct()
    {
        parent::__construct();

        $this->auth = new Auth();

        if($this->auth->isGuest()){
            $this->redirect('Home/Login/index');
            exit();
        }

        $this->login_user = $this->auth->getStates();

        //获取CRM系统栏目
        $this->assign('meun',$this->meun(CONTROLLER_NAME));
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