<?php

/**
 *  栏目模型 
 */

namespace Home\Model;
use Think\Model;

class MenuModel extends Model
{
    /**
     *  获取栏目
     */
    public function get_menu()
    {
        $_menu = array(
            
            '0'=>array(
                
                'name'      =>'提醒',
                'controller'=>'Schedule',
                'url'       =>U('Home/Schedule/index'),
                
             ),
            '1'=>array(
               'name'       =>'学生',
               'controller' =>'Student',
               'url'        =>U('Home/Student/index'), 
             ),
            '2'=>array(
                'name'      =>'院校',
                'controller'=>'School',
                'url'       =>U('Home/School/index'),
                
             ),
            
            '4'=>array(
                'name'      =>'邮件',
                'controller'=>'Letter',
                'url'       =>U('Home/Letter/index'),
             ),
            
             
            '5'=>array(
                'name'      =>'管理',
                'controller'=>'Member',
                'url'       =>U('Home/Member/view'),
               /* 'child'     =>array(
                    
                     '0'    =>array(
                                'name'=>'用户信息',
                                'url' =>U('Home/Member/view'),  
                            ),
                     '1'    =>array(
                                'name'=>'账户记录',
                                'url' =>U('Home/Member/record'),                      
                            ),
                     '2'    =>array(
                                'name'=>'子账户管理',
                                'url' =>U('Home/Member/sub_account'),
                            ),
                     '3'    =>array(
                                'name'=>'数据统计',
                                'url' =>'',
                            ),
                 ),*/
             ),
            
            '6'=>array(
                'name'      =>'退出',
                'controller'=>'Login',
                'url'       =>U('Home/Login/logout'),
             ),
        );
        
        return $_menu;
    }
}

?>