<?php
/**
 *  申请操作 
 */
namespace Home\Model;
use Think\Model;

class ApplyModel extends Model
{
    var $edu;
    
    public function __construct()
    {
        parent::__construct();
        
        $this->edu = array(
            '专科','本科','研究生'
        );    
    }
    
    /**
     *  判断是否是自己的合作院校 
     */
    public function check_Cooperation($member_id,$college_id)
    { 
       $num=0;
       if($member_id && $college_id)
       {
          $num = M('partner_college')->where(array('member_id'=>$member_id,'college_id'=>$college_id))
                                     ->count();
       }
       return $num;
    }
    
    /**
     *  验证重复申请 ，自生不能申请自院
     */
    public function check_repeat($member_id,$college_id)
    {
        $map = array('member_id'=>$member_id,'college_id'=>$college_id);
        $num = M('partner_college')->where($map)->count();
        if($num)
        {
            return false;
        }
        
        return true;
    }
    
    /**
     *  验证一个学生同一所院校同一个学历重复申请
     */
    public function check_repeat1($stu_id,$college_id,$commission_id)
    {
        $apply_name = getField_value('partner_college_commission', 'education',array('commission_id'=>$commission_id));
        $map = array('stu_id'=>$stu_id,'college_id'=>$college_id,'apply_name'=>$apply_name);
        $num = M('stu_apply')->where($map)->count();
        if($num)
        {
            return false;
        }
        
        return true;
    }
    
    /**
     *  根据学历验证 （一个学生只能成功报考2所院校）预科 语言除外 
     */
    public function check_edu($edu,$stu_id)
    {
        $msg = '';
        if(in_array($edu, $this->edu))
        {
            $condition=array(
                   'stu_id'=>$stu_id,
                'apply_name'=>$edu,
                'is_success'=>1
            );
            $count = M('stu_apply')->where($condition)->group(college_id)->count();
            if($count==C('MAX_COLLEGE_NUM'))
            {
                $msg = '该学生已经成功申请2所院校，不能再申请了';
            }
        }
        else
        {
            $conut = getField_value('stu_apply_count', 'times',array('stu_id'=>$stu_id));
            if($count==C('MAX_APPLY_TIMES'))
            {
                $msg = '该学生已经成功申请5次,不能再提出申请';
            }
        }       
        
        return $msg;      
    }
    
    /**
     *  申请操作 
     */
    public function apply_action($data,$commission_id,$apply_id1,$file='')
    {
        $apply_id = M('stu_apply')->add($data);
        //获取学历信息
        $share = D('Partner')->get_share_college($data['college_id'],$apply_id1);        
        if($share)
        {
            foreach ($share[$commission_id]['value1'] as $key =>$val)
            {
               $array[$key] = array(
                   'times'=>$val['times'],
                   'sharing_ratio'=>$val['sharing_ratio'],
               );
            }
        }

        if($apply_id)
        {
            //加入学历信息
            $edu= array(
                'apply_id'=>$apply_id,
                'education'=>$share[$commission_id]['education'],
                'pay_type'=>$share[$commission_id]['pay_type'],
                'pay_cycle'=>$share[$commission_id]['pay_cycle'],
                'unit'=>$share[$commission_id]['unit'],
                'commission'=>serialize($array),
            );
            M('stu_apply_education')->add($edu);
            //加入附件
            if(!empty($file))
            {
                foreach($file as $key=>$val)
                {
                    $insert=array(
                        'stu_apply_id'=>$apply_id,
                        'stu_id'=>$val['stu_id'],
                        'file_id'=>$val['file_id'],
                        'file_name'=>$val['file_name'],
                        'file_path'=>$val['file_path'],
                    );
                    M('stu_apply_file')->add($insert);
                }
            }
        }
        return $apply_id;
    }
    
    //添加申请附件表
    public function add_apply_file($file=array(),$stu_id,$stu_apply_id)
    {
        if(!empty($file))
        {
            foreach($file as $key=>$val)
            { 
                //判断是否重名
                $info = M('stu_apply_file')
                        ->where(array('file_name'=>$val['file_name'],
                                         'stu_id'=>$stu_id,
                                      'stu_apply_id'=>$stu_apply_id,                   
                        ))->find();
                if($info)
                {
                    M('stu_apply_file')->where('id='.$info['id'])->setField(
                        array('file_name'=>$val['file_name'],'file_path'=>$val['file_path'])
                    );
                }
                else 
                {
                    $insert=array(
                        'stu_apply_id'=>$stu_apply_id,
                        'stu_id'=>$stu_id,
                        'file_name'=>$val['file_name'],
                        'file_path'=>$val['file_path'],
                    );
                    
                    M('stu_apply_file')->add($insert);
                }
            }     
            return true;
        }
    }
    
    /**
     *  统计等待处理推送信息 
     */
    public function get_receive_num($member_id)
    {
       $num =0;
       $condition = array('member_id'=>$member_id,'status'=>0);
       $num= M('stu_receive')->where($condition)->count();
       return $num; 
    }
    
    /**
     *  添加一条推送信息 
     */
    public function add_stu_receive($receive)
    {
        $receive_id =0;
        $receive_id = M('stu_receive')->add($receive);
        return $receive_id;
    }
    
    /**
     *  获取一条输送信息 
     */
    public function get_receive_info($receive_id)
    {
        $info  = array();
        $info = M('stu_receive')->where('receive_id='.$receive_id)->find();
        if(!empty($info))
        {
            $stu_name=getField_value('stu_info','stu_name',array('stu_id'=>$info['stu_id']));
            $info['stu_name'] = str_replace(',', '', $stu_name);
            $info['from_member_name'] = getField_value('member','username',array('member_id'=>$info['from_member_id']));
        }
        return $info;
    }
    
