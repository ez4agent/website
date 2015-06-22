<?php

//申请院校
namespace Home\Controller;
use Common\Controller\FrontbaseController;
use Home\Model\ApplyModel;

class ApplyController extends FrontbaseController
{
    var $member_id;
    var $apply_mod;
    var $log_mod;
    var $status;
    var $status_other;
    
    public function __construct()
    {
        parent::__construct();
        //会员ID
        $this->member_id =$this->auth()->member_id;
        $this->apply_mod=D('Apply');
        $this->log_mod=D('Log');
        $this->status = C('APPLY_STATUS_OWN');
        $this->status_other = C('APPLY_STATUS_OTHERS');
    }


    public function listing(){

        //Country	Qualification
        //New Zealand (8 universities)
        $new_zealand = array(
            'Auckland University of Technology'	=> '专科；本科；研究生',
            'Lincoln University'	=> '专科；本科；研究生',
            'Massey University'	=> '专科；本科；研究生',
            'The University of Auckland'	=> '专科；本科；研究生',
            'The University of Waikato'	=> '专科；本科；研究生',
            'University of Canterbury'	=> '专科；本科；研究生',
            'University of Otago'	=> '专科；本科；研究生',
            'Victoria University of Wellington'	=> '专科；本科；研究生'
        );

        //Australia (39 universities)
        $australia = array(
            'Australian Catholic University'	=> '专科；本科；研究生',
            'Bond University'	=> '专科；本科；研究生',
            'Central Queensland University'	=> '专科；本科；研究生',
            'Charles Darwin University'	=> '专科；本科；研究生',
            'Charles Sturt University'	=> '专科；本科；研究生',
            'Curtin University of Technology'	=> '专科；本科；研究生',
            'Deakin University'	=> '专科；本科；研究生',
            'Edith Cowan University'	=> '专科；本科；研究生',
            'Flinders University'	=> '专科；本科；研究生',
            'Griffith University'	=> '专科；本科；研究生',
            'James Cook University'	=> '专科；本科；研究生',
            'La Trobe University'	=> '专科；本科；研究生',
            'Macquarie University'	=> '专科；本科；研究生',
            'Monash University'	=> '专科；本科；研究生',
            'Murdoch University'	=> '专科；本科；研究生',
            'Queensland University of Technology'	=> '专科；本科；研究生',
            'RMIT University'	=> '专科；本科；研究生',
            'Southern Cross University'	=> '专科；本科；研究生',
            'Swinburne University of Technology'	=> '专科；本科；研究生',
            'The Australian National University'	=> '专科；本科；研究生',
            'The University of Adelaide'	=> '专科；本科；研究生',
            'The University of Melbourne'	=> '专科；本科；研究生',
            'The University of New England'	=> '专科；本科；研究生',
            'The University of New South Wales'	=> '专科；本科；研究生',
            'The University of Newcastle'	=> '专科；本科；研究生',
            'The University of Notre Dame Australia'	=> '专科；本科；研究生',
            'The University of Queensland'	=> '专科；本科；研究生',
            'The University of Sydney'	=> '专科；本科；研究生',
            'The University of Western Australia'	=> '专科；本科；研究生',
            'University of Ballarat'	=> '专科；本科；研究生',
            'University of Canberra'	=> '专科；本科；研究生',
            'University of South Australia'	=> '专科；本科；研究生',
            'University of Southern Queensland'	=> '专科；本科；研究生',
            'University of Tasmania'	=> '专科；本科；研究生',
            'University of Technology Sydney'	=> '专科；本科；研究生',
            'University of the Sunshine Coast'	=> '专科；本科；研究生',
            'University of Western Sydney'	=> '专科；本科；研究生',
            'University of Wollongong'	=> '专科；本科；研究生',
            'Victoria University'	=> '专科；本科；研究生'
        );

        $this->assign('new_zealand',$new_zealand);
        $this->assign('australia',$australia);
        $this->display();
    }

