<?php
namespace Admin\Controller;
use Admin\Controller\AdminController;

class CommisionController extends AdminController
{
    /**
     * 当前模块参数
     */
    protected function _infoModule(){

        $college_id = I('param.college_id',0,'intval');


        $college_name = getField_value('college','cname',array('college_id'=>$college_id));

        $this->assign('college_id',$college_id);
        $this->assign('college_name',$college_name);

        return array(
            'info'  => array(
                'name' => '佣金方案',
                'description' => '',
            ),
            'menu' => array(
                array(
                    'name' => '佣金方案',
                    'url' => U('index',array('college_id'=>$college_id)),
                    'icon' => 'list',
                ),
                array(
                    'name' => '添加方案',
                    'url' => U('add',array('college_id'=>$college_id)),
                    'icon' => 'plus',
                ),
            ),
        );
    }

    public function index()
    {
        $college_id = I('param.college_id',0,'intval');

        $where = array('college_id'=>$college_id);

        //URL参数
        $pageMaps = array();

        //查询数据
        $count = D('CollegeCommision')->countList($where);
        $limit = $this->getPageLimit($count,20);
        $list = D('CollegeCommision')->loadList($where,$limit);
        //位置导航
        $breadCrumb = array('佣金方案'=>U('index',array('college_id'=>$college_id)));
        //模板传值
        $this->assign('breadCrumb',$breadCrumb);
        $this->assign('list',$list);
        $this->assign('page',$this->getPageShow($pageMaps));

        $this->adminDisplay();
    }
    
    /**
     *  添加学历 
     */
    public function add()
    {
        $college_id = I('param.college_id',0,'intval');

        if(IS_POST)
        {
            if(D('CollegeCommision')->saveData('add',array('college_id'=>$college_id))){
                $this->success('添加成功！');
            }else{
                $msg = D('CollegeCommision')->getError();
                if(empty($msg)){
                    $this->error('添加失败');
                }else{
                    $this->error($msg);
                }
            }
        }
        else 
        {
            $breadCrumb = array('佣金方案'=>U('index',array('college_id'=>$college_id)),'添加'=>U('add',array('college_id'=>$college_id)));

            $rows = array('A','B','C','D','E');
            $groups = array(
                array(
                    'id' => 0,
                    'rule_name' => '选择规则'
                )
            );

            foreach($rows as $v){
                $groups[] = array(
                    'id' => $v,
                    'rule_name' => $v,
                );
            }

            $education_rows = M('college_education')->where(array('college_id'=>$college_id))->select();
            $educations = array(
                array(
                    'education'=>0,
                    'name'=>'请选择'
                )
            );

            $education_conf=C('Education_TYPE');
            foreach($education_rows as $r){
                $r['name'] = $education_conf[$r['education']];
                $educations[] = $r;
            }

            $this->assign('rules',$groups);
            $this->assign('educations',$educations);
            $this->assign('breadCrumb',$breadCrumb);
            $this->adminDisplay('info');
        } 
    }
    
    /**
     *  修改学历 
     */
    public function edit()
    {
        $college_id = I('param.college_id',0,'intval');

        if(IS_POST)
        {
            if(D('CollegeCommision')->saveData('edit')){
                $this->success('修改成功！');
            }else{
                $msg = D('CollegeCommision')->getError();
                if(empty($msg)){
                    $this->error('修改失败');
                }else{
                    $this->error($msg);
                }
            }
        }
        else 
        {
            $id = I('get.id',0,'intval');
            $info = D('CollegeCommision')->get_info($id);

            $rows = array('A','B','C','D','E');
            $groups = array(
                array(
                    'id' => 0,
                    'rule_name' => '选择规则'
                )
            );
            foreach($rows as $v){
                $groups[] = array(
                    'id' => $v,
                    'rule_name' => $v,
                );
            }

            $education_rows = M('college_education')->where(array('college_id'=>$college_id))->select();
            $educations = array(
                array(
                    'education'=>0,
                    'name'=>'请选择'
                )
            );

            $education_conf=C('Education_TYPE');
            foreach($education_rows as $r){
                $r['name'] = $education_conf[$r['education']];
                $educations[] = $r;
            }

            $this->assign('rules',$groups);
            $this->assign('educations',$educations);
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
            $this->error('参数不能为空1！');
        }
        //获取用户数量
        if(D('CollegeCommision')->del_info($id)){
            $this->success('删除成功！');
        }else{
            $msg = D('CollegeCommision')->getError();
            if(empty($msg)){
                $this->error('删除失败！');
            }else{
                $this->error($msg);
            }
        }
    }
}

?>