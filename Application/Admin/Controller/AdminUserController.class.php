<?php
namespace Admin\Controller;
use Admin\Controller\AdminController;

class AdminUserController extends AdminController
{
    /**
     * 当前模块参数
     */
    protected function _infoModule(){
        return array(
            'info'  => array(
                'name' => '用户管理',
                'description' => '管理网站后台管理员',
            ),
            'menu' => array(
                array(
                    'name' => '用户列表',
                    'url' => U('index'),
                    'icon' => 'list',
                ),
                array(
                    'name' => '添加用户',
                    'url' => U('add'),
                    'icon' => 'plus',
                ),
            ),
        );
    }
    
    /* 用户管理  */
    public function index()
    {
        $where = array();
        $keyword = I('request.keyword','','trim');
        if(!empty($keyword))
        {
            $where['_string'] = ' (A.username like "%'.$keyword.'%")  OR ( A.nicename like "%'.$keyword.'%") ';  
        }
        
        $groupId = I('request.group_id','','intval');
        if(!empty($groupId))
        {
            $where['A.group_id'] = $groupId;
        }
        
        //URL参数
        $pageMaps = array();
        $pageMaps['keyword'] = $keyword;
        $pageMaps['group_id'] = $groupId;
        //查询数据
        $count = D('AdminUser')->countList($where);
        $limit = $this->getPageLimit($count,20);
        $list = D('AdminUser')->loadList($where,$limit);
        //位置导航
        $breadCrumb = array('用户列表'=>U());
        //模板传值
        $this->assign('breadCrumb',$breadCrumb);
        $this->assign('list',$list);
        $this->assign('groupList',D('AdminGroup')->loadList());
        $this->assign('page',$this->getPageShow($pageMaps));
        $this->assign('keyword',$keyword);
        $this->assign('groupId',$groupId);
        $this->adminDisplay();
    }
    
    //添加
    public function add()
    {
        if(!IS_POST){
            $breadCrumb = array('用户列表'=>U('index'),'添加'=>U());
            $this->assign('breadCrumb',$breadCrumb);
            $this->assign('name','添加');
            $this->assign('groupList',D('AdminGroup')->loadList());
            $this->adminDisplay('info');
        }else{
            if(D('AdminUser')->saveData('add')){
                $this->success('用户添加成功！');
            }else{
                $msg = D('AdminUser')->getError();
                if(empty($msg)){
                    $this->error('用户添加失败');
                }else{
                    $this->error($msg);
                }
        
            }
        }
    }
    
    //编辑
    public function edit()
    {
        if(!IS_POST){
            $userId = I('get.user_id','','intval');
            if(empty($userId)){
                $this->error('参数不能为空！');
            }
            //获取记录
            $info =  D('AdminUser')->getInfo($userId);
            if(!$info){
                $this->error(D('AdminUser')->getError());
            }
            $breadCrumb = array('用户列表'=>U('index'),'修改'=>U('',array('user_id'=>$userId)));
            $this->assign('breadCrumb',$breadCrumb);
            $this->assign('name','修改');
            $this->assign('groupList',D('AdminGroup')->loadList());
            $this->assign('info',$info);
            $this->adminDisplay('info');
        }else{
            if(D('AdminUser')->saveData('edit')){
                $this->success('用户修改成功！');
            }else{
                $msg = D('AdminUser')->getError();
                if(empty($msg)){
                    $this->error('用户修改失败');
                }else{
                    $this->error($msg);
                }
            }
        }
    }
    
    /**
     * 删除
     */
    public function del(){
        $userId = I('post.data');
        if(empty($userId)){
            $this->error('参数不能为空！');
        }
        if($userId == 1){
            $this->error('保留用户无法删除！');
        }
        //获取用户数量
        if(D('AdminUser')->delData($userId)){
            $this->success('用户删除成功！');
        }else{
            $msg = D('AdminUser')->getError();
            if(empty($msg)){
                $this->error('用户删除失败！');
            }else{
                $this->error($msg);
            }
        }
    }
}


?>