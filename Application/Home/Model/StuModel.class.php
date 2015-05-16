<?php
/**
 *  CRM--学生模型 
 */
namespace Home\Model;
use Think\Model;

class StuModel extends Model
{
    /**
     *  利用学生的证件号，判断学生重名 
     */
    public function is_unique($card_type,$card_id)
    {
        if($card_type && $card_type)
        {
            $map=array('card_type'=>$card_type,'card_id'=>$card_id);
            $count = M('stu_info')->where($map)->count();
            if($count)
            {
                return false;
            }
            return true;
        }
    }
    
    /**
     *  获取最近一个添加的学生ID 
     */
    public function get_last_stuId($member_id)
    {
        return M('stu')->field('id')->where('member_id='.$member_id)->order('id desc')->find();
    }
    
    /**
     *  获取自己学生列表 
     */
    public function get_stu_list($where=array(),$page,$count)
    {
        $_list = array();
        $_list = M('stu')->alias('a')->join('__STU_INFO__ b ON a.stu_id= b.stu_id','LEFT')
                 ->where($where)->field('a.id,a.stu_id,a.type,b.stu_name')->page($page.','.$count)
                 ->order('a.add_time desc')->select();
        if(!empty($_list))
        {
            foreach($_list as $key =>$val)
            {
               $stu_name = str_replace(',', '', $val['stu_name']);
               if($val['type']==1)
               {
                  $_list[$key]['name'] = '自生 - '.$stu_name; 
               }
               else 
               {
                   $_list[$key]['name'] = '他生 - '.$stu_name;
               }
               
               $_list[$key]['url'] = U('Home/Student/index',array('stu'=>$val['id']));
            }
        }
        return $_list;
    }
    
    
    /**
     *  统计自己学员，他人学员，推送学员数量 
     */
    public function count_num_stu($member_id,$type)
    {
       $num = 0;
       $con = array('member_id'=>$member_id,'type'=>$type);
       $num = M('stu')->where($con)->count();
       return $num;
    }
    
    /**
     *  生成学生选择框 
     */
    public function select_stu($stu_id,$member_id,$type='')
    {
        $str="";
        $select="";
        
        $where['member_id'] =$member_id;
        if($type){
            $where['type'] = $type;
        }  
        $list =M('stu')->where($where)->field('id,stu_id,type')->select();
        if(!empty($list))
        { 
            $str="";
            foreach($list as $key=>$val)
            {
                $stu_name = M('stu_info')->where('stu_id='.$val['stu_id'])->getField('stu_name');
                $stu_name = str_replace(',', '', $stu_name);
                if($val['type']==1){
                    $name= '自生-'.$stu_name;
                }else{
                    $name='他生-'.$stu_name;
                }   
                if($val['id'] == $stu_id)
                {
                    $select="selected";
                }else{
                    $select="";
                }
                $str.="<option value='".$val['id']."'".$select.">".$name."</option>";
            }
        }
        return $str;
    }
    
   /**
     * 读取一条学生信息数据
     * @param intval stu_id
     * @return array 
     */
    public function get_StuInfo($stu_id)
    {
        $_info = array();
        $_info = M('stu')->alias('s')->join('__STU_INFO__ b ON b.stu_id= s.stu_id')
                         ->where('s.id='.$stu_id)
                         ->find();
        $_info['stu_name']=str_replace(',', '', $_info['stu_name']);
        $_info['pinyin']=str_replace(',', '', $_info['pinyin']);
        if($_info['type']==2)
        {
            $_info['transportation_name'] = M('member')->where('member_id='.$_info['transportation'])->getField('username');
        }
        else 
        {
            $_info['transportation_name'] = M('member')->where('member_id='.$_info['member_id'])->getField('username');
        }
        
        $area = get_namebyarea($_info['countryid'],$_info['areaid'],$_info['cityid']);
        $_info['location'] = $area['city_name'].','.$area['area_name'].','.$area['country_name'];
        return array_merge($_info,$area);
        
    } 
    
    /**
     *	添加学员信息
     *  @param array $data
     *  @return boolean;
     */
    public function editData($type='add',$data)
    {
        $result = array('data'=>'','msg'=>'');
        if(!is_array($data)){
            return false;
        }
        if($type=='add')
        {
            $data['add_time'] = time();
            $stu_id = M('stu_info')->add($data);
            
            if(!$stu_id){
                $result['msg'] = '添加失败！';
            }
            else 
            {
                $result['data']=$stu_id;
            }
        }
        elseif($type=='edit')
        {
            if(empty($data['stu_id']))
            {
                return false;
            }
            
            if(M('stu_info')->where('stu_id='.$data['stu_id'])->save($data)===false)
            {
                $result['msg'] = '修改失败！';
            }
        }
        return $result;
    }
    
    /**
     *  获取学生附件
     *  @param intval id
     *  @return array 
     */
    public function get_stu_file($stu_id)
    {
        $file = array();
        $info = $this->get_StuInfo($stu_id);
        
        if($info['type'] == '2')//他生
        {
            $file=M('stu_apply_file')->where('stu_id='.$info['id'])->select();
        }
        else //自生
        { 
            $file = M('stu_file')->where('stu_id='.$info['id'])->select();
        }
        
        return $file;
    }
    
    /**
     *  删除信息
     *  @param intval id
     *  @return bool 
     */
    public function delData($table,$where=array())
    {
        return M($table)->where($where)->delete();
    }
    
    
    
}





?>