    //搜索院校
    public function search_college()
    {
        $keyword = I('post.keywords','','trim');
        $keywords = iconv("utf-8","gb2312//IGNORE",$keyword);
        $map['cname'] =array('LIKE',"%".$keyword."%");
        $map['ename'] =array('LIKE',"%".$keyword."%");
        $map['_logic'] = 'or';
        $college = M('college')->field('cname,ename')->where($map)->select();
        
        if(count($college)==0){
            echo 'no';
        }else{
            $result="[";
            foreach($college as $key=>$val)
            {
                $result.="{'keywords':'".$val['cname']."'},";
            }
            $result.="]";
        }
        echo $result;
    }
    
    //根据获取院校ID
    public function get_college_id()
    {
        if(IS_AJAX)
        { 
            $suggest_input = I('post.suggest_input','','trim');
            $where['cname'] = $suggest_input;
            $where['ename'] = $suggest_input;
            $where['_logic'] = 'or';
            
            $college_id = M('college')->where($where)->getField('college_id');
            //判断是否是自己的合作院校
            $num = $this->apply_mod->check_Cooperation($this->member_id,$college_id);
            if($num==1)
            {
               echo $this->ajaxReturn(array('status'=>'no','msg'=>'您暂时还不能投自己的合作院校！'));
               exit;
            }
            echo $this->ajaxReturn(array('status'=>'yes',college_url=>U('Home/School/college_view?id='.$college_id)));
            exit;
        }
    }
    
    //申请页面
    public function index()
    {
        $college_id = I('get.college_id',0,'intval');//院校ID
        $commission_id = I('get.commission_id',0,'intval'); //佣金
        $stu = I('get.stu',0,'intval');
        //获取院校信息
        $college_info =D('School')->get_college_info($college_id);
        //获取佣金记录
        $commission_info=D('Partner')->get_info_commission_id($college_id,$commission_id);
        //学生附件
        if($stu)
        {
            $stu_info = D('Stu')->get_StuInfo($stu);
            $stu_file = D('Stu')->get_stu_file($stu);

            $this->assign('stu_info',$stu_info);
            $this->assign('file',$stu_file);
        }
        //学生姓名
        $stu_option = D('Stu')->select_stu($stu,$this->member_id,1);
        //判断是不是自己的合作院校
        $Cooperation = $this->apply_mod->check_Cooperation($this->member_id,$college_id);
        
        $this->assign('college_info',$college_info);
        $this->assign('commission_info',$commission_info);
        $this->assign('stu_option',$stu_option);
        $this->assign('partner',$this->get_partner_info($this->member_id));
        $this->assign('view',$this->get_view_college($this->member_id));
        $this->assign('apply',$this->get_apply_college($this->member_id));
        $this->assign('Cooperation',$Cooperation);
        $this->assign('stu',$stu);
        $this->assign('country',country());//国家配置
        $this->display();   
    }
    
    //参看申请记录
    public function view()
    {
        $apply_id = I('get.apply_id','0','intval');
        $info = $this->apply_mod->get_apply_info($apply_id);
        //佣金信息
        $commission= M('stu_apply_education')->where('apply_id='.$apply_id)->find();
        $array = unserialize($commission['commission']);
        $share='';
        foreach($array as $key =>$val)
        {
            if($val['sharing_ratio']!=0)
            {
                $share.="(".$val['times'].")".$val['sharing_ratio']."%  ";
            }
        }
        $commission['share_value'] = $share;
        $info['commission']= $commission;
        if(!$info)
        {
            $this->error('改申请记录不存在！');
            exit();
        }

        $this->assign('apply_info',$info);


        $contact = D('Member')->get_Member_Info($this->member_id);
        $this->assign('contact',$contact);


        if($info['status'] == ApplyModel::VISA_WAIT && $info['receive_member'] != $this->member_id){
            $college_info = M('college')->where(array('college_id' => $info['college_id']))->find();

            $visa_info = M('visa_service')->where(array('country_id' => $college_info['country_id'],'member_id'=>$info['receive_member']))->find();
            $this->assign('visa_info',$visa_info);
        }

        $this->assign('log',$this->log_mod->get_log($apply_id));
        $this->assign('session',$this->member_id);
        $this->assign('stu_id3',getField_value('stu_apply', 'member_stu_id',
            array('stu_apply_id'=>$apply_id
                                )));
        $this->display();
    }

