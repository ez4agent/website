<?php
/**
 *  CRM -- 院校模型 
 */
namespace Home\Model;
use Think\Model;

class SchoolModel extends Model
{
    /**
     *  获取一天院校的基本数据 
     */
    public function get_college_info($college_id)
    {
        $_info= M('college')->where('college_id='.$college_id)->find();
        $_info['edu'] = get_educationbycollege_id($_info['college_id']);
        $array = get_namebyarea($_info['country_id'],$_info['area_id'],$_info['city_id']);
        $_info= array_merge($_info,$array);
        return $_info;    
    }
    
    //获取查看院校的名称
    public function get_view_college_list($member_id)
    {
        $_view = array();
        $_view = M('college_view')
                    ->field('c.college_id,c.cname,c.ename,c.schoolbadge')
                    ->alias('cv')
                    ->join(C('DB_PREFIX').'college c ON cv.college_id = c.college_id','LEFT')
                    ->where('cv.member_id='.$member_id)
                    ->order('c.college_id desc')
                    ->select();
        return $_view;
    }
    
    //获取已经申请院校的列表
    public function get_apply_college_list($member_id)
    {
        $_apply = array();
        $_apply = M('college_apply_view')
                  ->field('c.college_id,c.cname,c.ename,c.schoolbadge')
                  ->alias('cv')
                  ->join(C('DB_PREFIX').'college c ON cv.college_id = c.college_id','LEFT')
                  ->where('cv.member_id='.$member_id)
                  ->order('c.college_id desc')
                  ->select();
        return $_apply;
    }
    
    /**
     *  统计院校个数 
     */
    public function get_count($where)
    {
        return M('college')->where($where)->count();
    }
    
    /**
     *  院校列表 
     */
    public function get_college_list($where=array(),$limit='',$order='college_id desc')
    {
        $_list = array();
        $_list = M('college')->where($where)->field('college_id,cname,ename,schoolbadge')
                             ->order($order)->limit($limit)->select();
        return $_list;
    }
    
    /**
     *  国家属性筛选 （字母显示）
     */
    public function get_country_Attribute($where=array())
    {
        $condition = array();
        if(!empty($where))
        {
            //院校类别
            if($where['type']!=0)
            {
                $_college_id = M('college_type')->where('type='.$where['type'])->field('college_id')->select();
                if($_college_id)
                {
                	foreach ($_college_id as $key=>$val)
                	{
                		$_ids[$key]=$val['college_id'];
                	}
                }
                $condition['college_id']=array('IN',implode(',',$_ids));
            }
        }
        
        $country_zimu =array();
        $_Attribute = M('college')->where($condition)->field('country_id')->group('country_id')->select();
        if($_Attribute)
        {
            foreach($_Attribute as $key=>$val)
            {
                $country_zimu[$key] =M('country')->where('countryid='.$val['country_id'])->getField('zimu');
            }
        }
        return $country_zimu;
    }
    
    /**
     *  州省属性筛选 
     */
    public function get_area_Attribute($where=array())
    {
        $condition = array();
        if(!empty($where))
        {
            if($where['country_id']!=0)
            {
                $condition['country_id'] = $where['country_id'];
            }
            //类别
            if($where['type']!=0)
            {
                $_college_id = M('college_type')->where('type='.$where['type'])->field('college_id')->select();
                if($_college_id)
                {
                    foreach ($_college_id as $key=>$val)
                    {
                        $_ids[$key]=$val['college_id'];
                    }
                }
                $map['college_id']=array('IN',implode(',',$_ids));
            } 
        }
        
        $_areaa_zimu = array();
        $_Attribute = M('college')->where($condition)->field('area_id')->group('area_id')->select();
        if($_Attribute)
        {
            foreach($_Attribute as $key=>$val)
            {
                $_area_zimu[]=M('area')->where('aid='.$val['area_id'])->getField('zimu');
            }
        }
        return $_area_zimu;
    }
    
    /**
     *  城市属性筛选 
     */
    public function get_city_Attribute($where=array())
    {
        $condition = array();
        if(!empty($where))
        {
            if($where['country_id']!=0)
            {
                $condition['country_id']=$where['country_id'];
            }
            
            if($where['area_id']!=0)
            {
                $condition['area_id'] = $where['area_id'];
            }

            if($where['type']!=0)
            {
                $_college_id = M('college_type')->where('type='.$where['type'])->field('college_id')->select();
                if($_college_id)
                {
                	foreach ($_college_id as $key=>$val)
                    {
                		$_ids[$key]=$val['college_id'];
                	}
                }
                $map['college_id']=array('IN',implode(',',$_ids));
            }
        }
        
        $_city_zimu = array();
        $_Attribute = M('college')->where($condition)->field('city_id')->group('city_id')->select();
        if($_Attribute)
        {
            foreach($_Attribute as $key=>$val)
            {
                $_city_zimu[] = M('city')->where('cid='.$val['city_id'])->getField('zimu');
            }
        } 
        
        return $_city_zimu;
    }
    
    /**
     *  院校类别 
     */
    public function get_CollegeType_Attribute($where=array())
    {
        $map1 = array();
        if(!empty($where))
        {
            if($where['country_id']!=0)
            {
             	$map['country_id']=$where['country_id'];
            }
             
            if($where['area_id']!=0)
            {
                $map['area_id']=$where['area_id'];
            }
             
            if($where['city_id']!=0)
            {
             	$map['city_id']=$where['city_id'];
            }
             
            $_id = M('college')->where($map)->field('college_id')->select();
            if($_id)
            {
             	foreach ($_id as $key=>$val)
             	{
             	      $_ids[$key]=$val['college_id'];
             	}
             	$map1['college_id']=array('IN',implode(',',$_ids));
            }
             
            if($map1['college_id'] && $where['education']!=0)
            {
        
                $condition['college_id'] = $map1['college_id'];
                $condition['education']=$where['education'];
                $_college_id = M('college_education')->where($condition)->field('college_id')->select();
                if($_college_id)
                {
                    foreach ($_college_id as $key=>$val)
                    {
                        $_ids[$key]=$val['college_id'];
                    }
                }
                $map1['college_id']=array('IN',implode(',',$_ids));
            }
        }
        $_type = array();
        $_Attribute = M('college_education')->where($map1)->field('education')->group('education')->select();
         
        if(!empty($_Attribute))
        {
            $edu = C('Education_TYPE');
            foreach ($_Attribute as $key =>$val)
            {
                $Attribute[$key] = array(
                    'id'   =>$val['education'],
                    'name' =>$edu[$val['education']],
                );
            }
        }
        return $Attribute;
    }
    
    /**
     *	根据typeID 获取college_id
     */
    public function get_collegeIdByType($type_id)
    {
        $_id = M('college_type')->where('type='.$type_id)->field('college_id')->select();
        if($_id)
        {
            foreach ($_id as $key=>$val)
            {
                $_ids[$key]=$val['college_id'];
            }
        }
        return $_ids;
    }
    
    
}

?>