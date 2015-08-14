<?php
namespace Admin\Service;
/**
 * 后台菜单接口
 */
class MenuService{
	/**
	 * 获取菜单结构
	 */
	public function getAdminMenu(){
		return array(
            'index' => array(
                'name' => '首页',
                'icon' => 'home',
                'order' => 0,
                'menu' => array(
                    array(
                        'name' => '管理首页',
                        'url' => U('Admin/Index/index'),
                        'order' => 0
                    )
                )
            ),
            'system' => array(
                'name' => '系统',
                'icon' => 'bars',
                'order' => 9,
                'menu' => array(
                    array(
                        'name' =>'地区设置',
                        'url'  =>U('Admin/SetAddress/address'),
                        'order'=>1,
                        
                    ),
                    
                    array(
                        'name' => '用户管理',
                        'url' => U('Admin/AdminUser/index'),
                        'order' => 7,  
                    ),
                    array(
                        'name' => '用户组管理',
                        'url' => U('Admin/AdminUserGroup/index'),
                        'order' => 8,
                        
                    ),
                    
                    array(
                        'name'=>'学历设置',
                        'url'=>U('Admin/Education/index'),
                        'order'=>10,
                       
                    )
                )
            ),
        );
	}
	


}
