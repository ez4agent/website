<?php
/**
 *  地区模型 
 */
namespace Admin\Model;
use Think\Model;

class AddressModel extends Model
{
    /**
     *  获取国家列表
     *  @return array 
     */
    public function countryList($where=array())
    {
        $country = M('country')->where($where)->field('countryid,cname,ename,zimu')->select();
        return $country;
    }
    
    /**
     *  获取国家信息 
     */
    public function getCountryInfo($countryid)
    {
        return M('country')->where('countryid='.$countryid)->find();
    }
    
    /**
     * 更新信息
     * @param string $type 更新类型
     * @return bool 更新状态 
     */
    public function saveCountryData($type='add')
    {
        $data = M('country')->create();
        if(!$data){
            return false;
        }
        if($type == 'add'){
            return  M('country')->add();
        }
        if($type == 'edit'){
            if(empty($data['countryid'])){
                return false;
            }
            $status =  M('country')->save();
            if($status === false){
                return false;
            }
            return true;
        }
        return false;
    }
    
    public function getAreaList($where = array(), $limit = 0 ,$order=" aid ASC")
    {
        $pageList = M('area')->where($where)->limit($limit)->order($order)->select();
        if(!empty($pageList)){
            foreach ($pageList as $key=>$value) {
                
               $pageList[$key]['countryname'] = M('country')->where('countryid='.$value['countryid'])->getfield('cname');
            }
        }
        return $pageList;
    }
    
    public function getCityList($where = array(),$limit=0,$order=" cid ASC")
    {
        $pageList = M('city')->where($where)->limit($limit)->order($order)->select();
        if(!empty($pageList)){
            foreach ($pageList as $key=>$value) {
                $pageList[$key]['countryname'] = M('country')->where('countryid='.$value['countryid'])->getfield('cname');
                $pageList[$key]['areaname'] = M('area')->where('aid='.$value['area_id'])->getfield('cname');
            }
        }
        return $pageList;
    }
    
    /**
     *  获取信息
     *  @ return array 
     */
    public function loadList_area($where = array(),$limit='',$classId=0)
    {
        import("Common.Util.Category");
        $data = $this->getAreaList($where,$limit);
        $cat = new \Common\Util\Category(array('aid', 'pid', 'cname'));
        $data=$cat->getTree($data, intval($classId));
        return $data;
    }
    
    /**
     *  获取信息
     *  @param int 
     *  @return array 
     */
    public function AreaInfo($aid)
    {
        return M('area')->where('aid='.$aid)->find();
    }
    
    public function CityInfo($cid)
    {
        return M('city')->where('cid='.$cid)->find();
    }
    
    
    /**
     *  更新信息
     *  @param string $type
     *  @return bool 更新状态 
     */
    public function saveAreaData($type='add')
    {
        $data = M('area')->create();
        if(!$data){
            return false;
        }
        
        if($type == 'add'){
            return  M('area')->add($data);
        }
        
        if($type == 'edit'){
            if(empty($data['aid'])){
                return false;
            }

            $status =  M('area')->save($data);
            if($status === false){
                return false;
            }
            return true;
        }
        return false;
    }
    
    /**
     *  城市信息更新 
     */
    public function saveCityData($type="add")
    {
        $data = M('city')->create();
        if(!$data){
            return false;
        }
        
        if($type == 'add'){
            return  M('city')->add($data);
        }
        
        if($type == 'edit'){
            if(empty($data['cid'])){
                return false;
            }
        
            $status =  M('city')->save($data);
            if($status === false){
                return false;
            }
            return true;
        }
        return false;
    } 
    
    public function countList($table,$where= array())
    {
        return M($table)->where($where)->count();
    }
    
    
    public function getAareaList($countryid)
    {
        $map = array('countryid'=>$countryid);
        return M('area')->where($map)->select();
    }
}


?>