    /**
     *  获取推送列表 
     *  要接收的用户ID
     */
    public function get_receive_list($member_id,$page,$pagesize)
    {
        $receive = array();
        $receive= M('stu_receive')->where('member_id='.$member_id)
                               ->order('status asc')->page($page.','.$pagesize)
                               ->select();
        if(!empty($receive))
        {
            foreach ($receive as $key=>$val)
            {
                $stu_name = M('stu_info')->where('stu_id='.$val['stu_id'])->getField('stu_name');
                $receive[$key]['stu_name'] = str_replace(',', '', $stu_name);  
                $receive[$key]['from_member_username'] = M('member')->where('member_id='.$val['from_member_id'])->getField('username');     
            }
        }
        return $receive;
    }
    
    /**
     *  获取一条申请信息 
     */
    public function get_apply_info($stu_apply_id)
    {
        $_info = array();
        $_info = M('stu_apply')->where('stu_apply_id='.$stu_apply_id)->find();
        $_info['stu_info'] = D('Stu')->get_StuInfo($_info['member_stu_id']);
        $_info['college_name']=M('college')->where('college_id='.$_info['college_id'])->getField('ename');
        $map = array('stu_apply_id'=>$_info['stu_apply_id'],'stu_id'=>$_info['stu_info']['id']);
        $_info['file'] = M('stu_apply_file')->field('id,file_name,file_path')->where($map)->select();
        
        if($_info['file'])
        {
            $url = "http://".$_SERVER['HTTP_HOST'].'/Uploads/';
            foreach($_info['file'] as $key=>$val)
            {
                $_info['file'][$key]['file_url'] = $url.$val['file_path'];
            }
        }
        
        $_info['start_time1'] = date('Y/m/d',$_info['start_time']);
        $_info['status_name'] = $this->get_status_msg($_info['status']);
        $_info['intermediary_name']= M('member')->where('member_id='.$_info['member_id'])->getField('username');
        if($_info['receive_member']!=0)
        {
            $_info['receive_member_name'] =M('member')->where('member_id='.$_info['receive_member'])->getField('username');
        }
        return $_info;
    }
    
    //获取该申请的输送人，接收人ID
    public function get_operator($apply_id)
    {
        $operator = array();
        if($apply_id)
        {
            $info = $this->get_apply_info($apply_id);  
            $operator = array($info['member_id'],$info['receive_member']);
        }
        return $operator;
    }
    
    public function get_apply_file($stu_apply_id)
    {
        return M('stu_apply_file')->where('stu_apply_id='.$stu_apply_id)->select();
    }
    
    public function get_status_msg($status)
    {
        $msg='';
        $a = C('APPLY_STATUS_OTHERS');
        if($status==C('Apply_START'))
        {
            $msg= '提出申请';
        }
        elseif($status==C('IS_EMAIL'))
        {
            $msg='院校审核中';
        }
        elseif($status==$a['is_Receive'])
        {
            $msg='同意接收';
        }
        elseif($status==$a['no_Receive'])
        {
            $msg='拒绝接收';
        }
        elseif($status==$a['is_msm'])
        {
            $msg='等待接收';
        }
        elseif($status==C('IS_Conditions_Admission'))
        {
            $msg='有条件录取';
        }
        elseif($status==C('IS_NO_Conditions_Admission'))
        {
            $msg='无条件录取';
        }
        elseif ($status==C('OFFER_UPDATE'))
        {
            $msg = 'Offer更新';
        }
        elseif($status==C('COLLEGE_Refuse'))
        {
            $msg='申请失败'; 
        }
        elseif($status==C('APPLY_Cancel'))
        {
            $msg='取消申请'; 
        }
        elseif($status==C('VISA_SUCCESS'))
        {
            $msg='签证成功';
        }
        elseif($status==C('VISA_FAILURE'))
        {
            $msg='签证失败';
        }
        elseif($status==C('END1'))
        {
            $msg='终止（选择其他院校 ）';
        }
        elseif($status==C('END2'))
        {
            $msg='终止（行程中止 ）';
        }
        elseif($status==C('END3'))
        {
            $msg='终止（其他 ）';
        } 
        return $msg;
    }
    
    /**
     * 获取该学生的申请信息 
     */
    public function get_apply_list($stu_id,$member_id,$type)
    {
        $list = array(); 
        if($type==1)
        {
            $condition=array('stu_id'=>$stu_id,'member_id'=>$member_id);
        }
        else 
        {
            $condition=array('stu_id'=>$stu_id,'receive_member'=>$member_id,
                'status'=>array('NEQ',8));
        }
        
        $list = M('stu_apply')->where($condition)->select();
        
        if(!empty($list))
        {
            foreach ($list as $key=>$val)
            {
                $list[$key]['college_name'] = M('college')->where('college_id='.$val['college_id'])->getField('ename');
                //当推送给他人中介时
                if($val['intermediary']!=0)
                {
                    $list[$key]['intermediary_name'] = M('member')->where('member_id='.$val['intermediary'])->getField('username');
                }
                else //报考自己的院校 
                {
                    $list[$key]['intermediary_name'] = '';
                }
                $list[$key]['status_name'] = $this->get_status_msg($val['status']);
            }
        }
        
        return $list;
    }
    
    //更新学生申请次数
    public function update_apply_success_count($stu_id)
    {
        if($stu_id)
        {
            $count=M('stu_apply_count')->where('stu_id='.$stu_id)->count();
            if($count)
            {
                M('stu_apply_count')->where('stu_id='.$stu_id)->setInc('times',1);
            }
            else 
            {
                M('stu_apply_count')->add(array(
                    'stu_id'=>$stu_id,
                    'times'=>1,
                ));
            }
        }
        
        return true;
    }
     
}

?>