    //验证申请参数获得跳转链接
    public function get_header_apply_url()
    {
        if(IS_AJAX)
        {
            $college_id = I('post.college_id',0,'intval');
            $commission_id = I('post.commission_id',0,'intval');
            $stu = I('post.stu','','intval');
            
            //首先判断是否是自己的合作院校
            $Cooperation = $this->apply_mod->check_Cooperation($this->member_id,$college_id);
            if($Cooperation==1) //是自己的合作院校
            {
               echo $this->ajaxReturn(array('status'=>'no','msg'=>'您暂时不能向自己的合作院校提出申请！'));
               exit();
            }
            else //不是自己的合作院校
            {
                if(!$commission_id ||$college_id<=0)
                {
                   echo $this->ajaxReturn(array('status'=>'no','msg'=>'请选择中介！'));
                   exit();
                }
                $url =U('Home/Apply/index',array('college_id'=>$college_id,
                      'commission_id'=>$commission_id,'stu'=>$stu));
                echo $this->ajaxReturn(array('status'=>'yes','url'=>$url));
                exit();
            }  
        }
    }

    //申请院校操作
    public function apply_act()
    {
        if(IS_AJAX)
        {
            $stu_id = I('post.stu_id',0,'intval'); //学生ID
            $stu_id1= M('stu')->where('id='.$stu_id)->getField('stu_id');
            $college_id = I('post.college_id',0,'intval'); //院校ID
            $commission_id = I('post.commission_id',0,'intval');//佣金ID
            $profession = I('post.profession','','trim'); //专业
            $start_time = I('post.start_time','','trim'); //入学日期
            $content = I('post.content','','trim');//留言（可以为空）
            $items = I('post.items','','trim'); //文档字符串，逗号隔开
            
            //验证一个学生同一所院校同一个学历重复申请
            if(!D('Apply')->check_repeat1($stu_id,$college_id,$commission_id))
            {
                echo $this->ajaxReturn(array('status'=>'no','msg'=>'很抱歉,该学生重复申请该学历！'));
                exit();
            }
            
            //获取学历信息
            $info = D('Partner')->get_info_commission_id($college_id,$commission_id);
            $Cooperation =$this->apply_mod->check_Cooperation($this->member_id,$college_id);
            
            //验证申请成功次数小于5次, 大学数量小于等于2个（专科，本科，研究生，博士，MBA）预留
            $msg = D('Apply')->check_edu($info['education'],$stu_id1);
            if($msg)
            {
                echo $this->ajaxReturn(array('status'=>'no','msg'=>$msg));
                exit();
            }
            
            $insert_apply = array(
                'member_stu_id'=> $stu_id,
                'stu_id'=>$stu_id1,
                'member_id'=>$this->member_id, //发申请一方
                'college_id'=>$college_id,
                'apply_name'=>$info['education'],
                'profession'=>$profession,
                'start_time'=>strtotime($start_time),
                'intermediary'=>$info['member_id'],
                'status'=> ApplyModel::APPLY_START,
                'content'=>$content,
                'apply_type'=>$Cooperation?1:2,
                'receive_member'=>$Cooperation?0:$info['member_id'], //接收申请一方
                'is_success'=>0,
                'add_time'=>time(),
            );
            
            //附件
            $file_array = explode(',',$items);
            if(!empty($file_array))
            {
                $file=array();
                foreach($file_array as $key=>$val)
                {
                    $array = M('stu_file')->where(array('stu_id'=>$stu_id,'id'=>$val))->find();
                    $file[$key] = array( 
                        'stu_id'=>$array['stu_id'],
                        "file_id" => $array['file_id'],
                        'file_name'=>$array['file_name'],
                        'file_path'=>$array['file_path'],
                    );
                }
            }
            //提交申请操作
            $apply_id = $this->apply_mod->apply_action($insert_apply,$commission_id,$info['apply_id'],$file);

            if($apply_id)
            { 
                $college_name =getField_value('college','ename',array('college_id'=>$college_id)); 
                if(!$Cooperation) //非自己的合作院校
                {
                   $receive = array(
                        'from_member_id'=>$this->member_id, //推送人
                        'member_id'=>$info['member_id'], //接受人
                        'stu_id'=>$stu_id1,
                        'apply_id'=>$apply_id,
                        'college_id'=>$college_id,
                        'college_name'=>$college_name,
                        'add_time'=>time(),
                    );
                   $this->apply_mod->add_stu_receive($receive); //添加学生推送信息表   
                }
                
                $log = array(
                       'operate_user_id'=>$this->member_id,
                       'update_status'=>$Cooperation?C('Apply_START'):$this->status_other['is_msm'],
                       'operate_content'=>$content,
                );
                
                if(!M('college_apply_view')->where(array('member_id'=>$this->member_id,'college_id'=>$college_id))->count())
                {
                    M('college_apply_view')->add(array('member_id'=>$this->member_id,'college_id'=>$college_id));
                }


                if(!empty($file)){
                    $log['file'] = $file;
                }

                $this->log_mod->add_log($apply_id,$log);
                
                $jump_url = U('Home/Student/index?stu='.$stu_id);
                echo $this->ajaxReturn(array('status'=>'yes','msg'=>'申请成功！','url'=>$jump_url));
                exit;
            }
            else 
            {
                echo $this->ajaxReturn(array('status'=>'no','msg'=>'申请失败,请重新申请!'));
                exit();
            }
        }
    }

