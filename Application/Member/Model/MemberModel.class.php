<?php
/**
 *  会员模型 
 */
namespace Member\Model;
use Think\Model;

class MemberModel extends Model
{
    /**
     *  获取会员等级列表 
     *  @param array $where
     *  @return array
     */
    public function getMember_grade($where=array())
    {
        $grade = array();
        $grade = M('member_grade')->where($where)->field('id,gradename,free_stu,child_account,is_used')->select();
        return $grade;
    }
    
    /**
     *	操作会员等级
     *  @param array 
     *  @return boolean 
     */
    public function save_Grade($data,$type='add')
    {
    	if(!is_array($data)){
    		return false;
    		exit();
    	}
    	
    	if($type=='add'){
    		
    		return M('member_grade')->add(array('gradename'=>$data['gradename'],
    				                            'free_stu'=>$data['free_stu'],
    										    'child_account'=>$data['child_account'],
    											'is_used'=>$data['is_used']
    		                        ));
    	}elseif($type=='edit'){
    		if(empty($data['grade_id'])){
    			return false;
    		}
    		$status = M('member_grade')->where('id='.$data['grade_id'])
    		                           ->save(array('gradename'=>$data['gradename'],
    				                            'free_stu'=>$data['free_stu'],
    										    'child_account'=>$data['child_account'],
    											'is_used'=>$data['is_used']
    		                              ));
    		if($status === false){
    			return false;
    		}
    	}
    	return true;	
    }
    
    public function delGradeData($id)
    {
    	M('member_grade')->where('id='.$id)->delete();
    	return true;
    }
    
    /**
     *  数据统计 
     */
    public function get_count($where=array())
    {
        return M('member')->where($where)->count();
    }
    
    /**
     *  获得会员列表 
     */
    public function get_list($where=array(),$limit=0,$order='add_time desc')
    {
        
        $_list = array();
        $_list = M('member')->where($where)
            ->field('member_id,username,is_open,grade,add_time')
            ->limit($limit)
            ->order($order)
            ->select();
        /*
        $_list = M('member')->alias('m')->where($where)
                            ->field('m.member_id,m.username,m.is_open,m.grade,m.add_time')
                            ->join('RIGHT JOIN __MEMBER_CHILD__ mc ON m.member_id = mc.member_id')
                            ->limit($limit)
                            ->order($order)
                            ->select();
        */
        if(!empty($_list))
        {
            foreach ($_list as $key=>$val)
            {
                $_list[$key]['gradename'] = M('member_grade')->where('id='.$val['grade'])->getfield('gradename');
                $_info = M('member_info')->where('member_id='.$val['member_id'])->field('member_type,mobile,telephone')->find();
                $_list[$key]['member_type'] = $_info['member_type'];
                $_list[$key]['mobile']=$_info['mobile'];
                $_list[$key]['telephone']=$_info['telephone'];
                $_list[$key]['child_num'] = M('member_child')->where('pid='.$val['member_id'])->count();
            }
        }
        return $_list;
    }
    
    /**
     *  获取会员信息 
     *  @param intval $member_id
     *  @return array
     */
    public function get_member_info($member_id)
    {
        $_info = array();
        $_info = M('member_info')->where('member_id='.$member_id)->find();
        if($_info)
        {
            $_info['username']=M('member')->where('member_id='.$_info['member_id'])->getfield('username');
            $_info['country_name']=M('country')->where('countryid='.$_info['country_id'])->getfield('cname');
            $_info['area_name']=M('area')->where('aid='.$_info['area_id'])->getfield('cname');
            $_info['city_name']=M('area')->where('aid='.$_info['city_id'])->getfield('cname'); 
        }
        $email = M('email_set')->where('member_id='.$member_id)->find();
        $account = M('account_set')->where('member_id='.$member_id)->find();
        
        $info = array_merge($_info,$email,$account);
        
        return $info;
    }
    
    public function edit_member_Data($data,$member_id)
    {
        $result=M('member')->where('member_id='.$member_id)->save($data);
        if($result ===false)
        {
            return false;
        }
        S('member_'.$member_id,null);
        return true;
    }
    
    /**
     *  删除会员
     *  @param intval $member_id
     *  @return boolean 
     */
    public function delData($member_id)
    {
        M('member')->where('member_id='.$member_id)->delete();
        M('account_set')->where('member_id='.$member_id)->delete();
        M('email_set')->where('member_id='.$member_id)->delete();
        M('member_file')->where('member_id='.$member_id)->delete();
        return true;
    }
}


?>