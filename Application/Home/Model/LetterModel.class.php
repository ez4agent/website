<?php
/**
 *  CRM --站内信模型 
 */
namespace Home\Model;
use Think\Model;

class LetterModel extends Model
{
    //获取站内信列表。
    public function get_email_list($type,$where,$firstRow,$listRows,$order='date desc')
    {
        if($type=='inbox')
        {
            $list=M('message_receiver')->alias('sr')->join('__MESSAGE_SENDER__ ms ON sr.mid= ms.mid')
            ->where($where)->order($order)->limit($firstRow.','.$listRows)
            ->select();

            if(!empty($list))
            {
                foreach($list as $key=>$val)
                {
                    $file_count = M('email_file')->where('msm_id='.$val['mid'])->count();
                    if($file_count>0)
                    {
                        $list[$key]['is_file'] = 1;
                    }
                    else
                    {
                        $list[$key]['is_file'] = 0;
                    }
                }
            }
        }
        elseif($type=='outbox') 
        {
            $list = M('message_sender')->where($where)->order($order)
            ->limit($firstRow.','.$listRows)->select();
            if(!empty($list))
            {
                foreach($list as $key=>$val)
                {
                    $info = M('message_receiver')->where('mid='.$val['mid'])->field('to_username,is_readed')->find();
                    $list[$key]['is_read']=$info['is_readed'];
                    $list[$key]['to_member_name']=$info['to_username'];
                    $file_count = M('email_file')->where('msm_id='.$val['mid'])->count();
                    if($file_count>0)
                    {
                        $list[$key]['is_file'] = 1;
                    }
                    else
                    {
                        $list[$key]['is_file'] = 0;
                    }
                }
            }
        }
        
        
        return $list;
    }
    
    //获取单个信件的信息
    public function get_email_info($mid)
    {
        $email_info = array();
        $email_info = M('message_receiver')->alias('sr')->join('__MESSAGE_SENDER__ ms ON sr.mid= ms.mid')
                      ->where('sr.mid='.$mid)->find();
        $email_info['file'] = M('email_file')->where('msm_id='.$email_info['mid'])->select();
        if($email_info['status']==2)
        {
            $condition['_string']='ms.repay_id='.$email_info['repay_id'].' OR '.'ms.mid='.$email_info['repay_id'];
            $email_info['repay_email_info'] = M('message_receiver')->alias('sr')->join('__MESSAGE_SENDER__ ms ON sr.mid= ms.mid')
                      ->where($condition)->order('ms.date desc')->select();
            
            if(!empty($email_info['repay_email_info']))
            {
                foreach($email_info['repay_email_info'] as $key=>$val)
                {
                    if($mid==$val['mid'])
                    {
                        unset($email_info['repay_email_info'][$key]);
                    }
                    else
                    {
                        $file = M('email_file')->where('msm_id='.$val['mid'])->select();
                        $email_info['repay_email_info'][$key]['file']=$file;
                    }
                }
            }
        }
        return $email_info;
    }
    
    //获取常用联系人
    public function get_contact_list($member_id)
    {
        $contact = array();
        $contact = M('contact')->where('member_id='.$member_id)->select();
        return $contact;
    }
    
}


?>