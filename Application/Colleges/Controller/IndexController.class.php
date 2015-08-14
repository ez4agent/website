<?php
/**
 *  院校大全 
 */

namespace Colleges\Controller;
use Admin\Controller\AdminController;

class IndexController extends AdminController 
{
    /**
     * 当前模块参数
     */
    protected function _infoModule(){
        return array(
            'info'  => array(
                'name' => '院校大全',
                'description' => '管理系统所有院校',
            ),
            'menu' => array(
            	
            	array(
            		'name'=>'院校类别',
            		'url'=>U('type'),
            		'icon'=>'list',
                ),
            	array(
            		'name' => '添加类别',
            		'url' => U('addtype'),
            		'icon' => 'plus',
            	),
                array(
                    'name' => '院校列表',
                    'url' => U('index'),
                    'icon' => 'list',
                ),
                array(
                    'name' => '添加院校',
                    'url' => U('add'),
                    'icon' => 'plus',
                ),
                array(
                    'name'=> '院校求助',
                    'url' =>U('help'),
                    'icon'=>'plus',
                ),
            ),
        );
    }
    
    /**
     * 院校类型 
     */
    public function type()
    {
    	//类型列表
    	$type = D('Colleges')->get_type_list();
    	//位置导航
    	$breadCrumb = array('院校类型'=>U());
    	//模板传值
    	$this->assign('type',$type);
    	$this->assign('breadCrumb',$breadCrumb);
    	$this->adminDisplay();
    }
    
    /**
     *	添加类别 
     */
    public function addtype()
    {
    	if(!IS_POST)
    	{
    		$breadCrumb = array('院校类型'=>U('index'),'添加'=>U());
    		//模板传值
    		$this->assign('name','添加');
    		$this->assign('breadCrumb',$breadCrumb);
    		$this->adminDisplay('typeinfo');
    	}
    	else
    	{
    		if(D('Colleges')->save_Type($_POST,'add')){
    			$this->success('院校类型添加成功！');
    		}else{
    			$msg = D('Colleges')->getError();
    			if(empty($msg)){
    				$this->error('院校类型添加失败');
    			}else{
    				$this->error($msg);
    			}
    		}
    	}
    }
    
    /**
     *	修改列别 
     */
    public function edittype()
    {
    	if(!IS_POST)
    	{
    		$id = I('get.id','0','intval');
    		$info = M('type')->where('id='.$id)->find();
    		$breadCrumb = array('院校类型'=>U('index'),'修改'=>U());
    		//模板传值
    		$this->assign('info',$info);
    		$this->assign('name','修改');
    		$this->assign('breadCrumb',$breadCrumb);
    		$this->adminDisplay('typeinfo');
    	}
    	else
    	{
    		if(D('Colleges')->save_Type($_POST,'edit')){
    			$this->success('院校类型修改成功！');
    		}else{
    			$msg = D('Colleges')->getError();
    			if(empty($msg)){
    				$this->error('院校类型修改失败');
    			}else{
    				$this->error($msg);
    			}
    		}
    	}
    }
    
