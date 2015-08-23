<?php

namespace Colleges\Model;
use Think\Model;

/**
 *  院校模型
 */
class CollegesModel extends Model
{
	/**
	 *	获取院校类型 
	 */
	public function get_type_list($where=array())
	{
		$_list = array();
		$_list = M('type')->where($where)->field('id,typename,is_used')->select();
		return $_list;
	}
	
	
    /**
     *  统计院校数量 
     */
    public function get_count($where=array())
    {
        return M('college')->where($where)->count();
    }
    
    /**
     *  统计院校求助数量 
     * 
     */
    public function get_help_count($where=array())
    {
        return M('get_help_count')->where($where)->count();
    }
    
    /**
     *  院校列表 
     */
    public function get_college_list($where,$limit=0,$order='college_id asc')
    {
       $college_type = C('COLLEGE_TYPE');
       $_list = array(); 
       $_list = M('college')->where($where)->field('college_id,cname,ename,country_id,city_id,website')
                            ->order($order)->limit($limit)->select();

       if(!empty($_list))
       {
           foreach ($_list as $key=>$val)
           {
               $_list[$key]['country'] = M('country')->where('countryid='.$val['country_id'])->getfield('cname');
               $_list[$key]['city'] = M('city')->where('cid='.$val['city_id'])->getfield('cname');
               //类型
               $type = M('college_type')->where('college_id='.$val['college_id'])->field('type')->select();

               if($type)
               {
                   $str = '';
                   foreach($type as $key1=>$val1)
                   {
                       $str.=M('type')->where('id='.$val1['type'])->getField('typename').'&nbsp;';
                   }
               }
               $_list[$key]['type_str'] = $str;
           }
       }
       return $_list;
    } 
    
    /**
     *  获取院校求助列表  
     *  
     */
    public function get_help_list($where,$limit,$order='addtime desc')
    {
        $_list = array();
        $_list = M('college_help')->where($where)->order($order)->limit($limit)->select();
        if(!empty($_list))
        {
            foreach($_list as $key =>$val)
            {
                $_list[$key]['college_name']=getField_value('college', 'cname',array('college_id'=>$val['college_id']));
                $_list[$key]['member_name']=getField_value('member','username',array('member_id'=>$val['member_id']));
            }
        }
        
        return $_list;
    }
    
    /**
     *  获取数据 
     */
    public function get_one_info($college_id)
    {
        $_info = array();
        if($college_id)
        {
            $_info =M('college')->where('college_id='.$college_id)->find();
            
        }
        return $_info;
        
    }
    
    
    /**
     *  添加数据
     *  @param array $data
     *  @return boolean 
     */
    public function add_data($data)
    {
        if(!is_array($data)){
            return false;
        }
        //主表操作
        $college_id = M('college')->add(
            array(
                'cname'=>trim($data['cname']),
                'ename'=>trim($data['ename']),
                'country_id'=>intval($data['class_id']),
                'area_id'=>intval($data['area_id']),
                'city_id'=>intval($data['city_id']),
                'apply_price'=>floatval($data['apply_price']),
                'schoolbadge'=>trim($data['image']),  
                'introduction'=>trim($data['content']),
                'website' =>trim($data['website']) 
            ));
        if($college_id)
        {
            //院校与院校分类
            foreach ($data['type'] as $val)
            {
                $type[] = array('college_id'=>$college_id,'type'=>$val);
            }   
            M('college_type')->addAll($type); 
			
			//院校与学历的管理
			foreach($data['education'] as $val)
			{
				$education[] = array('college_id'=>$college_id,'education'=>$val);
			}
			M('college_education')->addAll($education);


            $is_partner = isset($data['is_partner'])  && $data['is_partner'] ? 1 :0;
            //是否合作院校
            if($is_partner) {
                $partner_member = C('SYSTEM_PARTNER_MEMBER');
                M('partner_college')->add(
                    array(
                        'member_id' => $partner_member,
                        'college_id' => $college_id,
                        'addtime' => time()
                    ));
            }

            return true;
        } 
        return false;
    }
    
    /**
     *	添加院校类型信息
     *	@param array $data
     *	@return boolean 
     */
    public function save_Type($data ,$type='add')
    {
    	if(!is_array($data)){
    		return false;
    	}
    	if($type=='add'){
    		return M('type')->add(array('typename'=>$data['typename'],'is_used'=>$data['is_used'],'addtime'=>time()));
    		
    	}elseif($type=='edit'){
    		if(empty($data['type_id'])){
    			return false;
    		}
    		$status = M('type')->where('id='.$data['type_id'])->save(array('typename'=>$data['typename'],'is_used'=>$data['is_used']));
    		if($status === false){
    			return false;
    		}
    	}
    	return true;
    }
    
    /**
     * 删除类别数据
     * @param intval $college_id
     * @return bool
     */
    public function delTypeData($id)
    {
    	M('type')->where('id='.$id)->delete();
    	return true;
    }
    
    //修改数据
    public function edit_data($data)
    {
        if(!is_array($data)){
            return false;
        }
        //主表操作
        M('college')->where('college_id='.intval($data['college_id']))->save(
            array(
                'cname'=>trim($data['cname']),
                'ename'=>trim($data['ename']),
                'country_id'=>intval($data['class_id']),
                'area_id'=>intval($data['area_id']),
                'city_id'=>intval($data['city_id']),
                'apply_price'=>floatval($data['apply_price']),
                'schoolbadge'=>trim($data['image']),
                'introduction'=>trim($data['content']),
                'website' =>trim($data['website'])
            ));
        //院校与院校分类
        M('college_type')->where('college_id='.intval($data['college_id']))->delete();
        foreach ($data['type'] as $val)
        {
            $insert[] = array('college_id'=>intval($data['college_id']),'type'=>$val);
        }
        M('college_type')->addAll($insert);
        //院校与学历
        M('college_education')->where('college_id='.intval($data['college_id']))->delete();
        foreach($data['education'] as $val)
        {
        	$education[] = array('college_id'=>intval($data['college_id']),'education'=>$val);
        }
        M('college_education')->addAll($education);

        $is_partner = isset($data['is_partner']) && $data['is_partner'] ? 1 :0;
        $college_id = intval($data['college_id']);
        $partner_member =C('SYSTEM_PARTNER_MEMBER');

        //是否合作院校
        if($is_partner){
            $partner_college = M('partner_college')->where(array('college_id'=>$college_id,'member_id'=>$partner_member))->find();
            if(!$partner_college){
                M('partner_college')->add(
                    array(
                        'member_id'=>$partner_member,
                        'college_id'=>$college_id,
                        'addtime'=>time()
                    ));
            }
        }else{
            M('partner_college')->where(
                array(
                    'member_id'=>$partner_member,
                    'college_id'=>$college_id
                ))->delete();
        }

        return true;
    }
    
    /**
     * 删除数据
     * @param intval $college_id
     * @return bool
     */
    public function delData($college_id)
    {
        M('college')->where('college_id='.$college_id)->delete();
        M('college_type')->where('college_id='.$college_id)->delete();
        return true;
    }
}

?>