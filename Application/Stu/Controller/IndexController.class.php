<?php
/**
 *  院校大全 
 */

namespace Stu\Controller;
use Admin\Controller\AdminController;

class IndexController extends AdminController 
{
    /**
     * 当前模块参数
     */
    protected function _infoModule(){
        return array(
            
			'info'  => array(
                'name' => '学生管理',
                'description' => '管理系统所有学生信息',
            ),
			
            'menu' => array(
            	array(
            		'name'=>'学生列表',
            		'url'=>U('index'),
            		'icon'=>'list',
                ),
            ),
        );
    }
    
	/**
	 *	学生管理
	 */
	 public function index()
	 {
		 $where = array();
		 $member_id = I('param.member_id',0,'intval');
		 if(!empty($member_id))
		 {
		    $where['member_id'] = $member_id;
		 }
		 
		 $keyword = I('param.keyword','','trim');
		 
		 if(!empty($keyword))
		 {
		     $where['_string'] = ' (stu_name like "%'.$keyword.'%") ';
		 }
		 
		 //URL参数
		 $pageMaps = array();
		 $pageMaps['member_id'] = $member_id;
		 $pageMaps['keyword'] = $keyword;
		 //查询数据
		 $count = D('Stu')->get_count($where);
		 $limit = $this->getPageLimit($count, 20);
		  $list = D('Stu')->get_stu_list($where,$limit);
		 $this->assign('list',$list);
		 //位置导航
		 $breadCrumb = array('学生列表'=>U());
		 $this->assign('page',$this->getPageShow($pageMaps));
		 //模板传值
		 $this->assign('breadCrumb',$breadCrumb);
		 $this->assign('keyword',$keyword);
		 $this->adminDisplay();
	 }
	 
	 /**
	  *    学员查看 
	  */
	 public function view()
	 {
	     $stu_id = I('get.stu_id',0,'intval');
	     $info = D('Stu')->get_stu_info($stu_id);
	     $this->assign(info,$info);
	     $breadCrumb = array('学生管理'=>U('index'),'查看'=>U());
	     $this->assign('breadCrumb',$breadCrumb);
	     $this->adminDisplay();
	 }

}