    //提交申请学校审核材料
    public function submit_school_apply(){
        if(IS_AJAX) {
            $waybill_company = I('post.waybill_company', '');
            $waybill_no = I('post.waybill_no', '');
            $stu_apply_id = I('post.stu_apply_id', '');

            if(!$waybill_company){
                echo $this->ajaxReturn(array('status'=>'no','msg'=>'请填写快递公司'));
                exit();
            }

            if(!$waybill_no){
                echo $this->ajaxReturn(array('status'=>'no','msg'=>'请填写快递单号'));
                exit();
            }

            $res = $this->check_apply($stu_apply_id,$this->member_id);
            if($res){
                echo $this->ajaxReturn($res);
                exit;
            }

            //更改状态
            M('stu_apply')->where('stu_apply_id='.$stu_apply_id)->setField(
                array('status'=>ApplyModel::APPLY_CONFIRM)
            );

            //更新日志
            $log = array(
                'apply_id'=>$stu_apply_id,
                'operate_user_id'=>$this->member_id,
                'update_status'=>ApplyModel::APPLY_CONFIRM,
                'type'=>0,
                'title' => $this->apply_mod->get_status_msg(ApplyModel::APPLY_CONFIRM),
                'operate_content'=>$waybill_company ." : ".$waybill_no,
            );

            D('Log')->add_log($stu_apply_id,$log);

            echo $this->ajaxReturn(array("status"=>"yes",'msg'=>'提交成功！','url'=>U('Home/Apply/view',array('apply_id'=>$stu_apply_id))));
        }
    }

    //院校审核(需扩展 用邮件代替)
    public function examine_act()
    { 
        if(IS_AJAX)
        { 
            $stu_apply_id = I('post.stu_apply_id',0,'intval');
            $res = $this->check_apply($stu_apply_id);
            if($res){
                echo $this->ajaxReturn($res);
                exit;
            }
            
            //更改状态
            M('stu_apply')->where('stu_apply_id='.$stu_apply_id)->setField(
                array('status'=>C('IS_EMAIL'))
            );

            //写日志
            $info = $this->apply_mod->get_apply_info($stu_apply_id);
            $college_name = M('college')->where('college_id='.$info['college_id'])->getField('ename');
            $operate_content='';
            $log = array('operate_user_id'=>$this->member_id,'operate_content'=>$operate_content);
            $this->log_mod->add_log($stu_apply_id,$log);

            echo $this->ajaxReturn(array('status'=>'yes'));
            exit;
        }
    }
    
