<?php
/*
 *  留学CRM系统后台系统首页 
 */
namespace Admin\Controller;
use Admin\Controller\AdminController;

class IndexController extends AdminController 
{
    /**
     * 当前模块参数
     */
    protected function _infoModule(){
        return array(
            'menu' => array(
                array(
                    'name' => '管理首页',
                    'url' => U('Index/index'),
                    'icon' => 'dashboard',
                )
            ),
            'info' => array(
                'name' => '管理首页',
                'description' => '站点运行信息',
                'icon' => 'home',
            )
        );
    }
    
    /**
     *  后台首页 
     */
    public function index()
    {
        //设置目录导航
        $breadCrumb = array('首页'=>U('Index/index'));
        $this->assign('breadCrumb',$breadCrumb);
        $this->adminDisplay();
    }
}