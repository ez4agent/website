<?php

/**
 *  CRM系统 --站内信 
 */
namespace Home\Controller;
use Common\Controller\FrontbaseController;

class LetterController extends FrontbaseController
{
    var $letter_mod;
    var $member_id;
    var $student_mod;
    
    public function __construct()
    {
        parent::__construct();
        //实例化站内信模型
        $this->letter_mod = D('Letter');
        $this->student_mod=D('Stu');
        $this->member_id=$this->auth()->member_id;
    }
    
    /**
     * 列表页面（收件箱、发件箱） 
     */
    public function index()
    { 
        $type = !empty($_GET['type'])?trim($_GET['type']):'inbox';
        if($type=='inbox') //收信箱
        {
            $where['sr.to_uid'] = $this->member_id;
            $where['sr.is_deleted']=0;
            $count = M('message_receiver')->alias('sr')->where($where)->count();   
        }
        elseif($type=='outbox') //发信箱
        {
            $where['from_uid'] = $this->member_id;
            $where['from_deleted'] = 0;
            $count = M('message_sender')->where($where)->count();      
        }

        $Page = new \Think\Page($count,20);// 实例化分页类 传入总记录数和每页显示的记录数(25)
        $list = $this->letter_mod->get_email_list($type,$where,$Page->firstRow,$Page->listRows);
        
        $show = $Page->show();// 分页显示输出
        $this->assign('type',$type);
        $this->assign('list',$list);
        $this->assign('page',$show);
        $this->display();
    }
    
    /**
     * 发信页面
     */
    public function sentLetter()
    {
        //如果有传stu_id过来
        $stu_id = I('get.stu_id',0,'intval');
        //member_id
        $member_id = I('get.member_id',0,'intval');
        $member_name="";
        if($member_id>0){
           $member_name =getField_value('member', 'username',array('member_id'=>$member_id));  
        }
        //常用联系人
        $this->assign('contact',$this->letter_mod->get_contact_list($this->member_id));
        $this->assign('type','send');
        $this->assign('member_name',$member_name);
        $this->display();
    }
    
    /**
     *  发站内信操作 
     */
    public function send_letter_act()
    {
        if(IS_AJAX)
        { 
            $data = $_POST;
            //判断收信人是否存在
            $msg = $this->check_to_member($data['to']);
            if($msg)
            { 
                $this->ajaxReturn(array('status'=>'no','msg'=>$msg));
                exit();
            }
            
            $type = isset($data['type'])?intval($data['type']):1;
            
            if($type ==1) //站内信
            {
                $from_member_name = getField_value('member','username',array('member_id'=>$this->member_id));
                $to_id = getField_value('member', 'member_id',array('username'=>$data['to']));
                
                $message_sender =array(
                    'mid'=>'',
                    'from_uid'=>$this->member_id,
                    'from_username'=>$from_member_name,
                    'title'=>trim($data['email_title']),
                    'content'=>trim($data['content']),
                    'repay_id'=>isset($data['repay_id'])?intval($data['repay_id']):0,
                    'status'=>isset($data['status'])?intval($data['status']):1,
                    'from_deleted'=>0,
                    'date'=>time(),
                );
                
                $mid = M('message_sender')->add($message_sender);
                if(!$mid)
                {
                    $this->ajaxReturn(array('status'=>'no','msg'=>'发送失败！'));
                    exit();
                }
                else 
                {
                    $message_receiver =array(
                        'mid' =>$mid,
                        'to_uid'=>$to_id,
                        'to_username'=>trim($data['to']),
                        'is_readed'=>0,
                        'is_deleted'=>0,
                    );
                    
                    M('message_receiver')->add($message_receiver);
                    
                    if(!empty($data['file_name]'])||!empty($data['file_path']))
                    {
                        $file = array();
                        foreach ($data['file_name'] as $key=>$val)
                        {
                            $file[$key]=array(
                                'msm_id'=>$mid,
                                'file_name'=>$val,
                                'file_path'=>$data['file_path'][$key],
                            );
                        }
                        M('email_file')->addAll($file);
                    }
                    
                    //添加常用联系人
                    $this->contact($to_id, $data['to'], $this->member_id,$from_member_name);
                    
                    $this->ajaxReturn(array('status'=>'yes','msg'=>'发送成功！','url'=>
                        U('Home/Letter/index')
                    ));
                    exit();
                }
            }
        }
    }
    
    public function sentEmail()
    {
        $apply_id = I('get.apply_id',0,'intval');
        //申请附件
        
        $file = M('stu_apply_file')->where('stu_apply_id='.$apply_id)->select();  
        $this->assign('file',$file); 
        $this->assign('apply_id',$apply_id);       
        $this->display();
    }
    
