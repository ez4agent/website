<?php

/**
 *  CRM--会员模型   
 */

namespace Home\Model;
use Think\Model;

class MemberModel extends Model
{
    /**
     *  验证登陆
     *  @param  string username
     *  @return info 
     */
    public function check_login($username)
    {
        $info = array();
        $info= M('member')->where(array('username'=>$username))->find();
        return $info;
    }
    
    /**
     *  获取会员信息
     *  @param intval member_id
     *  @return array 
     */
    public function get_Member_Info($member_id)
    {
        $member_info = array();
        $_info = M('member_info')->alias('ai')
                 ->join('__MEMBER__ b ON b.member_id= ai.member_id','left')
                 ->where('ai.member_id='.$member_id)
                 ->find();   
        
        if(!empty($_info))
        {
           //国家、省、城市
           $array =array();
           $array = get_namebyarea($_info['country_id'],$_info['area_id'],$_info['city_id']); 
           $member_info = array_merge($_info,$array);
           //附件
           $member_info['file'] = $this->get_file_list($member_id);
           //固定电话
           $member_info['telephone1'] = explode('-',  $_info['telephone']);
           $member_info['mobile1']=explode('-', $_info['mobile']);
        }

        return $member_info;     
    }
    
    /**
     * 会员附件列表 
     */
    public function get_file_list($member_id)
    {
        $file = array();
        $file = M('member_file')->where('member_id='.$member_id)->select();
        if(!empty($file))
        {
           foreach ($file as $key=>$val)
           {
               //判断是否有.pdf
               $file_houzui= substr(strrchr($val['filepath'], '.'), 1);
               $file[$key]['houzui']  =$file_houzui;  
           } 
        }
        return $file;
    }
    
    /**
     *  注册操作 
     *  @param array data
     *  @return boolean
     */
    public function reg_Member_Info($data)
    {
        $result = array('data'=>'','error'=>'');
        
        $model = new Model();
        //开始事物
        $model->startTrans();
        //对登陆信息表操作
        $member_id = $this->insert_data('member', array(
                          'username'=>trim($data['username']),
                          'pwd'     =>md5($data['pwd']),                 
                          'is_open' =>1,
                          'grade'   =>1,
                          'add_time' =>time(),
                       ));
        if($member_id)
        {
            //会员基本信息
            $info=array(
                'member_id'=>$member_id,
                'member_type'=>intval($data['member_type']),
                'company'=>!empty($data['company'])?trim($data['company']):'',
                'contact'=>trim($data['contact']),
                'mobile' =>!empty($data['mobile'])?trim($data['mobile']):'',
                'telephone'=>!empty($data['telephone'])?trim($data['telephone']):'',
                'email1'=>trim($data['email_jiban']),
                'country_id'=>intval($data['country_id']),
                'area_id'=>intval($data['area_id']),
                'city_id'=>intval($data['city_id']),
                'address'=>trim($data['address']),
                'introduction'=>trim($data['introduction']),
            );
            $_info = $this->insert_data('member_info', $info);
            //会员跟子账户的管理
            $_child = $this->insert_data('member_child', array('member_id'=>$member_id,'pid'=>0));
            //给会员赋超级管理员权限
            $_p= $this->insert_data('member_power',array('member_id'=>$member_id,'identify'=>-1,'power'=>'_all'));
            
            if($_info  && $_child && $_p )
            {
                $result['data']=$member_id;
                $model->commit();
            }
            else
            {
                $model->rollback();
                $result['error']='注册失败';
            }
        }
        else
        {
            $model->rollback();
            $result['error']='注册失败';
        }
        
        return $result;            
    }
    
    
    /**
     *  更新会员信息
     *  @param array data,string type intval member_id
     *  @return result;  
     */ 
    public function save_Member_Info($data,$member_id,$type='info')
    {
        $result = array('data'=>'','error'=>'');
        switch ($type){
            //基本信息
            case 'info':
                if(trim($data['newpwd']) && trim($data['confirmpwd']) && ($data['newpwd']==$data['confirmpwd']))
                {
                    M('member')->where('member_id='.$member_id)->setField('pwd',md5(trim($data['newpwd'])));
                }
                
                $update =array(
                'telephone'=>trim($data['telephone']),
                'mobile'=>trim($data['mobile']),
                'email1'=>trim($data['email']),
                'country_id'=>intval($data['country_id']),
                'area_id'=>intval($data['area_id']),
                'city_id'=>intval($data['city_id']),
                'address'=>trim($data['address']),
                'contact'=>trim($data['contact']),
                'is_show'=>intval($data['is_show']),
                'introduction'=>trim($data['introduction']),
                );
                
                $this->update_data('member_info', $update, $member_id);
                break;
            
            default:
                break;     
        }
        return $result;
    }
    
    
    /**
     *  添加数据
     *  @param  string table,array data
     *  @return boolean
     */
    public function insert_data($table,$data)
    {
        return M($table)->add($data);
    }
    
    
    /**
     *  更新数据
     *  @param string table,array data ,intval id
     *  @return boolean;
     */
    public function update_data($table,$data,$id)
    {
        return M($table)->where('member_id='.$id)->save($data);
    }
    
    /**
     *  保存文件 
     */
    public function update_file($url,$title,$member_id)
    {
        $msg = '';
        $map = array('member_id'=>$member_id,'filename'=>$title);
        $info = M('member_file')->where($map)->find();
        if($info)
        {
           if(!M('member_file')->where('member_id='.$member_id)->save(array(
               'filename'=>$title,'filepath'=>$url
           )))
           {
              $msg='附件保存失败！';  
           }   
        }
        else
        {
            $file_id = M('member_file')->add(array(
                'member_id'=>$member_id,
                'filename'=>$title,
                'filepath'=>$url,
                'add_time'=>time(),
            ));
            
            if(!$file_id)
            {
                $msg='附件保存失败！';
            }
        }
        
        return $msg;
    }
    
}


?>