<?php
/**
 * 会员管理
 */
namespace Member\Controller;
use Admin\Controller\AdminController;

class IndexController extends AdminController
{
    
    /**
     * 当前模块参数
     */
    protected function _infoModule(){
        return array(
            'info'  => array(
                'name' => '会员管理',
                'description' => '管理系统所有会员',
            ),
            'menu' => array(
                array(
                    'name' => '会员等级',
                    'url' => U('grade'),
                    'icon' => 'list',
                ),            	
            	array(
            		'name'=>'添加等级',
            		'url'=>U('addgrade'),
            		'icon' => 'plus',
                ),
                array(
                    'name' => '会员列表',
                    'url' => U('index'),
                    'icon' => 'list',
                ), 
            ),
        );
    }
    
    //会员管理
    public function index()
    {
        $where=array('mc.pid'=>0);
        
        //关键字
        $keyword = I('request.keyword','','trim');
        if(!empty($keyword))
        {
            $where['_string'] = ' (m.username like "%'.$keyword.'%") ';
        }
        
        //等级
        $grade = I('request.grade','0','intval');
        if(!empty($grade))
        {
            $where['m.grade']=$grade;
        }
        //URL参数
        $pageMaps = array();
        $pageMaps['keyword'] = $keyword;
        $pageMaps['grade'] = $grade;
        
        //查询数据
        $count = D('Member')->get_count($where);
        $limit = $this->getPageLimit($count,20);
        $list = D('Member')->get_list($where,$limit);
        $this->assign('list',$list);
        
        //会员等级
        $this->assign('grade_list',D('Member')->getMember_grade(array('is_used'=>1)));
        
        //位置导航
        $breadCrumb = array('会员列表'=>U());
        //模板传值
        $this->assign('page',$this->getPageShow($pageMaps));
        $this->assign('breadCrumb',$breadCrumb);
        $this->assign('keyword',$keyword);
        $this->assign('grade',$grade);
        $this->adminDisplay();
    }
    
    //会员等级
    public function grade()
    {
    	$list = D('Member')->getMember_grade();
    	//位置导航
    	$breadCrumb = array('会员等级列表'=>U());
    	$this->assign('breadCrumb',$breadCrumb);
    	$this->assign('list',$list);
    	$this->adminDisplay();
    }
    
    //添加会员等级
    public function addgrade()
    {
    	if(!IS_POST)
    	{
    		//位置导航
    		$breadCrumb = array('会员等级列表'=>U('grade'),'添加'=>U());
    		$this->assign('breadCrumb',$breadCrumb);
    		//模板传值
    		$this->assign('name','添加');
    		$this->adminDisplay('gradeinfo');
    	}else{
    		if(D('Member')->save_Grade($_POST,'add')){
    			$this->success('会员等级添加成功！');
    		}else{
    			$msg = D('Member')->getError();
    			if(empty($msg)){
    				$this->error('会员等级添加失败');
    			}else{
    				$this->error($msg);
    			}
    		}
    	}
    }
    
    //修改会员等级
    public function editgrade()
    {
    	if(!IS_POST)
    	{
    		$id = I('get.id','0','intval');
    		
    		$info = M('member_grade')->where('id='.$id)->find();
    		//位置导航
    		$breadCrumb = array('会员等级列表'=>U('grade'),'修改'=>U());
    		$this->assign('breadCrumb',$breadCrumb);
    		//模板传值
    		$this->assign('name','修改');
    		$this->assign('info',$info);
    		$this->adminDisplay('gradeinfo');
    	}
    	else
    	{
    		if(D('Member')->save_Grade($_POST,'edit')){
    			$this->success('会员等级修改成功！');
    		}else{
    			$msg = D('Member')->getError();
    			if(empty($msg)){
    				$this->error('会员等级修改失败');
    			}else{
    				$this->error($msg);
    			}
    		}
    	} 	
    }
    
    //删除会员
    public function delgrade()
    {
    	$id = I('post.data',0,'intval');
    	$info = M('member_grade')->where('id='.$id)->find();
    	if(!$info){
    		$this->error('该数据不存在！');
    	}
    	$count = M('member')->where('grade='.$id)->count();
    	if($count){
    		$this->error('该会员等级下面已经有会员,不能删除！');
    	}
    	
    	//获取用户数量
    	if(D('Member')->delGradeData($id)){
    		$this->success('会员等级删除成功！');
    	}else{
    		$msg = D('Member')->getError();
    		if(empty($msg)){
    			$this->error('会员等级删除失败！');
    		}else{
    			$this->error($msg);
    		}
    	}
    }
    
    //会员修改
    public function edit()
    {
        if(IS_POST)
        {
           $update = array(
               'grade'=>I('post.grade',0,'intval'),
               'is_open'=>I('post.is_open',0,'intval'),
           );
           
           $pwd = I('post.pwd','','trim');
           if($pwd)
           {
               $update['pwd'] = md5($pwd);
           }

           if(M('member')->where('member_id='.intval($_POST['member_id']))->save($update))
           {
               $this->success('会员信息修改成功！');
           }
           else 
           {
               $msg = M('member')->getError();
               if(empty($msg)){
                   $this->error('会员信息修改失败！');
               }else{
                   $this->error($msg);
               }
           }
            
        }
        else 
        {
            $member_id = I('get.member_id','0','intval');
            $_info = M('member')->where('member_id='.$member_id)
                                ->field('member_id,username,is_open,grade')->find();
            //位置导航
            $breadCrumb = array('会员列表'=>U('index'),'修改会员信息'=>U());
            $this->assign('grade',D('Member')->getMember_grade(array('is_used'=>1)));
            $this->assign('breadCrumb',$breadCrumb);
            $this->assign('name','修改');
            $this->assign('info',$_info);
            $this->adminDisplay('editinfo');
        }
    }
    
    //会员基本信息
    public function view()
    {
        $member_id = I('get.member_id',0,'intval');
        $info = D('Member')->get_member_info($member_id);
        $breadCrumb = array('会员列表'=>U('index'),'查看会员信息'=>U());
        $this->assign('breadCrumb',$breadCrumb);
        $this->assign('info',$info);
        $this->adminDisplay('memberinfo');
    }
    
    //删除会员
    public function del()
    {
        $member_id = I('post.data','0','intval');
        $_info =D('Member')->get_member_info($member_id);
        if(!$_info){
            $this->error('该数据不存在！');
        }
        //获取用户数量
        if(D('Member')->delData($member_id)){
            $this->success('会员删除成功！');
        }else{
            $msg = D('Member')->getError();
            if(empty($msg)){
                $this->error('会员删除失败！');
            }else{
                $this->error($msg);
            }
        }
    }
    
    //批量操作
    public function batchAction()
    {
        $ids=I('post.ids','','trim');
        $type= I('post.type','','intval');
        
        $ids_str = implode(',', $ids);
        if($type==1)
        {
           $result = M('member')->where('member_id in('.$ids_str.')')
                       ->setfield(array('is_open'=>0));
           if($result){
               $this->success('会员锁定成功！');
           }else{
               $msg = M('Member')->getError();
               if(empty($msg)){
                   $this->error('会员锁定失败！');
               }else{
                   $this->error($msg);
               }
           }
        }
        elseif($type==2)
        {
            $result = M('member')->where('member_id in('.$ids_str.')')
                                 ->setfield(array('is_open'=>1));
            if($result){
                $this->success('会员解锁成功！');
            }else{
                $msg = M('Member')->getError();
                if(empty($msg)){
                    $this->error('会员解锁失败！');
                }else{
                    $this->error($msg);
                }
            }
        } 
    }
}