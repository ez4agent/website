<?php
/**
 *  CRM系统前台基类 
 */

namespace Common\Controller;
use Think\Controller;

class FrontbaseController extends BaseController
{

    public $login_user = array();
 
    public function __construct()
    {
        parent::__construct();

        if($this->auth()->isGuest()){
            $this->redirect('Home/Login/index');
            exit();
        }

        $this->login_user = $this->auth()->getStates();

        $this->event_status();

        //获取CRM系统栏目
        $this->assign('meun',$this->meun(CONTROLLER_NAME));
    }

    //日程统计数量
    public function event_status($stu_id = 0)
    {
        $now = D('Schedule')->get_event_count('now',$this->auth()->member_id,$stu_id);
        $this->assign('now',$now);
        $plan_count = D('Schedule')->get_event_count('plan',$this->auth()->member_id,$stu_id);
        $this->assign('plan_count',$plan_count);
        $past = D('Schedule')->get_event_count('over',$this->auth()->member_id,$stu_id);
        $this->assign('past',$past);

        $this->assign('event_num',$now + $past + $plan_count);
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