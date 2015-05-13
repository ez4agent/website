<?php
namespace Admin\Model;
use Think\Model;

class EducationModel extends Model
{
    public function countList($where = array())
    {
        return M('education')->where($where)->count();
    } 
    
    public function loadList($where = array(), $limit = 0)
    {  
        $data  = M('education')->where($where)->limit($limit)->select();
        if($data)
        {
            foreach($data as $key=>$val)
            {
                $data[$key]['countryname'] = M('country')->where('countryid='.$val['countyid'])->getfield('cname');
            }
        }
        return $data;
    }
    
    public function get_info($id)
    {
        return M('education')->where('id='.$id)->find();
    }
    
    public function del_info($id)
    {
        return M('education')->where('id='.$id)->delete();
    }
    
    /**
     *  更新信息
     *  @param string $type 更新类型
     *  @return bool 更新
     */
    public function saveData($type='add')
    {
        $data = $this->create();
        if(!$data){
            return false;
        }
    
        if($type =='add'){
            return $this->add();
        }
    
        if($type == 'edit'){
            if(empty($data['id'])){
                return false;
            }
            $status = $this->save();
            if($status === false){
                return false;
            }
            return true;
        }
        return false;
    }
}
?>