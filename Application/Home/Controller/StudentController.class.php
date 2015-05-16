<?php
/**
 *  CRM系统--学生管理 
 */
namespace Home\Controller;
use Common\Controller\FrontbaseController;
use Common\Util\Pinyin;

class StudentController extends FrontbaseController
{
    var $student_mod;
    var $apply_mod;
    var $member_id;
    var $stu_last_id; //最后一次添加的ID
    var $receive_num;
    
    public function __construct()
    {
        parent::__construct();
        //实例化学生模型
        $this->student_mod = D('Stu');
        $this->apply_mod = D('Apply');
        //会员ID
        $this->member_id=$this->auth()->member_id;
        $this->stu_last_id=$this->student_mod->get_last_stuId($this->member_id);
        //推送学员
        $this->receive_num=$this->apply_mod->get_receive_num($this->member_id); 
        $this->status = C('APPLY_STATUS_OWN');
        $this->status_other = C('APPLY_STATUS_OTHERS');
    }
    /**
     *  学生管理 
     */
    public function index()
    {
       $stu_id = I('get.stu',0,'intval'); 
       $_info = array(); 
       if($stu_id && $stu_id>0)
       {
          //学生基本信息
          $_info=$this->student_mod->get_StuInfo($stu_id);
          $_info['file'] = $this->student_mod->get_stu_file($stu_id);
       }
       elseif(!empty($this->stu_last_id)) 
       {
          $this->redirect('Student/index',array('stu'=>$this->stu_last_id['id']));
       }
       else 
       {
           $stu_id=0;   
       }
       
       //自生学员列表
       $condition = array('a.member_id'=>$this->member_id);
       $page = isset($_GET['P'])?intval($_GET['P']):1;
       $this->assign('stu_list',$this->student_mod->get_stu_list($condition,$page,25));
       $this->assign('info',$_info);
       $this->assign('country',country());//国家配置
       $this->assign('education',C('Education_TYPE')); //学历配置
       $this->assign('apply_list',$this->apply_mod->get_apply_list($_info['stu_id'],$this->member_id, $_info['type']));
       $this->assign('stu_id',$stu_id);
       $this->assign('receive_num',$this->receive_num);
       $this->display(); 
    }
    
    //查看学生推送列表
    public function receive_list()
    {
        $page = isset($_GET['p'])?intval($_GET['P']):1;
        $pagesize =15;
        $count = M('stu_receive')->where('member_id='.$this->member_id)->count();
        $list = D('Apply')->get_receive_list($this->member_id,$page,$pagesize);
        $Page= new \Think\Page($count,$pagesize);

        $this->assign('list',$list);
        $this->assign('page',$Page->show());
        $this->display();
    }
    
    //接收他人推送过来的学生
    public function get_receive_info()
    {
        $receive_id = I('post.receive_id',0,'intval');    
        $receive_info = $this->apply_mod->get_receive_info($receive_id); 
       
        if(!empty($receive_info))
        { 
            if($receive_info['status']==0)
            {
                //获取一条申请时间
                $apply_info = $this->apply_mod->get_apply_info($receive_info['apply_id']);
                //返回信息
                $return_data = array(
                    'receive_id'=>$receive_info['receive_id'],
                    'stu_name'=>$apply_info['stu_info']['stu_name'],
                    'college_name'=>$apply_info['college_name'],
                    'apply_name'=>$apply_info['apply_name'],
                    'profession'=>$apply_info['profession'],
                    'start_time'=>date('Y/m/d',$apply_info['start_time']),
                    'intermediary_name'=>$apply_info['intermediary_name'],
                    'file'=>$apply_info['file'],
                );
                echo $this->ajaxReturn(array('status'=>'yes','info'=>$return_data));
                exit();
            }
            else
            { 
                echo $this->ajaxReturn(array('status'=>'no','msg'=>'你已经操作过,不能重复操作！'));
                exit();
            }
        }
        else 
        {
            echo $this->ajaxReturn(array('status'=>'no','输送信息不存在！'));
            exit();
        }   
        
    }