    //申请结果
    public function apply_result()
    {
        if(IS_AJAX)
        {
            //验证该申请的操作权限
            $data = $_POST;
            $res = $this->check_apply($data['stu_apply_id'],$this->member_id);
            if($res){ 
                echo $this->ajaxReturn($res);
                exit;
            } 
            //更改状态
            if($data['status'] > 0)
            {
                $update = array(
                  'status'=> ApplyModel::VISA_WAIT,
                  'is_success' =>1,
                  'is_email'=>1 
                );
                //更新成功申请次数
                $stu_id = getField_value('stu_apply','stu_id',array('stu_apply_id'=>$data['stu_apply_id']));
                $this->apply_mod->update_apply_success_count(intval($stu_id));
            }
            else
            {
                $update = array(
                    'status'=>$data['status'],
                    'is_email'=>1,
                );
            }
            M('stu_apply')->where('stu_apply_id='.$data['stu_apply_id'])->setField(
                $update
            );
            
            //更新日志
            $log = array(
                'apply_id'=>$data['stu_apply_id'],
                'operate_user_id'=>$this->member_id,
                'update_status'=>$data['status'],
                'type'=>0,
                'title'=>$this->apply_mod->get_status_msg(ApplyModel::VISA_WAIT),
                'operate_content'=>$data['content'], 
            );
            
            if(!empty($data['apply_results']))
            {
                foreach($data['apply_results'] as $key=>$val)
                { 
                    $array = explode('|', $val);
                    $log['file'][$key]['file_name'] =$array[0] ;
                    $log['file'][$key]['file_path'] =$array[1] ;
                }
            }
            D('Log')->add_log($data['stu_apply_id'],$log);
            //更新申请附件表
            $stu_id = getField_value('stu_apply','member_stu_id',array('stu_apply_id'=>$data['stu_apply_id']));
            $this->apply_mod->add_apply_file($log['file'],$stu_id,$data['stu_apply_id']);
            
            echo $this->ajaxReturn(array("status"=>"yes",'msg'=>'提交成功！'));
        }
    }
    
    //交流
    public function message()
    {
        if(IS_AJAX)
        {
            //验证该申请的操作权限
            $data = $_POST;

            $title =  isset($data['title']) ? trim($data['title']) : '';
            $content =  isset($data['content']) ? trim($data['content']) : '';
            if(!$title){
                echo $this->ajaxReturn(array('status'=>'no','msg'=>'请选择主题'));
                exit();
            }

            if(!$content){
                echo $this->ajaxReturn(array('status'=>'no','msg'=>'请填写内容'));
                exit();
            }

            $res = $this->check_apply($data['stu_apply_id'],$this->member_id);
            if($res){
                echo $this->ajaxReturn($res);
                exit;
            }
            
            //更新日志
            $log = array(
                'apply_id'=>$data['stu_apply_id'],
                'operate_user_id'=>$this->member_id,
                'update_status'=>$data['status'],
                'type'=>1,
                'title'=>$title,
                'operate_content'=>$content,
            );
            
            if(!empty($data['apply_results']))
            {
                foreach($data['apply_results'] as $key=>$val)
                {
                    $array = explode('|', $val);
                    $log['file'][$key]['file_name'] =$array[0] ;
                    $log['file'][$key]['file_path'] =$array[1] ;
                }
            }
            D('Log')->add_log($data['stu_apply_id'],$log);
            //更新申请附件表
            $stu_id = getField_value('stu_apply','member_stu_id',array('stu_apply_id'=>$data['stu_apply_id']));
            $this->apply_mod->add_apply_file($log['file'],$stu_id,$data['stu_apply_id']);
            
            echo $this->ajaxReturn(array("status"=>"yes",'msg'=>'提交成功！'));
        }
    }