    /**
     *	删除类别 
     */
    public function deltype()
    {
    	$id = I('post.data','0','intval');
    	$_info =M('type')->where('id='.$id)->find();
    	if(!$_info){
    		$this->error('该数据不存在！');
    	}
    	//
    	$count = M('college_type')->where('type='.$id)->count();
    	if($count)
    	{
    		$this->error('该分类下面有关联院校，故不能删除！');
    	}
    	if(D('colleges')->delTypeData($id)){
            $this->success('院校类型删除成功！');
        }else{
            $msg = D('colleges')->getError();
            if(empty($msg)){
                $this->error('院校类型删除失败！');
            }else{
                $this->error($msg);
            }
        }
    }
    
    
    /**
     *  院校列表 
     */
    public function index()
    {
       //where条件
       $where = array();
       
       //关键字
       $keyword = I('request.keyword','','trim');
       if(!empty($keyword))
       {
           $where['_string'] = ' (cname like "%'.$keyword.'%")  OR ( ename like "%'.$keyword.'%") ';
       }
       
       //国家
       $country = I('request.country_id','0','intval');
       if(!empty($country))
       {
           $where['country_id']=$country;
       }
       
       //州
       $area = I('request.area_id','0','intval');
       if(!empty($area))
       {
           $where['area_id']=$area;
       }
       
       //城市
       $city = I('request.city_id','0','intval');
       if(!empty($city))
       {
           $where['city_id']=$city;
       }
       
       //URL参数
       $pageMaps = array();
       $pageMaps['keyword'] = $keyword;
       $pageMaps['country_id'] = $country;
       $pageMaps['area_id'] = $area;
       $pageMaps['city_id'] = $city;
       
       //查询数据
       $count = D('colleges')->get_count($where);
       $limit = $this->getPageLimit($count,20);
       $list = D('colleges')->get_college_list($where,$limit);
       
       if(!empty($list))
       {
           foreach($list as $key=>$val)
           {
               //获取未处理求助的数量
               $list[$key]['num'] = $this->get_help_num($val['college_id']);
           }
       }
       
       $this->assign('list',$list);
       //位置导航
       $breadCrumb = array('院校列表'=>U());
       $this->assign('page',$this->getPageShow($pageMaps));
       $this->assign('country',country());
       
       $this->assign('keyword',$keyword);
       //模板传值
       $this->assign('breadCrumb',$breadCrumb);
       $this->adminDisplay(); 
    }
    
    /**
     *  添加院校 
     */
    public function add()
    {
        if(!IS_POST){
            //位置导航
            $breadCrumb = array('院校列表'=>U('index'),'添加'=>U());
            $this->assign('name','添加');
            $this->assign('country',country());
            $this->assign('breadCrumb',$breadCrumb);
            $type = D('Colleges')->get_type_list(array('is_used'=>1));
            //类型
            foreach ($type as $key=>$val)
            {
				$type[$key]['select']=0;
            }
            //学历
            $education=C('Education_TYPE');
            foreach ($education as $key=>$val)
            {
            	$education1[$key]=array(
            		'id'  =>$key,
            	    'name'=>$val,
            		'select'=>0,
            	);
            }
            $this->assign('education',$education1);
            $this->assign('type',$type);
            $this->adminDisplay('collegeinfo');
        }else{
            
            if(D('Colleges')->add_data($_POST)){
                $this->success('院校添加成功！');
            }else{
                $msg = D('Colleges')->getError();
                if(empty($msg)){
                    $this->error('院校添加失败');
                }else{
                    $this->error($msg);
                }
            }
        }
    }
    
    /**
     *  修改院校 
     */
    public function edit()
    {
        if(!IS_POST)
        {
            $college_id = I('get.college_id','0','intval');
            $_info = D('Colleges')->get_one_info($college_id);
             $type = D('Colleges')->get_type_list(array('is_used'=>1));
            //类型
            foreach ($type as $key=>$val)
            {
                $num = M('college_type')
                           ->where(array('college_id'=>$_info['college_id'],'type'=>$val['id']))->count();
                if($num){
                    $type[$key]['select']=1;
                }else{
                    $type[$key]['select']=0;
                }
              
            }
            //学历
            $education=C('Education_TYPE');
            foreach ($education as $key=>$val)
            {
            	$num = M('college_education')->where(array('college_id'=>$_info['college_id'],'education'=>$key))->count();
            	
            	if($num){$select=1;}else{$select=0;}
            	
            	$education1[$key]=array(
            			'id'  =>$key,
            			'name'=>$val,
            			'select'=>$select,
            	);
            }
            //位置导航
            $breadCrumb = array('院校列表'=>U('index'),'修改'=>U());
            $this->assign('name','修改');
            $this->assign('country',country());
            $this->assign('education',$education1);
            $this->assign('info',$_info);
            $this->assign('type',$type);
            $this->assign('breadCrumb',$breadCrumb);
            $this->adminDisplay('collegeinfo');
        }
        else
        {
            if(D('Colleges')->edit_data($_POST)){
                $this->success('院校修改成功！');
            }else{
                $msg = D('Colleges')->getError();
                if(empty($msg)){
                    $this->error('院校修改失败！');
                }else{
                    $this->error($msg);
                }
            }
        }
    }
    