    //学生基本信息(添加，修改)
    public function save_Stuinfo()
    {
        if(IS_AJAX)
        {
            $data= $_POST;
            if($data['stu_id'])
            {
                $data['stu_name']=$data['xin'].','.$data['mingzi'];
                $data['pinyin']=$data['xin_pinyin'].','.$data['mingzi_pinyin'];
                unset($data['xin']);
                unset($data['mingzi']);
                unset($data['xin_pinyin']);
                unset($data['mingzi_pinyin']);
                unset($data['id']);
                $res=$this->student_mod->editData('edit',$data);
                if($res['msg'])
                {
                    $this->ajaxReturn(array('status'=>'no','msg'=>$res['msg']));
                    exit();
                }
                $this->ajaxReturn(array('status'=>'yes','msg'=>'修改成功！'));
                exit();
            }
            else
            {
                //添加学生操作
                $data['stu_name']=$data['xin'].','.$data['mingzi'];
                $data['pinyin']=$data['xin_pinyin'].','.$data['mingzi_pinyin'];
                $data['member_id'] = $this->member_id;
                unset($data['xin']);
                unset($data['mingzi']);
                unset($data['xin_pinyin']);
                unset($data['mingzi_pinyin']);               
                //添加操作
                $res=$this->student_mod->editData('add',$data);
                if($res['data'])
                {
                    $stu_insert = array(
                        'member_id'=>$this->member_id,
                        'stu_id'=>$res['data'],
                        'type'=>1,
                        'transportation'=>0,
                        'add_time'=>time(),
                    );
                    $stu_id=M('stu')->add($stu_insert);
                    $this->ajaxReturn(array('status'=>'yes','msg'=>'添加成功！'));
                    exit();
                }
                else 
                {
                    $this->ajaxReturn(array('status'=>'no','msg'=>$res['msg']));
                    exit();
                }
            }
            
        }
    } 
    
    //ajax获得学生基本信息
    public function get_infoBystu_id()
    {
        if(IS_AJAX)
        {
            $stu_id = I('post.stu_id',0,'intval');
            $info=array();
            if($stu_id>0)
            {
                $info = M('stu')->alias('s')->join('__STU_INFO__ b ON b.stu_id= s.stu_id')
                         ->where('s.id='.$stu_id)
                         ->find();
                $name = explode(',', $info['stu_name']);
                $pinyin = explode(',', $info['pinyin']);
                $info['xing'] =$name[0];
                $info['mingzi'] = $name[1];
                $info['xing_pinyin'] = $pinyin[0];
                $info['minzi_pinyin'] = $pinyin[1];
            }
             
            $this->ajaxReturn(array('status'=>'yes','info'=>$info));
            exit();
        }
    }
    
    //中文英文转化
    public function character_change()
    {
        if(IS_AJAX)
        {
            $xin = I('post.xin','','trim');
            $pinyin = new Pinyin();
            $output = $pinyin->output($xin);
            $this->ajaxReturn(array('status'=>'ok','info'=>$output));
        }
    }
    
    public function upload_StuFile_info()
    {
        $type = I('get.type','','trim');
        $path = 'Stu';
        $result = D('Upload')->file_upload($type,$path);
        if($result['error']==1)
        {
             $this->ajaxReturn(array('error'=>$result['error'],'message'=>$result['message']));
             exit();
        }
        else 
        {
             $this->ajaxReturn(array('error'=>$result['error'],'url'=>$result['info']['imgFile']['savepath'].$result['info']['imgFile']['savename']));
             exit();    
        }  
    }
    
    /**
     *  获取学生附件 
     */
    public function get_file()
    { 
        if(IS_AJAX)
        { 
            $stu_id = I('post.stu_id',0,'intval');
            //拿到stu_id;
            $_info=$this->student_mod->get_StuInfo($stu_id);
            if(!$_info)
            {
                $this->ajaxReturn(array('status'=>'no','msg'=>'该学生不存在！'));
                exit();
            }
            else 
            {
                $file=array();
                $file = M('stu_file')->where('stu_id='.$_info['id'])->field('id,file_name,file_path')->select();
                $this->ajaxReturn(array('status'=>'yes','info'=>$file));
                exit();
            }
        }
    }
    
