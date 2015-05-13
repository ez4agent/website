<?php
/**
 *  系统设置 
 */
namespace Admin\Controller;
use Admin\Controller\AdminController;

class SettingController extends AdminController
{
    /**
     *  当前模块参数 
     */
    protected  function _infoModule()
    {
        return array(
           'info'=>array(
               'name'=>'系统设置',
               'description'=>'设置系统整体功能',
           ), 
           'menu'=>array(
               array(
                   'name'=>'站点信息',
                   'url' =>U('Setting/site'),
                   'icon' => 'exclamation-circle',
               ),
               
               array(
                   'name' => '上传设置',
                   'url' => U('Setting/upload'),
                   'icon' => 'upload',
               ),
               array(
                   'name' => '性能设置',
                   'url' => U('Setting/performance'),
                   'icon' => 'dashboard',
               ),
               array(
                   'name' => '安全设置',
                   'url' => U('Setting/shield'),
                   'icon' => 'shield',
               )
           ), 
        );    
    }
    
    public function site()
    {
        if(!IS_POST){
            $breadCrumb = array('站点信息'=>U());
            $this->assign('breadCrumb',$breadCrumb);
            $this->assign('info',D('Config')->getInfo());
            $this->adminDisplay();
        }else{
        
            if(D('Config')->saveData()){
                $this->success('站点配置成功！');
            }else{
                $this->error('站点配置失败');
            }
        }
    }
    
    public function upload()
    {
        if(!IS_POST){
            $breadCrumb = array('站点信息'=>U());
            $this->assign('breadCrumb',$breadCrumb);
            $this->assign('info',D('Config')->getInfo());
            $this->adminDisplay();
        }else{
            if(D('Config')->saveData()){
                $this->success('上传配置成功！');
            }else{
                $this->error('上传配置失败');
            }
        }
    }
    
    /**
     * 性能设置
     */
    public function performance(){
        $file = APP_PATH . 'Common/Conf/performance.php';
        if(!IS_POST){
            $breadCrumb = array('性能设置'=>U());
            $this->assign('breadCrumb',$breadCrumb);
            $this->assign('info',load_config($file));
            $this->adminDisplay();
        }else{
            if(write_config($file, $_POST)){
                $this->success('性能配置成功！');
            }else{
                $this->error('性能配置失败');
            }
        }
    }
    
    /**
     * 安全设置 
     */
    public function shield()
    {
        $file = APP_PATH . 'Common/Conf/shield.php';
        if(!IS_POST){
            $breadCrumb = array('安全设置'=>U());
            $this->assign('breadCrumb',$breadCrumb);
            $this->assign('info',load_config($file));
            $this->adminDisplay();
        }else{
            if(write_config($file, $_POST)){
                $this->success('安全配置成功！');
            }else{
                $this->error('安全配置失败');
            }
        }
    }
}





?>