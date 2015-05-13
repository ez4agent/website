<?php
/**
 *  CRM--日程计划 
 */

namespace Home\Model;
use Think\Model;

class ScheduleModel extends Model
{
    //按日期统计数量
    public function get_count_num($where)
    { 
        
        $count = M('schedule_event')->where($where)->count();
        return $count;
    }
    
    public function get_event_info($event_id)
    {
        $_info = array();
        $_info = M('schedule_event')->where('event_id='.$event_id)->find();
        return $_info;
    }
    
    /**
     *  添加事件或者提醒 
     *  @param array data
     *  @return intval id
     */
    public function editData($data,$type='add')
    {
        $result = array('data'=>'','msg'=>'');
        if(!is_array($data)){
            return false;
        }
        if($type=='add')
        {
            $id = M('schedule_event')->add($data);
            if(!$id){
                $result['msg'] = '添加失败！';
            }
            else
            {
                $result['data']=$id;
            }
        }
        else if($type=='edit') 
        {
            if(empty($data['event_id']))
            {
                return false;
            }
            
            if(M('schedule_event')->where('event_id='.$data['event_id'])->save($data)===false)
            {
                $result['msg'] = '修改失败！';
            }
        }
        return $result;
    }
    
    /**
     *  统计事件数量
     *  @param array where
     *  @return intval 
     */
    public function get_event_count($type='now',$member_id,$stu_id='')
    {
        $count=0; 
        $where['member_id']=$member_id;
        $where['is_use']=0;
        $now = date('Y/m/d',time());
        if($stu_id)
        {
            $where['stu_id']=$stu_id;
        }
        if($type=="now")
        {
            $where['date_value']=$now; 
        }
        elseif($type=="over")
        {
            $where['date_value']=array('LT',$now);
        }
        elseif($type=="plan")
        {
            $where['date_value']=array('GT',$now);
        }
        $count = M('schedule_event')->where($where)->count();
        
        return $count;
    }
    
    
   
    //事件列表（用于日程计划）
    public function get_event_list($where,$page,$count)
    {
        $list = array();
        $list= M('schedule_event')->where($where)
               ->order('is_use asc,date_value desc')->page($page.','.$count)
               ->select();
        if($list)
        {
            foreach($list as $key =>$val)
            {
               $stu_info = D('Stu')->get_StuInfo($val['stu_id']);
               $list[$key]['stu_name'] = $stu_info['stu_name'];
            }
        }
        return $list;
    }
    
}



?>