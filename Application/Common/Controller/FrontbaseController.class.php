<?php
/**
 *  CRM系统前台基类 
 */

namespace Common\Controller;
use Think\Controller;

class FrontbaseController extends BaseController
{

    public $login_user = array();
    public $apply_event_stu_groups = array();
 
    public function __construct()
    {
        parent::__construct();

        if($this->auth()->isGuest()){
            $this->redirect('Home/Login/index');
            exit();
        }

        $this->login_user = $this->auth()->getStates();

        $this->event_status();

        $apply_event_stu_count = array();
        $apply_event_total_count = 0;

        if(!$this->auth()->isGuest()){
            $partner_member =C('SYSTEM_PARTNER_MEMBER');
            $apply_ids = $stu_ids = array();
            if($this->auth()->member_id == $partner_member){
                $receive_rows = M('stu_receive')->where(array('member_id'=>$partner_member))->select();
            }else{
                $receive_rows = M('stu_receive')->where(array('from_member_id'=>$this->auth()->member_id))->select();
            }

            foreach($receive_rows as $r){
                $apply_ids[] = $r['apply_id'];
                $stu_ids[$r['apply_id']] =  $r['stu_id'];
            }

            if($apply_ids){
                $apply_event_group = array();
                $operate_log = M('stu_apply_operate_log')->where(array('apply_id'=>array('IN',$apply_ids),'has_readed'=>0,'operate_user_id'=>array('NEQ',$this->auth()->member_id)))->group('apply_id')->field('count(1) as event_num,apply_id')->select();
                foreach($operate_log as $r){
                    $apply_event_group[$r['apply_id']] = $r['event_num'];
                }

                foreach($stu_ids as $apply_id => $stu_id){
                    if(isset($apply_event_group[$apply_id])){
                        $apply_event_stu_count[$stu_id] = $apply_event_group[$apply_id];
                        $apply_event_total_count += (int)$apply_event_group[$apply_id];
                    }
                }
            }
        }

        $this->apply_event_stu_count = $apply_event_stu_count;
        $this->assign('apply_event_total_count',$apply_event_total_count);
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