    public function submit_visa_apply(){
        if(IS_AJAX) {
            $waybill_company = I('post.waybill_company', '');
            $waybill_no = I('post.waybill_no', '');
            $stu_apply_id = I('post.stu_apply_id', '');

            if(!$waybill_company){
                echo $this->ajaxReturn(array('status'=>'no','msg'=>'请填写快递公司'));
                exit();
            }

            if(!$waybill_no){
                echo $this->ajaxReturn(array('status'=>'no','msg'=>'请填写快递单号'));
                exit();
            }

            $res = $this->check_apply($stu_apply_id,$this->member_id);
            if($res){
                echo $this->ajaxReturn($res);
                exit;
            }
/*
            //更改状态
            M('stu_apply')->where('stu_apply_id='.$stu_apply_id)->setField(
                array('status'=>ApplyModel::VISA_PAY_WAIT)
            );

            //更新日志
            $log = array(
                'apply_id'=>$stu_apply_id,
                'operate_user_id'=>$this->member_id,
                'update_status'=>ApplyModel::VISA_PAY_WAIT,
                'type'=>0,
                'title' => $this->apply_mod->get_status_msg(ApplyModel::VISA_PAY_WAIT),
                'operate_content'=>$waybill_company ." : ".$waybill_no,
            );

            D('Log')->add_log($stu_apply_id,$log);
*/
            echo $this->ajaxReturn(array("status"=>"yes",'msg'=>'提交成功！','url'=>U('Home/Order/visa',array('apply_id'=>$stu_apply_id))));
        }
    }
    
    //签证结果
    public function visa()
    {
        if(IS_AJAX)
        {
            //验证该申请的操作权限
            $data = $_POST;
            $res = $this->check_apply($data['stu_apply_id'],$this->member_id);
            if($res){
                echo $this->ajaxReturn($res);
                exit;
            }
            //更改状态
            M('stu_apply')->where('stu_apply_id='.$data['stu_apply_id'])->setField(
            array('status'=>$data['visa_status'])
            );
            
            //更新日志
            $log = array(
                'apply_id'=>$data['stu_apply_id'],
                'operate_user_id'=>$this->member_id,
                'update_status'=>$data['visa_status'],
                'type'=>0,
                'title' => $this->apply_mod->get_status_msg($data['visa_status']),
                'operate_content'=>$data['content'],
            );
            if(!empty($data['apply_results']))
            {
                foreach($data['apply_results'] as $key=>$val)
                {
                    $array = explode('|', $val);
                    $log['file'][$key]['file_name'] =$array[0] ;
                    $log['file'][$key]['file_path'] =$array[1] ;
                }
            }
            D('Log')->add_log($data['stu_apply_id'],$log);
            //更新申请附件表
            $stu_id = getField_value('stu_apply','member_stu_id',array('stu_apply_id'=>$data['stu_apply_id']));
            $this->apply_mod->add_apply_file($log['file'],$stu_id,$data['stu_apply_id']);
            
            echo $this->ajaxReturn(array("status"=>"yes",'msg'=>'提交成功！'));
        }
    }
    
