<?php
/**
 *  CRM系统-地区联动
 */
namespace Home\Controller;
use Think\Controller;

class AreaController extends Controller
{
    /**
     *  ajax 获取省/州 
     */
    public function get_area()
    {
        if(IS_AJAX)
        {
            $country_id = I('post.country_id',0,'intval');
            $select_area_id=I('post.select_area_id',0,'intval');
            $str="<option value='0'>==请选择省(州)==</option>";
            if($country_id)
            {
                $area_list = area_list($country_id);
                if($area_list)
                {
                    foreach ($area_list as $val)
                    {
                        if($val['aid']==$select_area_id){
                            $select="selected";
                        }else{
                            $select='';
                        }
                        $str.="<option value='".$val['aid']."'".$select.">".$val['name']."</option>";
                    }
                }
            }
            
            $this->ajaxReturn(array('status'=>'ok','info'=>$str));
            exit();
        }
    }
    
    /**
     *  ajax 获取城市 
     */
    public function get_city()
    {
        if(IS_AJAX)
        {
            $area_id = I('post.area_id','0'.'intval');
            $city_select_id =I('post.city_select_id','0','intval');
            $str="<option value='0'>==选择城市==</option>";
            if($area_id)
            {
                $city_list = city_list($area_id,2);
                if($city_list)
                {
                    foreach ($city_list as $val)
                    {
                        if($val['cid']==$city_select_id){
                            $select="selected";
                        }else{
                            $select='';
                        }
                        $str.="<option value='".$val['cid']."'".$select.">".$val['name']."</option>";
                    }
                }
            }
            
            $this->ajaxReturn(array('status'=>'ok','info'=>$str));
            exit();
        }
    }
}


?>