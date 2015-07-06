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

    public function guide()
    {

        $class = isset($_GET['class']) ? $_GET['class'] : 's1';

        if(in_array($class,array('s1','s2'))){
            $video_url = array(
                's1' => 'http://player.youku.com/player.php/sid/XMTI2OTExNjQ4NA==/v.swf',
                's2' => 'http://player.youku.com/player.php/sid/XMTI2OTExNTcwNA==/v.swf'
            );

            $this->assign('video_url',$video_url[$class]);
        }

        $this->assign('class',$class);
        $this->display();
    }
}