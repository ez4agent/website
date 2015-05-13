<?php
namespace Admin\Controller;
use Admin\Controller\AdminController;

class EducationController extends AdminController
{
    /**
     * 当前模块参数
     */
    protected function _infoModule(){
        return array(
            'info'  => array(
                'name' => '学历管理',
                'description' => '管理所有国家的学历信息',
            ),
            'menu' => array(
                array(
                    'name' => '学历管理',
                    'url' => U('index'),
                    'icon' => 'list',
                ),
                array(
                    'name' => '添加学历',
                    'url' => U('add'),
                    'icon' => 'plus',
                ),
            ),
        );
    }
    
    /**
     *  学历列表 
     */
    public function index()
    {
        $where = array();
        $keyword = I('request.keyword','','trim');
        if(!empty($keyword))
        {
            $where['_string'] = 'ename like "%'.$keyword.'%" ';
        }
        $countryId = I('request.country_id','','intval');
        if(!empty($countryId))
        {
            $where['countyid'] = $countryId;
        }
        //URL参数
        $pageMaps = array();
        $pageMaps['country_id'] = $countryId;
        
        //查询数据
        $count = D('Education')->countList($where);
        $limit = $this->getPageLimit($count,20);
        $list = D('Education')->loadList($where,$limit);
        //位置导航
        $breadCrumb = array('学历列表'=>U());
        //模板传值
        $this->assign('breadCrumb',$breadCrumb);
        $this->assign('list',$list);
        $this->assign('page',$this->getPageShow($pageMaps));
        $map['countryid']=array('NEQ',1);
        $this->assign('country',D('Address')->countryList($map));
        $this->assign('countyid',$countryId);
        $this->assign('keyword',$keyword);
        $this->adminDisplay();
    }
    
    /**
     *  添加学历 
     */
    public function add()
    {
        if(IS_POST)
        {
            if(D('Education')->saveData('add')){
                $this->success('学历添加成功！');
            }else{
                $msg = D('Education')->getError();
                if(empty($msg)){
                    $this->error('学历添加失败');
                }else{
                    $this->error($msg);
                }
            }
        }
        else 
        {
            $breadCrumb = array('学历列表'=>U('index'),'添加'=>U());
            $map['countryid']=array('NEQ',1);
            $this->assign('country',D('Address')->countryList($map));
            $this->assign('breadCrumb',$breadCrumb);
            $this->assign('name','添加');
            $this->adminDisplay('info');
        } 
    }
    
    /**
     *  修改学历 
     */
    public function edit()
    {
        if(IS_POST)
        {
            if(D('Education')->saveData('edit')){
                $this->success('学历修改成功！');
            }else{
                $msg = D('Education')->getError();
                if(empty($msg)){
                    $this->error('学历修改失败');
                }else{
                    $this->error($msg);
                }
            }
        }
        else 
        {
            $id = I('get.id',0,'intval');
            $info = D('Education')->get_info($id);
            $map['countryid']=array('NEQ',1);
            $this->assign('country',D('Address')->countryList($map));
            $this->assign('info',$info);
            $this->adminDisplay('info');
        }
    }
    
    /**
     *  删除学历 
     */
    public function del()
    {
        $id = I('post.data',0,'intval');
        if(empty($id)){
            $this->error('参数不能为空！');
        }
        //获取用户数量
        if(D('Education')->del_info($id)){
            $this->success('学历删除成功！');
        }else{
            $msg = D('Education')->getError();
            if(empty($msg)){
                $this->error('学历删除失败！');
            }else{
                $this->error($msg);
            }
        }
    }
}

?>