    /**
     *  删除院校 
     */
    public function del()
    {
        $college_id = I('post.data','0','intval');
        $_info =D('colleges')->get_one_info($college_id);
        if(!$_info){
            $this->error('该数据不存在！');
        }
        if(D('colleges')->delData($college_id)){
            $this->success('院校删除成功！');
        }else{
            $msg = D('colleges')->getError();
            if(empty($msg)){
                $this->error('院校删除失败！');
            }else{
                $this->error($msg);
            }
        }
    }
    
    /**
     *  批量删除 
     */
    public function batchAction()
    {
        $ids = I('post.ids');
        $type = I('post.type','0','intval');
        
        if(!$ids){
            $this->error('请选择要操作的数据');
        }
        
        if($type ==4)
        {
            foreach ($ids as $val)
            {
                D('colleges')->delData($val);
            }
            
            $this->success('院校删除成功！');
        }
    }
    
    //院校help
    public function help()
    {
       $college_id = I('request.college_id',0,'intval');
       if($college_id)
       {
           $where['college_id'] = $college_id;
       }
       
       $member_id = I('request.member_id',0,'intval');
       if($member_id)
       {
           $where['member_id'] = $member_id;
       }
       
       //URL参数
       $pageMaps = array();
       $pageMaps['college_id'] = $college_id;
       $pageMaps['member_id'] = $member_id;
       
       //查询数据
       $count = D('colleges')->get_help_count($where);
       $limit = $this->getPageLimit($count,20);
       $list = D('colleges')->get_help_list($where,$limit);
       
       //位置导航
       $breadCrumb = array('院校求助'=>U('help')); 
       //模板传值
       $this->assign('breadCrumb',$breadCrumb);
       $this->assign('list',$list);
       //位置导航
       $this->assign('page',$this->getPageShow($pageMaps));

       $this->adminDisplay();
    }
    
    /**
     *  help回复 
     */
    public function reply()
    {
        if(!IS_POST)
        {
            $id = I('get.id',0,'intval');
            $info = array();
            $info = M('college_help')->where('id='.$id)->find();
            $info['college_name'] = getField_value('college','cname',array('college_id'=>$info['college_id']));
            $info['member_name'] = getField_value('member','username',array('member_id'=>$info['member_id']));
            
            $breadCrumb = array('院校求助'=>U('index'),'回复'=>U());
            $this->assign('info',$info);
            $this->assign('breadCrumb',$breadCrumb);
            $this->adminDisplay();
        }
        else 
        {
            $content = I('post.content','','trim');
            $id = I('post.id',0,'intval');
            
            $update = array(
              'is_read'=>1,
               'reply'=>$content,
              'reply_time'=>time(),
            );
            
            if(!M('college_help')->where('id='.$id)->save($update))
            {
                $this->error('回复失败！');
                exit();
            }
            
            $info = M('college_help')->where('id='.$id)->find();
            
            //发站内信
            $insert = array(
                'from_member_id' =>0,
                'from_member_name'=>'系统',
                'to_member_id'=>$info['member_id'],
                'to_member_name'=>getField_value('member','username',array('member_id'=>$info['member_id'])),
                'title'=>'回复院校求助',
                'content'=>$content,
                'status'=>1,
                'type'=>1,
                'repay_id'=>0,
                'add_time'=>time(),
            );
            M('email')->add($insert);
            $this->success('回复成功！');
        }
    }
    
    /**
     *  ajax获取州数据 
     */
    public function change_area()
    {
        if(IS_AJAX)
        {
            $country_id = I('post.country_id','0','intval');
            $area_id = I('post.area_id','0'.'intval');
            $area=area_list($country_id);
            $option='<option value="0">==请选择==</option>';
            if($area)
            {
                foreach ($area as $key=>$val)
                {
                    $select = ($val['aid']==$area_id)?'selected="selected"':'';
                    $option.='<option value="'.$val['aid'].'"'.$select.'>'.$val['name'].'</option>';
                }
            }
            
            $this->ajaxReturn(array('status'=>'ok','info'=>$option));
            exit;
        }
    }
    
