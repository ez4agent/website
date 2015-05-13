<?php
//地区设置
namespace Admin\Controller;
use Admin\Controller\AdminController;

class SetAddressController extends AdminController
{
    /**
     * 当前模块参数
     */
    protected function _infoModule(){
        return array(
            'info'  => array(
                'name' => '地区设置',
                'description' => '管理CRM系统地区（国家、省\州、城市）',
            ),
            'menu' => array(
                array(
                  'name'=>'国家列表',
                  'url' =>U('country'),
                  'icon'=>'list',  
                ),
                array(
                    'name' => '添加国家',
                    'url' => U('addcountry'),
                    'icon' => 'plus',
                ),
                array(
                    'name' => '州/省列表',
                    'url' => U('address'),
                    'icon' => 'list',
                ),
                array(
                    'name' => '添加州/省',
                    'url' => U('add'),
                    'icon' => 'plus',
                ),
                
                array(
                    'name' => '城市列表',
                    'url' => U('city'),
                    'icon' => 'list',
                ),
                array(
                    'name' => '添加城市',
                    'url' => U('addcity'),
                    'icon' => 'plus',
                ),
            ),
        );
    }

    /**
     *  国家列表 
     */
    public function country()
    {
        //位置导航
        $breadCrumb = array('国家列表'=>U());
         //模板传值
        $this->assign('countryList',D('Address')->countryList());
        $this->assign('breadCrumb',$breadCrumb);
        $this->adminDisplay();
    }
    
    /**
     * 添加国家
     */
    public function addcountry()
    {
        if(!IS_POST){
            $breadCrumb = array('国家列表'=>U('index'),'添加'=>U());
            $this->assign('breadCrumb',$breadCrumb);
            $this->assign('name','添加');
            $this->adminDisplay('countryinfo');
        }else{
            if(D('Address')->saveCountryData('add')){
                $this->success('国家添加成功！');
            }else{
                $msg = D('Address')->getError();
                if(empty($msg)){
                    $this->error('国家添加失败！');
                }else{
                    $this->error($msg);
                }
            
            }  
        }
    }
    
    /**
     * 修改国家
     */
    public function editcountry()
    {
        if(!IS_POST){
            $countryid = I('get.countryid','0','intval');
            $breadCrumb = array('国家列表'=>U('index'),'修改'=>U());
            $this->assign('breadCrumb',$breadCrumb);
            $this->assign('name','修改');
            $this->assign('info',D('Address')->getCountryInfo($countryid));
            $this->adminDisplay('countryinfo');
        }else{
            if(D('Address')->saveCountryData('edit')){
                $this->success('国家修改成功！');
            }else{
                $msg = D('Address')->getError();
                if(empty($msg)){
                    $this->error('国家修改失败！');
                }else{
                    $this->error($msg);
                }
            
            }
        }
    }
    
    /**
     *  地区列表 （省\市，城市2级）
     */
    public function address()
    {
        $where = array();
        $keyword = I('request.keyword','','trim');
        if(!empty($keyword))
        {
            $where['_string'] = ' (cname like "%'.$keyword.'%")  OR ( ename like "%'.$keyword.'%") ';
        }
        
        $countryid = I('request.countryid','','intval');
        if(!empty($countryid))
        {
            $where['countryid'] = $countryid;
        }
        
        //URL参数
        $pageMaps = array();
        $pageMaps['keyword'] = $keyword;
        $pageMaps['countryid'] =  $countryid;
        
        //查询数据
        $count = D('Address')->countList('area',$where);
        $limit = $this->getPageLimit($count,30);
        
        $breadCrumb = array('地区列表' => U());
        $this->assign('breadCrumb', $breadCrumb);
        $this->assign('country',D('Address')->countryList());
        $this->assign('list', D('Address')->getAreaList($where,$limit));
        $this->assign('page',$this->getPageShow($pageMaps));
        $this->assign('keyword',$keyword);
        $this->assign('countryid',$countryid);
        $this->adminDisplay();
    }
    
    /**
     *  添加地区 (省\市，城市2级)
     */
    public function add()
    {
        if(!IS_POST){
            
            $breadCrumb = array('地区列表'=>U('address'),'添加'=>U());
            $this->assign('breadCrumb',$breadCrumb);
            $this->assign('name','添加');
            $this->assign('country',D('Address')->countryList());
            $this->adminDisplay('addressinfo');
        
        }else{
            
            if(D('Address')->saveAreaData('add')){
                $this->success('地区添加成功！');
            }else{
                $msg = D('Address')->getError();
                if(empty($msg)){
                    $this->error('地区添加成功！');
                }else{
                    $this->error($msg);
                }
            
            }
        }
    }
    
