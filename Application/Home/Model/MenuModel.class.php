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
            /*
            '0'=>array(
                
                'name'      =>'提醒',
                'controller'=>'Schedule',
                'url'       =>U('Home/Schedule/index'),
                
             ),
            */
            '0'=>array(

                'name'      =>'Q&A',
                'controller'=>'Qa',
                'url'       =>U('Home/Qa/index'),

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
                'url'       => 'javascript:;',
                'child'     =>array(
                    
                     '0'    =>array(
                                'name'=>'基本信息',
                                'url' =>U('Home/Member/view'),  
                            ),
                     '1'    =>array(
                                'name'=>'邀请好友',
                                'url' =>U('Home/Member/invite'),
                            ),
                    /*
                     '2'    =>array(
                                'name'=>'我的账单',
                                'url' =>U('Home/Member/bill'),
                     ),
                    */
                 ),
             ),
            
            '6'=>array(
                'name'      =>'退出',
                'controller'=>'Login',
                'url'       =>U('Home/Login/logout'),
             ),
            '7'=>array(
                'name'      =>'意见反馈',
                'controller'=>'feedback',
                'class' => 'feedback_link',
                'url'       =>U('Home/Letter/sentLetter',array('feedback'=>'true')),
            ),
        );
        
        return $_menu;
    }
}

?>