    /**
     *  ajax获取城市数据 
     */
    public function change_city()
    {
        if(IS_AJAX)
        {
           $pid = I('post.pid','0','intval');
           $city_id = I('post.city_id','0','intval'); 
           $city = city_list($pid);
           $option='<option value="0">==请选择==</option>';
           if($city)
           {
               foreach ($city as $key=>$val)
               {
                   $select = ($val['cid']==$city_id)?'selected="selected"':'';
                   $option.='<option value="'.$val['cid'].'"'.$select.'>'.$val['name'].'</option>';
               } 
           }
           $this->ajaxReturn(array('status'=>'ok','info'=>$option));
           exit;
        }
    }
    
    /**
     *  获取该院校未完成的求助数量
     *  int $college_id
     *  return int
     */
    public function get_help_num($college_id)
    {
        $num=0;
        if($college_id)
        {
            $num = M('college_help')->where(array('college_id'=>$college_id,'is_read'=>0))->count();
        }
        return $num;
    }

    public function commision(){

        $action = I('get.action','','trim');

        if(!IS_POST)
        {
            $college_id = I('get.college_id',0,'intval');
            $id = I('get.id',0,'intval');

            $education_conf=C('Education_TYPE');

            $commison_arr = array();
            $education_rows = M('crm_college_commision')->table('crm_college_commision c')
                ->join('INNER JOIN crm_college_education e on c.college_id=e.college_id and e.education = c.education')
                ->join('LEFT JOIN crm_commision cm on cm.id=c.commision_id')
                ->where(array('e.college_id'=>$college_id))->field('e.*,c.id as iid,c.commision_id,cm.rule_name')->order('c.education asc')->select();

            foreach($education_rows as $r){
                $r['name'] = $education_conf[$r['education']];
                $commison_arr[] = $r;
            }

            $college_name = getField_value('college','cname',array('college_id'=>$college_id));

            $education_rows = M('college_education')->where(array('college_id'=>$college_id))->select();
            $educations = array(
                array(
                    'education'=>0,
                    'name'=>'请选择'
                )
            );

            foreach($education_rows as $r){
                $r['name'] = $education_conf[$r['education']];
                $educations[] = $r;
            }

            $rows = M('commision')->select();
            $commisions = array(
                array(
                    'id'=>0,
                    'rule_name'=>'请选择'
                )
            );
            foreach($rows as $v){
                $commisions[] = array(
                    'id' => $v['id'],
                    'rule_name' => $v['rule_name'],
                );
            }

            $info = array();
            if($id){
                $info = M('college_commision')->where(array('id'=>$id))->find();
            }

            $pay_type = C('pay_type');

            $breadCrumb = array('院校大全'=>U('index'),$college_name =>U('edit',array('college_id'=>$id)),'返佣设置'=>U());
            $this->assign('college_id',$college_id);
            $this->assign('college_name',$college_name);
            $this->assign('commision_arr',$commison_arr);
            $this->assign('commisions',$commisions);
            $this->assign('educations',$educations);
            $this->assign('pay_type',$pay_type);
            $this->assign('info',$info);
            $this->assign('breadCrumb',$breadCrumb);
            $this->adminDisplay();
        }elseif($action == 'del') {
            $id = I('post.data',0,'intval');
            if(empty($id)){
                $this->error('参数不能为空！');
            }

            if(M('college_commision')->where('id='.$id)->delete()){
                $this->success('删除成功！');
            }else{
                $this->error('删除失败！');
            }
        }elseif($action == 'add') {
            $college_id = I('post.college_id',0,'intval');
            $id = I('post.id',0,'intval');
            $education = I('post.education',0,'intval');
            $commision = I('post.commision',0,'intval');

            $update = array(
                'college_id' => $college_id,
                'commision_id'=>$commision,
                'education'=>$education
            );

            if($id){
                $res = M('college_commision')->where('id='.$id)->save($update);
            }else{
                $res = M('college_commision')->add($update);
            }

            if(!$res){
                $this->error('操作失败！');
                exit();
            }

            $this->success('操作成功！');
        }
    }
}