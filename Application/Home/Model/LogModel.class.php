<?php
//日志操作类 (日志操作类)
namespace Home\Model;
use Think\Model;

class LogModel extends Model
{
    /**
     *  添加用户操作日志 
     */
    public function add_log($apply_id,$data=array())
    {
        $insert_log = array(
            'apply_id'=>intval($apply_id),
            'operate_user_id'=>intval($data['operate_user_id']),
            'update_status'=>intval($data['update_status']),
            'operate_content'=>trim($data['operate_content']),
            'type'=>intval($data['type'])?intval($data['type']):0,
            'title'=>trim($data['title']),
            'operate_time'=>time(),
        );
        
        $log_id = M('stu_apply_operate_log')->add($insert_log);
        if(!empty($data['file']))
        { 
            $add_file = array();
            foreach ($data['file'] as $key =>$val)
            { 
                $info = M('stu_apply_uploadfile')
                        ->where(array('log_id'=>$log_id,
                                    'apply_id'=>$apply_id,
                                'file_name'=>$val['file_name'],
                         ))->find();
               if($info)
               { 
                  M('stu_apply_uploadfile')->where('file_id='.$info['file_id'])->setField(
                    array('file_name'=>$val['file_name'],
                          'file_path'=>$val['file_path'])
                  ); 
                   
               }    
               else
               {  
                   $add_file = array(
                       'log_id'=>$log_id,
                       'apply_id'=>$apply_id,
                       'file_name'=>$val['file_name'],
                       'file_path'=>$val['file_path'],
                   );
    
                   M('stu_apply_uploadfile')->add($add_file);
               }
            } 
        }  
        return $log_id;
    }
    
    /**
     *  获得院校申请操作日志 
     */
    public function get_log($apply_id)
    {
        $log_list = array();
        $log_list = M('stu_apply_operate_log')->where('apply_id='.$apply_id)->select();
        if(!empty($log_list))
        { 
            foreach ($log_list as $key=>$val)
            { 
                $log_list[$key]['member_name'] = getField_value('member','username',array('member_id'=>$val['operate_user_id'])); 
                $log_list[$key]['update_status_name']=D('Apply')->get_status_msg($val['update_status']);
                $log_list[$key]['file'] = M('stu_apply_uploadfile')->where(array('log_id'=>$val['log_id']))->select();
            }
        }
        return $log_list;
    }
}

?>