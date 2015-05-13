<?php
/**
 *  CRM系统首页 
 */
namespace Home\Controller;
use Think\Controller;


class IndexController extends Controller
{
    //关于我们
    public function aboutUS(){
        $this->display();
    }
    
    //联系我们
    public function contacts()
    {
        $this->display();
    }
}