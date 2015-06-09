<?php
/**
 *  CRM -- 合作院校模型
 */
namespace Home\Model;
use Think\Model;
class PartnerModel extends Model
{
    /**
     *  根据会员读相应的合作院校 
     */
    public function get_partner_listbymemberId($member_id)
    {
        $_partner = array();
        $_partner = M('partner_college')
                    ->field('pc.partner_id,c.cname,c.ename,c.schoolbadge')
                    ->alias('pc')
                    ->join(C('DB_PREFIX').'college c ON pc.college_id = c.college_id','LEFT')
                    ->where('pc.member_id='.$member_id)
                    ->order('addtime desc')
                    ->select();
        return $_partner;
    }
    
    //添加合作院校
    public function add_partner_info($data)
    {
       if(!isset($data['college_id'])){
           return false;
       }
       
       $partner_id = $this->add_info('partner_college',$data);
       /*
       $education = C('Education_TYPE');
       $college_education = M('college_education')->field('id,education')
                       ->where('college_id='.$data['college_id'])->select();
       if(!empty($college_education))
       {
           foreach($college_education as $key=>$val)
           {
              //插入数据
                $insert_data = array(    
                    'partner_id' =>$partner_id,
                    'member_id'  =>intval($data['member_id']),
                    'college_id' =>$data['college_id'],
                    'apply_id'   =>$val['education'],
                    'education'  =>$education[$val['education']],
                    'is_share'   =>1,
                ); 
                $commission_id=$this->add_info('partner_college_commission',$insert_data);
                if($commission_id)
                {
                    for ($i=1; $i<=4;$i++) 
                    {
                        $value = array(
                            'college_id'=>intval($data['college_id']),
                            'member_id'=>intval($data['member_id']),
                            'commission_id'=>$commission_id,
                            'times'=>$i,
                            'sharing_ratio'=>'',
                        );
                        $this->add_info('share_value', $value);
                    }
                }
           }
       }
       */
       return $partner_id;
    }
    
    /**
     *  取消合作院校 
     */
    public function cancel_partner($college_id,$member_id)
    {
        if($college_id && $member_id)
        {
            $map = array('college_id'=>$college_id,'member_id'=>$member_id);
            M('partner_college_commission')->where($map)->delete();
            M('share_value')->where($map)->delete();
            M('partner_college')->where($map)->delete();
            return true;
        }
        else 
        {
            return false;
        }
    }
    
    /**
     *   用于单表添加操作
     */
    public function add_info($table,$data)
    {
        return M($table)->add($data);
    }
    
    /**
     *  按院校ID获取学历(select option选项)
     */
    public function get_edu_option($college_id,$select_id=0)
    {
        $edu = get_educationbycollege_id($college_id);
        $html="";
        foreach($edu as $key =>$val)
        {
            if($val['id']==$select_id )
            {
                $select="selected";
            }
            else 
            {
                $select="";
            }
            $html.="<option value='".$val['id']."'".$select.">".$val['name']."</option>";
        }
        
        return $html;
    }
    
    /**
     * 合作院校的信息列表   
     */
    public function get_partner_list($table,$where=array())
    {
        return M($table)->where($where)->select();    
    }
    
    //根据院校读出佣金分享
    public function get_share_college($college_id,$commission_id='',$page='',$pagesize=10)
    {
        $type = C('pay_type');
        
        $where['college_id']= $college_id;
       // $where['is_share'] = 1;
        if($commission_id)
        {
            $where['apply_id']=$commission_id; 
        }
        
        $share = M('partner_college_commission')->where($where)->page($page.','.$pagesize)->select(); 
        //echo M('partner_college_commission')->getLastSql();exit;

        $share1 = array();
        if(!empty($share))
        {
            foreach($share as $key=>$val)
            {
               $share1[$val['commission_id']] = $val;        
            }
            
            foreach ($share1 as $key1=>$val1)
            {
                //获取中介名称
                $share1[$key1]['username'] = getField_value('member','username',array('member_id'=>$val1['member_id']));
                $share1[$key1]['type_name'] = $type[$val1['pay_type']];
                //获取分享比例
                /*
                $map = array('commission_id'=>$val1['commission_id'],'sharing_ratio'=>array('neq',0));
                
                $share_value1 = array();
                $share_value = M('share_value')->where($map)->select();
                if(!empty($share_value)){
                    foreach ($share_value as $key2=>$val2)
                    {
                        $val2['sharing_ratio1'] = $val2['sharing_ratio']."%";
                        $share_value1[$val2['times']]=$val2;
                    }
                    $share1[$key1]['value1'] = $share_value1;
                }else{
                    unset($share1[$key1]);
                }
                */
            }
        }
        return $share1;    
    }
    
    //获取一条学历申请信息
    public function get_info_commission_id($college_id,$commission_id)
    {
        $info = array();
        if($commission_id)
        {
            $map = array('college_id'=>$college_id,'commission_id'=>$commission_id);
            
            $info = M('partner_college_commission')->where($map)->find();

            if(!empty($info)){
                $type = C('pay_type');
                $share_length_str = $info['share_length'];
                if($info['pay_type'] == 1){
                    $share_length_str.=" 学年";
                }elseif($info['pay_type'] == 2){
                    $share_length_str.=" 学期";
                }else{
                    $share_length_str.="一次性";
                }

                $info['share_length_str'] = $share_length_str;
                $info['pay_type_str'] = $type[$info['pay_type']];

                $info['username'] = M("member")->where('member_id='.$info['member_id'])->getField('username');
            }

            /*
            if(!empty($info))
            {
                $info['username'] = M("member")->where('member_id='.$info['member_id'])->getField('username');
                if($info['is_share']==1)
                {
                    $share_value = M('share_value')->where('commission_id='.$commission_id)->select();
                    $share="";
                    foreach($share_value as $key =>$val)
                    {
                        if($val['sharing_ratio']!=0)
                        {
                            $share.="(".$val['times'].")".$val['sharing_ratio']."%  ";
                        }
                    }
                    
                    $info['share_value'] = $share;
                }
            }*/
        }
        
        return $info;
    }
    
    
}

?>