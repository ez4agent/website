<?php

/**
 *  CRM系统--日程计划 
 */

namespace Home\Controller;
use Common\Controller\FrontbaseController;
use Common\Util\Calendar;

class ScheduleController extends FrontbaseController
{
    var $schedule_mod;
    var $member_id;
    
    public function __construct()
    {
        parent::__construct();
        $this->schedule_mod = D('Schedule');
        $this->member_id = $this->auth()->member_id;
        
    }
    
    public function index()
    {
        //条件
        $where['member_id'] = $this->member_id;
        //页码
        $page = isset($_GET['P'])?intval($_GET['P']):1;
        $pageSzie = 15;
        //类别
        $type = isset($_GET['type'])?trim($_GET['type']):'now';
        
        if($type=='plan')
        {
            $where['date_value'] = array('GT',date('Y/m/d',time()));
        }
        elseif($type=='over')
        {
            $where['date_value'] = array('LT',date('Y/m/d',time()));
        }
        elseif($type=='now') 
        { 
            $where['date_value'] = date('Y/m/d',time());
        }
        
        //时间
        $date = !empty($_GET['date'])?trim($_GET['date']):'';
        if($date){
            $where['date_value'] = $date;
        }
        
        //年月
        $year =!empty($_GET['year'])?trim($_GET['year']):'';
        $month = !empty($_GET['month'])?trim($_GET['month']):'';
        
        if($year && $month)
        {
            $day = cal_days_in_month(CAL_GREGORIAN, $month, $year);
            $start = $year.'/'.$month.'/'.'01';
            $end = $year.'/'.$month.'/'.$day;
            $where['date_value']=array('between',array($start,$end));
        }
        
        //学生ID
        $stu_id = isset($_GET['stuid'])?intval($_GET['stuid']):0;
        if($stu_id!=0)
        {
            $where['stu_id'] = $stu_id;
        }
        $count = M('schedule_event')->where($where)->count();
        $Page = new \Think\Page($count,$pageSzie);
        $show = $Page->show();
        
        $list = $this->schedule_mod->get_event_list($where,$page,$pageSzie);
        if(!empty($where))
        {
            foreach($where as $key=>$val) {
                $Page->parameter[$key] = urlencode($val);
            }
        }
        $str = D('Stu')->select_stu($stu_id,$this->member_id);
        //$this->get_num($stu_id);
        $this->assign('str',$str);
        $this->assign('type',$type);
        $this->assign('now_time',date('Y/m/d',time()));
        $this->assign('date',$date);
        $this->assign('list',$list);
        $this->assign('page',$show);
        $this->display();    
    }
    
    
    /**
     *  添加事件框 
     */
    public function event_form()
    {
        if(IS_AJAX)
        {
            $stu_id = I('post.stu_id',0,'intval');
            $event_id = I('post.event_id',0,'intval');
            $info = array();
            if($event_id)
            {
                $info = $this->schedule_mod->get_event_info($event_id);
                if($info['is_use']==0)
                {
                    $str = D('Stu')->select_stu($info['stu_id'],$this->member_id);
                }
                else 
                {
                    $stu_info = D('Stu')->get_StuInfo($info['stu_id']);
                    $str = $stu_info['stu_name'];
                }
            }
            else 
            {
                $str = D('Stu')->select_stu($stu_id,$this->member_id);
            }
            $this->ajaxReturn(array('status'=>'yes','info'=>$str,'event'=>$info));
        }
    }
    
    /**
     *  添加事件 
     */
    public function add_events()
    {
       if(IS_AJAX)
       {
           $data = $_POST;
           if($data['event_id'])
           {
               $data['member_id'] = $this->member_id;
               $data['is_use'] = isset($data['is_use'])?intval($data['is_use']):0;
               $res = $this->schedule_mod->editData($data,'edit');
               if($res['msg'])
               {
                   $this->ajaxReturn(array('status'=>'no',$res['msg']));
                   exit;
               }
               $this->ajaxReturn(array('status'=>'yes','msg'=>'修改成功！'));
               exit;
           }
           else
           {
                if(!isset($data['title'])){
                   $this->ajaxReturn(array('status'=>'no','请输入主题'));
                   exit;
                }

                if(!isset($data['stu_id'])){
                   $this->ajaxReturn(array('status'=>'no','请选择学生'));
                   exit;
                }

                if(!isset($data['date_value']) || !strtotime($data['date_value'])){
                   $this->ajaxReturn(array('status'=>'no','请输入日期'));
                   exit;
                }
                

               $timenow = time();
               $data['member_id'] = $this->member_id;
               $data['addtime'] = $timenow;

               $data['is_use'] = 0;
               $data['finishtime'] = 0;
               if(isset($data['is_use']) && intval($data['is_use']) > 0){
                    $data['is_use'] = 1;
                    $data['finishtime'] = $timenow;
               }
               
               $res = $this->schedule_mod->editData($data,'add');
               if($res['msg'])
               {
                   $this->ajaxReturn(array('status'=>'no',$res['msg']));
                   exit;
               }
               
               $this->ajaxReturn(array('status'=>'yes','msg'=>'添加成功！'));
               exit;
           }
       } 
    }
    
    //更改日程安排 
    public function change_use()
    { 
        if(IS_AJAX)
        { 
            $id = I('post.id',0,'intval');
            $info = $this->schedule_mod->get_event_info($id);
            if(!$info)
            {
                echo $this->ajaxReturn(array('status'=>'no','msg'=>'提醒不存在！'));
                exit;
            }
            else 
            {
                if(!M('schedule_event')->where('event_id='.$id)->setField(array('is_use'=>1,'finishtime'=>time())))
                {
                    echo $this->ajaxReturn(array('status'=>'no','msg'=>'更新失败！'));
                }
                else
                {
                    echo $this->ajaxReturn(array('status'=>'yes'));
                }
                exit();
            }
        }
    }
    
    //日历加载
    public function calendar_load()
    {
        $Calendar1 = new Calendar();
        echo $Calendar1->out();
    }
}

?>