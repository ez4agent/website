<?php
namespace Admin\Controller;
use Common\Controller\BaseController;


class AdminController extends BaseController
{
    public function __construct()
    {
        parent::__construct();
    }
    
    /**
     * 后台控制器初始化
     */
    protected function _initialize()
    {
        // 检测用户登录
        define('ADMIN_ID',$this->isLogin());
        if( !ADMIN_ID && ( MODULE_NAME <> 'Admin' || CONTROLLER_NAME <> 'Login' )){
            $this->redirect('Admin/Login/index');
        }
        
        // 读取后台配置
        $config = load_config(APP_PATH . 'Common/Conf/admin.php');
        C($config);
        //判断模块是否开启
        if (1 != C('APP_STATE') || 1 != C('APP_INSTALL')) {
            $this->error('该应用尚未开启!', false);
        }
        //设置登录用户信息
        $this->loginUserInfo = D('Admin/AdminUser')->getWhereInfo(ADMIN_ID);
        //检测权限
        $this->checkPurview();
        //赋值当前菜单
        if(method_exists($this,'_infoModule')){
            
            $this->assign('infoModule',$this->_infoModule());
        }
    }
    
    /**
     *  检验权限 
     */
    protected function checkPurview()
    {
        
    }
    
    /**
     * 检测用户是否登录
     * @return int 用户IP
     */
    protected function isLogin()
    {
        $user = cookie('admin_user');
        if (empty($user)) {
            return 0;
        } else {
            return cookie('admin_user_sign') == data_auth_sign($user) ? $user['user_id'] : 0;
        }
    }
    
    /**
     * 后台模板显示 调用内置的模板引擎显示方法，
     * @access protected
     * @param string $templateFile 指定要调用的模板文件
     * @return void
     */
    protected function adminDisplay($templateFile='') {
        $common = $this->fetch(APP_PATH.'Admin/View/common.html');
        $tpl = $this->fetch($templateFile);
        echo str_replace('<!--common-->', $tpl, $common);
    }
    
    /**
     * 后台框架显示 调用内置的模板引擎显示方法，
     * @access protected
     * @param string $templateFile 指定要调用的模板文件
     * @return void
     */
    protected function frameDisplay($templateFile='') {
        $common = $this->fetch(APP_PATH.'Admin/View/commonFrame.html');
        $tpl = $this->fetch($templateFile);
        echo str_replace('<!--common-->', $tpl, $common);
    }
}


