<?php 
namespace Common\Controller;
use Think\Controller;


class BaseController extends Controller
{
    public function __construct()
    {
        parent::__construct();
    }
    
    /**
     * 页面不存在
     * @return array 页面信息
     */
    protected function error404()
    {
        $this->error('页面不存在！');
    }

    /**
     * 通讯错误
     */
    protected function errorBlock(){
        $this->error('通讯发生错误，请稍后刷新后尝试！');
    }

    /**
     * 获取分页数量
     * @param int $count 数据总数
     * @param int $listRows 每页数量
     */
    protected function getPageLimit($count,$listRows) {

        $this->pager = new \Think\Page($count,$listRows);
        return $this->pager->firstRow.','.$this->pager->listRows;

    }

    /**
     * 分页显示
     * @param array $map 分页附加参数
     */
    protected function getPageShow($map = '') {
        if(!empty($map)){
            $map = array_filter($map);
            $this->pager->parameter = $map;
        }
        return $this->pager->show();
    }
}




?>