    /**
     *  ajax学生附件提交
     */
    public function ajax_stufile_submit()
    {
        if(IS_AJAX)
        {
            $fileurl = I('post.fileurl','','trim');
            $title = I('post.title','','trim');
            $stu_id = I('post.stu_id',0,'intval');
            
            $_info = $this->student_mod->get_StuInfo($stu_id);

            if(!$stu_id || !$_info)
            {
                $this->ajaxReturn(array('status'=>'no','msg'=>'该学生信息不存在,请先填写基本信息！'));
                exit();
            }

            //判断文件名是否存在
            $info = M('stu_file')->where(array('file_name'=>$title,'stu_id'=>$stu_id))->find();
            if($info)
            {
               $map = array('id'=>$info['id'],'stu_id'=>$stu_id);
               if(M('stu_file')->where($map)->save(array('file_path'=>$fileurl))===false)
               {
                   $this->ajaxReturn(array('status'=>'no','msg'=>'上传失败！'));
               }
               $this->ajaxReturn(array('status'=>'yes'));
               exit();
            }
            else 
            {
                $id = M('stu_file')->add(array(
                    'stu_id'   =>$stu_id,
                    'file_name'=>$title,
                    'file_path'=>$fileurl,
                ));
                if($id){
                    $this->ajaxReturn(array('status'=>'yes'));
                    exit();
                }
            }
        }
    }
    
    /**
     *  AJAX提交学生附件（用户申请院校） 
     */
    public function ajax_stufile_submit1()
    {
        if(IS_AJAX)
        {
            $fileurl = I('post.fileurl','','trim');
            $title = I('post.title','','trim');
            $stu_id = I('post.stu_id',0,'intval');
            
            $_info = $this->student_mod->get_StuInfo($stu_id);
            
            if(!$stu_id || !$_info)
            {
                $this->ajaxReturn(array('status'=>'no','msg'=>'该学生信息不存在,请先填写基本信息！'));
                exit();
            }
            
            //判断文件名是否存在
            $info = M('stu_file')->where(array('file_name'=>$title,'stu_id'=>$stu_id))->find();
            if($info)
            {
                $map = array('id'=>$info['id'],'stu_id'=>$stu_id);
                if(M('stu_file')->where($map)->save(array('file_path'=>$fileurl))===false)
                {
                    $this->ajaxReturn(array('status'=>'no','msg'=>'上传失败！'));
                    exit;
                }
                else 
                {
                    $this->ajaxReturn(array('status'=>'yes1'));
                    exit();
                }
            }
            else
            {
                $id = M('stu_file')->add(array(
                    'stu_id'   =>$stu_id,
                    'file_name'=>$title,
                    'file_path'=>$fileurl,
                ));
                if(!$id){
                    $this->ajaxReturn(array('status'=>'no','msg'=>'上传失败！'));
                    exit;
                }
                $file = $this->student_mod->get_stu_file($stu_id);
                $num = count($file);
                if($num==1)
                {
                    $this->ajaxReturn(array('status'=>'yes2'));
                    exit();
                }
                else 
                { 
                    $this->ajaxReturn(array('status'=>'yes','id'=>$id));
                    exit();
                }
            }
            
            
        }
    }
    //附件删除
    public function del_file_stu()
    {
        if(IS_AJAX)
        {
            $id = I('post.id',0,'intval');
             
            $_info = M('stu_file')->where(array('id'=>$id))->find();
            if(!$_info)
            {
                $this->ajaxReturn(array('status'=>'no','msg'=>'该附件不存在！'));
                exit();
            }
            else
            {
                unlink('./Uploads'.$_info['file_path']);
                if(!$this->student_mod->delData('stu_file',array('id'=>$id)))
                {
                    $this->ajaxReturn(array('status'=>'no','msg'=>'删除失败！'));
                    exit();
                }
                else
                {
                    $this->ajaxReturn(array('status'=>'yes','msg'=>'删除成功！'));
                    exit();
                }
            }
        }
    }
    