    //终止
    public function apply_end()
    {
        if(IS_AJAX)
        {
            //验证该申请的操作权限
            $data = $_POST;

            $end_status =  isset($data['end_status']) ? intval($data['end_status']) : 0;
            $content =  isset($data['content']) ? trim($data['content']) : '';
            if(!$end_status){
                echo $this->ajaxReturn(array('status'=>'no','msg'=>'请选择原因'));
                exit();
            }

            if(!$content){
                echo $this->ajaxReturn(array('status'=>'no','msg'=>'请填写内容'));
                exit();
            }


            $res = $this->check_apply($data['stu_apply_id'],$this->member_id);
            if($res){
                echo $this->ajaxReturn($res);
                exit;
            }
            //更改状态
            M('stu_apply')->where('stu_apply_id='.$data['stu_apply_id'])->setField(
            array('status'=>$data['end_status'],'is_stop'=>1)
            );
            
            //更新日志
            $log = array(
                'apply_id'=>$data['stu_apply_id'],
                'operate_user_id'=>$this->member_id,
                'update_status'=>$data['end_status'],
                'type'=>0,
                'title' => $this->apply_mod->get_status_msg($data['end_status']),
                'operate_content'=>$data['content'],
            );
            if(!empty($data['apply_results']))
            {
                foreach($data['apply_results'] as $key=>$val)
                {
                    $array = explode('|', $val);
                    $log['file'][$key]['file_name'] =$array[0] ;
                    $log['file'][$key]['file_path'] =$array[1] ;
                }
            }
            D('Log')->add_log($data['stu_apply_id'],$log);
            //更新申请附件表
            $stu_id = getField_value('stu_apply','member_stu_id',array('stu_apply_id'=>$data['stu_apply_id']));
            $this->apply_mod->add_apply_file($log['file'],$stu_id,$data['stu_apply_id']);
            
            echo $this->ajaxReturn(array("status"=>"yes",'msg'=>'提交成功！'));
        }
    }
    
    //验证该申请是否存在且操作人是否合法
    public function check_apply($apply_id,$operator)
    {
        $res = array();
        
        //验证是否是登陆状态下
        if(!$this->member_id)
        {
            $url=U('Home/Login/index');
            $res = array('status'=>'no','msg'=>'抱歉,您还没有登陆！','url'=>$url);
        }
        else 
        {
            $info = $this->apply_mod->get_apply_info($apply_id);
            if(!$info)
            {
                $url=U('Home/Student/index');
                $res = array('status'=>'no','msg'=>'该院校申请不存在！','url'=>$url);
            }
            else
            { 
               //判断操作权限
               $get_operator_array = $this->apply_mod->get_operator($apply_id);
               if(!in_array($operator,$get_operator_array))
               {
                   $url=U('Home/Student/index');
                   $res = array('status'=>'no','msg'=>'您没有该院校申请操作权限！','url'=>$url);
               }
            }
        }
        return $res;
    }
    
    //查看留言
    public function get_log_info()
    { 
        if(IS_AJAX)
        { 
            $log_id = I('post.log_id',0,'intval');
            if(!$log_id){
                echo $this->ajaxReturn(array('status'=>'no','msg'=>'留言不存在！'));
                exit();
            }
            $content = getField_value('stu_apply_operate_log','operate_content',array('log_id'=>$log_id));

            $files = M('stu_apply_file')->field('id,file_name,file_path')->where(array('stu_apply_id'=>$log_id))->select();
            if($files)
            {
                $url = "http://".$_SERVER['HTTP_HOST'].'/Uploads/';
                foreach($files as $key=>$val)
                {
                    $files[$key]['file_url'] = $url.$val['file_path'];
                }
            }

            echo $this->ajaxReturn(array('status'=>'yes','info1'=>$content,'files' => $files));
            exit;
        }
    }

    //验证该申请是否已经发送邮件
    public function check_is_email()
    {
        if(IS_AJAX)
        {
            $apply_id = I('post.stu_apply_id',0,'intval');
            $is_email = getField_value('stu_apply_id','is_email',array('stu_apply_id'=>$apply_id));
            if($is_email==0)
            {
                $this->ajaxReturn(array('status'=>'no'));
                exit;
            }
            else 
            {
                
                $this->ajaxReturn(array('status'=>'yes'));
                exit;
            }
        }
    }
    
    //读取合作院校信息列表
    public function get_partner_info($member_id)
    {
        $_partner = array();
        $_partner =D('Partner')->get_partner_listbymemberId($member_id);
        return $_partner;
    }
    
    //读取查看过的院校的名称
    public function get_view_college($member_id)
    {
        $_view= array();
        $_view = D('School')->get_view_college_list($member_id);
        return $_view;
    }
    
    //读取申请过的院校的名称
    public function get_apply_college($member_id)
    {
        $_apply= array();
        $_apply = D('School')->get_apply_college_list($member_id);
        return $_apply;
    }
}

?>