    //发邮件
    public function send_email_act()
    {
        if(IS_AJAX)
        {
            $data = $_POST;
            $file = array();
            if($data['id'])
            { 
                foreach($data['id'] as $key=>$val)
                {
                    $array = M('stu_apply_file')->where('id='.$val)->find();
                    $file[$key] = array(
                        'file_name'=>$array['file_name'],
                        'file_path'=>$array['file_path'],
                    );
                }
            }
            if(!send_email($data['to'], $data['email_title'], $data['content'],$file))
            { 
                $this->ajaxReturn(array('status'=>'no','msg'=>'发送失败！'));
                exit();
            }
            else 
            {
                $apply_id = I('get.apply_id','0','intval');
                
                //判断是否已提交申请
                $is_email = getField_value('stu_apply', 'is_email',array('stu_apply_id'=>$apply_id));
                if($is_email==0) //提交申请
                { 
                    $updata = array(
                        'status'=>C('IS_EMAIL'),
                        'is_email'=>1,
                    );
                    M('stu_apply')->where('stu_apply_id='.$apply_id)
                                  ->setField($updata);
                    //插入log日志
                    $log = array(
                       'operate_user_id'=>$this->member_id,
                       'update_status'=>C('IS_EMAIL'),
                       'operate_content'=>'',
                    );
                    
                    D('Log')->add_log($apply_id,$log);
                }
                elseif($is_email==1) //offer更新
                {
                    M('stu_apply')->where('stu_apply_id='.$apply_id)->setField('status',C('OFFER_UPDATE'));
                    //插入log日志
                    $log = array(
                       'operate_user_id'=>$this->member_id,
                       'update_status'=>C('OFFER_UPDATE'),
                       'operate_content'=>'',
                    );
                    
                    D('Log')->add_log($apply_id,$log);
                    
                } 
                
                $this->ajaxReturn(array('status'=>'yes','msg'=>'发送成功!',
                    'url'=>U('Home/Apply/view',array('apply_id'=>$apply_id))));
                exit();
            }
        }
    }
    
    //发件箱(查看)
    public function view()
    {
        $msm_id = I('get.email',0,'intval');
        $email_info = $this->letter_mod->get_email_info($msm_id);
        $this->assign('info',$email_info);
        $this->display();
    }
    
    //查看回复页面
    public function reply()
    {
        $msm_id = I('get.email',0,'intval');
        $email_info = $this->letter_mod->get_email_info($msm_id);
        if($email_info['is_readed'] ==0)
        {
            M('message_receiver')->where('mid='.$msm_id)->setField('is_readed',1);
        }
        //echo "<pre>";print_r($email_info);exit;
        $this->assign('info',$email_info);
        $this->display();
    }
    
    //批量删除
    public function Batch_del()
    {
        if(IS_AJAX)
        {
            $msm_id = I('post.msm_id','','trim');
            $type = I('post.type','','trim'); 
            if($msm_id)
            {
                $msm_array= explode(',', $msm_id);
                if($type=='inbox')
                {
                    foreach ($msm_array as $val)
                    {
                        M('message_receiver')->where('rid='.$val)->setField('is_deleted',1);    
                    }
                }
                elseif($type=="outbox")
                {
                    foreach ($msm_array as $val)
                    {
                       M('message_sender')->where('mid='.$val)->setField('from_deleted',1); 
                      
                    }
                }
                
                $this->ajaxReturn(array('status'=>'yes'));
                exit;
            }
            else 
            { 
                $this->ajaxReturn(array('status'=>'no','msg'=>'删除失败！'));
                exit;
            }
        }
    }
    
        
    /**
     *  验证收信人是否是 
     */
    public function check_to_member($to)
    {
        $msg ='';
        //获取收件人的ID
        $to_id = getField_value('member', 'member_id',array('username'=>$to));
        if(empty($to_id))
        { 
            $msg = '收信人不存在';
        }
        else if($to_id==$this->member_id)
        {
            $msg = "收信人不能是自己";    
        }
        return $msg;
    }
    
    //添加常用联系人
    public function contact($contact_id,$contact_name,$member_id,$from_member_name)
    {
        if($contact_id && $contact_name && $member_id)
        {
            //发信人记收新人
            $count = M('contact')->where(
                     array('contact_id'=>$contact_id,'member_id'=>$member_id))->count();
            if(!$count)
            {
                M('contact')->add(
                    array(
                    'contact_id'=>$contact_id,
                    'contact_name'=>$contact_name,
                    'member_id'=>$member_id,   
                ));
            }
            
            //收信人记发信人
            $count1 =M('contact')->where(
                array('contact_id'=>$member_id,'member_id'=>$contact_id))->count(); 
            if(!$count)
            {
                M('contact')->add(array(
                    'contact_id'=>$member_id,
                    'contact_name'=>$from_member_name,
                    'member_id'=>$contact_id,
                ));
            }
        }
        
        return true;
    }
    
    //删除附件
    public function del_file()
    {
        if(IS_AJAX)
        {
            $url = I('post.url','','trim');
            unlink('./Uploads'.$url);
            $this->ajaxReturn(array('status'=>'yes'));
        }
    }
    
    //邮件上传图片
    public function uplode_apply_file()
    {
        if(IS_AJAX)
        {
           $apply_id = I('post.apply_id',0,'intval'); 
           $title = I('post.title','','trim');
           $url = I('post.url','','trim');
           
           //获取stu_id
           $stu_id = getField_value('stu_apply','stu_id',array('stu_apply_id'=>$apply_id));
           $id = M('stu_apply_file')->add(
            array(
                'stu_apply_id'=>$apply_id,
                'stu_id'=>$stu_id,
                'file_name'=>$title,
                'file_path'=>$url,
            )    
           );
           if(!$id)
           {
              $this->ajaxReturn(array('status'=>'no','msg'=>'上传失败！')); 
           }
           $this->ajaxReturn(array('status'=>'yes'));
        }
    }
}

?>