    /**
     *  输送学生信息 (接收)
     */
    public function accept()
    {
        if(IS_AJAX)
        {
            $id = I('post.id',0,'intval');
            
            $info = $this->apply_mod->get_receive_info($id);
            if(!$info)
            { 
                echo $this->ajaxReturn(array('status'=>'no','msg'=>'输送信息不存在！'));
                exit();
            }
            elseif($info['status']!=0)
            {
                echo $this->ajaxReturn(array('status'=>'no','msg'=>'您已经操作过！'));
                exit();
            }
            elseif($info['member_id']!=$this->member_id)
            {
                echo $this->ajaxReturn(array('status'=>'no','msg'=>'您没有权限操作！'));
                exit();
            }
            
            //更新申请状态
            $condition = array('stu_apply_id'=>$info['apply_id'],'receive_member'=>$this->member_id);
            M('stu_apply')->where($condition)->setField('status',$this->status_other['is_Receive']);
            //添加日志信息
            $log = array(
                'operate_user_id'=>$this->member_id,
                'operate_content'=>'',
                'update_status'=>$this->status_other['is_Receive'],
                'operate_time'=>time(),
            );
            D('Log')->add_log($info['apply_id'],$log);
            //更新输送信息
            M('stu_receive')->where('receive_id='.$info['receive_id'])->setField(
                array('status'=>1,'receive_time'=>time())
            );
            
            //添加一条他生信息
            //判断该学生是否已经添加过
            $num = M('stu')->where(array('member_id'=>$this->member_id,'stu_id'=>$info['stu_id']))->count();
            if(!$num)
            {
                $insert_stu = array(
                    'member_id'=>$this->member_id,
                    'stu_id'=>$info['stu_id'],
                    'type'=>2,
                    'transportation'=>$info['from_member_id'],
                    'add_time'=>time(),
                );
                $stu_id = M('stu')->add($insert_stu);
            }
            
            $this->apply_mod->update_apply_success_count($info['stu_id']);
            
            $url = U('Home/Student/index',array('stu'=>$stu_id));
            $this->ajaxReturn(array('status'=>'yes','url'=>$url));
        }
    }
    
    /**
     *  输送学生信息 (拒绝)
     */
    public function refuse()
    { 
        if(IS_AJAX)
        { 
            $id = I('post.id',0,'intval');
            $content = I('post.content','','trim');
            $info = $this->apply_mod->get_receive_info($id);
 
            if(!$info){
                echo $this->ajaxReturn(array('status'=>'no','msg'=>'输送信息不存在！'));
                exit();
            }
            
            //更新申请状态
            $condition = array('stu_apply_id'=>$info['apply_id'],'receive_member'=>$this->member_id);
            M('stu_apply')->where($condition)->setField(
                array('status'=>$this->status_other['no_Receive'],'reason'=>$content)
            );
            //添加日志信息
            $log = array(
                'operate_user_id'=>$this->member_id,
                'operate_content'=>'',
                'update_status'=>$this->status_other['no_Receive'],
                'operate_time'=>time(),
            );
            D('Log')->add_log($info['apply_id'],$log);
            
            //更新输送信息
            M('stu_receive')->where('receive_id='.$info['receive_id'])->setField(
            array('status'=>2,'refuse_time'=>time())
            );
            
            echo $this->ajaxReturn(array('status'=>'yes'));
        } 
    }
    
    /**
     *  学生模糊搜索 
     */
    public function searchbyname()
    {
        if(IS_AJAX)
        {
            $name = I('param.value','','trim');
            $page = isset($_GET['P'])?intval($_GET['P']):1;
            //搜索条件
            $condition['a.member_id']=$this->member_id;
            $condition['b.stu_name'] = array('like','%'.$name.'%');
            
            $list = $this->student_mod->get_stu_list($condition,$page,10);
            if(empty($list))
            {
                $list = 0;
            }
            
            $this->ajaxReturn(array('status'=>'yes','info'=>$list));
            exit;
        }
    }
}


?>