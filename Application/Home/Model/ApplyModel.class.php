<?php
/**
 *  申请操作 
 */
namespace Home\Model;
use Think\Model;

class ApplyModel extends Model
{
    var $edu;

    //申请
    const APPLY_PAY_WAIT = 5; //申请未支付

    const APPLY_START = 10; //提出申请
    const APPLY_WAIT = 11;
    const APPLY_UPDATE_OFFER = 12; //Offer更新
    const APPLY_CONFIRM = 13;
    const APPLY_HAS_CONDITION = 14;
    const APPLY_FAILURE = 15; //申请失败
    const APPLY_NO_CONDITION = 16;

    //签证
    const VISA_WAIT = 20;
    const VISA_CONFIRM = 21;
    const VISA_PAY_WAIT = 22;

    //支付
    const PAY_WAIT = 30;
    const PAY_CONFIRM = 31;

    //完成
    const FINISH = 100;

    const APPLY_REJECT = -10; //拒绝接收


    const VISA_FAILURE = -20; //拒签
    const PAY_FAILURE = -30; //支付失败

    //终止
    const STOP_B_SCHOOL = -100;
    const STOP_B_TRAVEL = -101;
    const STOP_B_OTHER = -102;

    public function __construct()
    {
        parent::__construct();
        
        $this->edu = array(
            '专科','本科','研究生'
        );    
    }

    public function get_status_msg($status)
    {
        $msg='';

        if($status==self::APPLY_PAY_WAIT){
            $msg= '等待支付申请费';
        }elseif($status==self::APPLY_START){
            $msg= '等待接收';
        }
        elseif($status==self::APPLY_UPDATE_OFFER)
        {
            //$msg='Offer更新';
            $msg='院校申请拒绝';
        }elseif($status==self::APPLY_REJECT){
            $msg='院校申请拒绝';
        }
        elseif($status==self::APPLY_FAILURE)
        {
            $msg='申请失败';
        }
        elseif($status==self::APPLY_WAIT){
            $msg='等待材料审核';
        }
        elseif($status==self::APPLY_CONFIRM){
            $msg='等待院校申请结果';
        }
        elseif($status==self::APPLY_HAS_CONDITION)
        {
            $msg='有条件录取';
        }
        elseif($status==self::APPLY_NO_CONDITION)
        {
            $msg='无条件录取/等待签证结果';
        }
        elseif($status==self::VISA_WAIT)
        {
            $msg='等待签证原材料';
        }elseif($status==self::VISA_PAY_WAIT){
            $msg='等待支付委托费';
        }
        elseif($status==self::VISA_CONFIRM)
        {
            $msg='等待签证结果';
        }
        elseif($status==self::VISA_FAILURE){
            $msg='签证失败';
        }
        elseif($status==self::PAY_WAIT){
            $msg='等待佣金支付';
        }
        elseif($status==self::FINISH){
            $msg='完成';
        }
        elseif($status==self::STOP_B_SCHOOL)
        {
            $msg='申请终止';
        }
        elseif($status==self::STOP_B_TRAVEL)
        {
            $msg='申请终止';
        }
        elseif($status==self::STOP_B_OTHER)
        {
            $msg='申请终止';
        }
        return $msg;
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
            $count = getField_value('stu_apply_count', 'times',array('stu_id'=>$stu_id));
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

        $_info['receive_address'] = array();

        if($_info['post_address_id'] == "-1"){
            $receive_member_info = M('member_info')->where(array('member_id'=>$_info['receive_member']))->find();

            $_info['receive_address'] = array(
                'address' => $receive_member_info['address'],
                'contacter' => $receive_member_info['contact'],
                'phone' => $receive_member_info['telephone']
            );
        }else{
            $_info['receive_address'] = M('member_address')->where(array('address_id'=>$_info['post_address_id']))->find();
        }

        if($_info['needmore']){
            $needkind_arr = array(
              1 => '学历', 2 => '成绩单' , 3 => '语言成绩'
            );

            $needtype_arr = array(
                '1' => '原件', '2' => '公证件'
            );

            $needkind = $needkind_other = array();
            $needmore = json_decode($_info['needmore'],true);

            foreach($needmore['needkind'] as $v){
                $needkind[] = $needkind_arr[(int)$v];
            }

            if($needmore['needkind_other']){
                $needkind[] = $needmore['needkind_other'];
            }

            foreach($needmore['needtype'] as $v){
                $needtype[] = $needtype_arr[(int)$v];
            }

            if(is_array($needmore)){
                $_info['needmore'] = array(
                    'needkind' => join(' , ',$needkind),
                    'needtype' => join(' , ',$needtype),
                    //'address' => join(' , ',$_info['receive_address']),
                );
            }
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

                $list[$key]['paywait'] = $val['status'] == self::APPLY_PAY_WAIT ? 1:0;;

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

    public function delete_apply($stu_apply_id){

        if(M('stu_apply')->where('stu_apply_id='.$stu_apply_id)->delete()){
            //crm_stu_apply_count
            $files = M('stu_apply_file')->where('stu_apply_id='.$stu_apply_id)->select();
            foreach($files as $f){
                $file = __ROOT__. '/'.$f['file_path'];
                if(is_file($file)){
                    @unlink($file);
                }
            }

            M('stu_apply_file')->where('stu_apply_id='.$stu_apply_id)->delete();
            M('stu_apply_operate_log')->where('apply_id='.$stu_apply_id)->delete();
            M('stu_apply_uploadfile')->where('apply_id='.$stu_apply_id)->delete();

            return true;
        }

        return false;
    }
     
}

?>