    //更新地区信息
    public function edit()
    {
        if(!IS_POST){
            $aid = I('get.aid','0','intval');
            $info = D('Address')->AreaInfo($aid);
            $breadCrumb = array('地区列表'=>U('address'),'修改'=>U());
            $this->assign('breadCrumb',$breadCrumb);
            $this->assign('name','修改');
            $this->assign('country',D('Address')->countryList());
            $this->assign('info',$info);
            $this->assign('countryid',$info['countryid']);
            $this->adminDisplay('addressinfo');
        }else{
            if(D('Address')->saveAreaData('edit')){
                $this->success('地区修改成功！');
            }else{
                $msg = D('Address')->getError();
                if(empty($msg)){
                    $this->error('地区修改失败！');
                }else{
                    $this->error($msg);
                }
            
            }
        }
    }
    
    //城市
    public function city()
    {
        $where = array();
        $keyword = I('request.keyword','','trim');
        if(!empty($keyword))
        {
            $where['_string'] = ' (cname like "%'.$keyword.'%")  OR ( ename like "%'.$keyword.'%") ';
        }
        
        $countryid = I('request.countryid','','intval');
        if(!empty($countryid))
        {
            $where['countryid'] = $countryid;
        }
        
        $areaid = I('request.area_id','','intval');
        
        if(!empty($areaid))
        {
            $where['area_id'] = $areaid;
            $countryid = M('area')->where('aid='.$areaid)->getfield('countryid');
        }
        
        //URL参数
        $pageMaps = array();
        $pageMaps['keyword'] = $keyword;
        $pageMaps['countryid'] =  $countryid;
        $pageMaps['area_id'] = $areaid;
        
        //查询数据
        $count = D('Address')->countList('city',$where);
        $limit = $this->getPageLimit($count,30);
        
        $breadCrumb = array('地区列表' => U());
        $this->assign('breadCrumb', $breadCrumb);
        $this->assign('country',D('Address')->countryList());
        $this->assign('list', D('Address')->getCityList($where,$limit));
        $this->assign('page',$this->getPageShow($pageMaps));
        $this->assign('keyword',$keyword);
        $this->assign('countryid',$countryid);
        $this->adminDisplay();
    }
    
    //添加城市
    public function addcity()
    {
        if(!IS_POST){
        
            $breadCrumb = array('城市列表'=>U('address'),'添加'=>U());
            $this->assign('breadCrumb',$breadCrumb);
            $this->assign('name','添加');
            $this->assign('country',D('Address')->countryList());
            $this->adminDisplay('cityinfo');
        
        }else{
        
            if(D('Address')->saveCityData('add')){
                $this->success('城市添加成功！');
            }else{
                $msg = D('Address')->getError();
                if(empty($msg)){
                    $this->error('城市添加失败！');
                }else{
                    $this->error($msg);
                }
        
            }
        } 
    }
    
    //修改城市信息
    public function cityedit()
    {
        if(!IS_POST){
            $cid = I('get.cid','0','intval');
            $info = D('Address')->CityInfo($cid);
            $breadCrumb = array('城市列表'=>U('address'),'修改'=>U());
            $this->assign('breadCrumb',$breadCrumb);
            $this->assign('name','修改');
            $this->assign('country',D('Address')->countryList());
            $this->assign('info',$info);
            $this->assign('countryid',$info['countryid']);
            $this->adminDisplay('cityinfo');
        }else{
            if(D('Address')->saveCityData('edit')){
                $this->success('城市修改成功！');
            }else{
                $msg = D('Address')->getError();
                if(empty($msg)){
                    $this->error('城市修改失败！');
                }else{
                    $this->error($msg);
                }
        
            }
        }
    }
    
    //根据国家ID 获取省\州
    public function getArea()
    {
        if(IS_AJAX)
        {
            $countryid = I('post.country_id','0','intval');
            $area_id = I('post.area_id','0','intval');
            $areaList = D('Address')->getAareaList($countryid);
            $str="<option value='0'>===请选择===</option>";
            if(!empty($areaList))
            {
                foreach ($areaList as $key=>$val)
                {
                    if($area_id==$val['aid']){
                        $select = ' selected="selected"';
                    }else{
                        $select='';
                    }
                    
                    $str.='<option value="'.$val['aid'].'"'.$select.'>'.$val['cname'].'('.$val['ename'].')</option>';
                }
            }
            echo $this->ajaxReturn(array('status'=>'ok','info'=>$str));
            exit;
        }
    }
}
?>