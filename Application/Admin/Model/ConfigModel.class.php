<?php
/**
 *  系统配置 
 */

namespace Admin\Model;
use Think\Model;

class ConfigModel extends Model
{
    /**
     *  获取信息
     *  @return array 网站配置 
     */
    public function getInfo()
    {
        $list =$this->select();
        $config = array();
        foreach($list as $key=>$value)
        {
            $config[$value['name']] = $value['data'];
        }
        return $config;
    }
    
    /**
     *  更新信息
     *  @param int $siteId 站点配置ID
     *  @return bool 更新状态 
     */
    public function saveData()
    {
        $data = I('post.');
        if(empty($data)){
            $this->error = '数据创建失败！';
            return false;
        }
        foreach ($data as $key => $value) {
            $currentData = array();
            $currentData['data'] = $value;
            $status = $this->data($currentData)->where('name = "'.$key.'"')->save();
            if($status === false){
                return false;
            }
        }
        return true;
    }
}



?>