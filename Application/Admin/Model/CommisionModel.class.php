<?php
namespace Admin\Model;
use Think\Model;

class CommisoinModel extends Model
{

    public function countList($where = array())
    {
        return M('commision')->where($where)->count();
    }
    
    public function loadList($where = array(), $limit = 0)
    {  
        $data  = M('commisoin')->where($where)->limit($limit)->select();
        return $data;
    }
    
    public function get_info($id)
    {
        return M('commisoin')->where('id='.$id)->find();
    }
    
    public function del_info($id)
    {
        return M('commisoin')->where('id='.$id)->delete();
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