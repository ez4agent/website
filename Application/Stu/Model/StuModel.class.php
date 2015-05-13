<?php
/**
 *  学员模型 
 */
namespace Stu\Model;
use Think\Model;

class StuModel extends Model
{
    /**
     *  按条件统计学生数  
     *  @param array where
     *  @return intval count 
     */
    public function get_count($where=array())
    {
        $count = M('stu_info')->where($where)->count();
        return $count; 
    }
    
    /**
     *  按条件生成列表
     *  @param array where string limit string order
     *  @return array 
     */
    public function get_stu_list($where,$limit=0,$order='stu_id desc')
    {
        $_list=array();
        $_list = M('stu_info')->where($where)->field('stu_id,member_id,stu_name,pinyin,birthday,sex,add_time')
                         ->order($order)->limit($limit)->select();
        if(!empty($_list))
        {
            foreach ($_list as $key=>$val)
            {
                $_list[$key]['stu_name'] = str_replace(',','', $val['stu_name']);
                $_list[$key]['pinyin']   = str_replace(',','', $val['pinyin']); 
                //获取  用户用户名
                $_list[$key]['username'] = getField_value('member','username',array('member_id'=>$val['member_id']));
                
            }
        }
        
        return $_list;
    }
    
    /**
     *  获取一条学生信息
     *  @param intval stu_id
     *  @return array 
     */
    public function get_stu_info($stu_id)
    {
        $_info = array();
        $e_type = C('Education_TYPE');
        $_info = M('stu_info')->where('stu_id='.$stu_id)->find();
        $_info['stu_name'] = str_replace(',', '', $_info['stu_name']);
        $_info['pinyin'] = str_replace(',', '', $_info['pinyin']);
        if($_info)
        {
            $_info['file']=M('stu_file')->where('stu_id='.$stu_id)->field('file_name,file_path')->select();
            $_info['country_name']=M('country')->where('countryid='.$_info['countryid'])->getfield('cname');
            $_info['area_name']=M('area')->where('aid='.$_info['areaid'])->getfield('cname');
            $_info['city_name']=M('area')->where('aid='.$_info['cityid'])->getfield('cname');
            $_info['xueli'] = $e_type[$_info['education']];
        }
        
        return $_info;
    }
}

?>