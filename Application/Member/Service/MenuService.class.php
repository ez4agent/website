<?php
namespace Member\Service;
/**
 * 后台菜单接口
 */
class MenuService{
	/**
	 * 获取菜单结构
	 */
	public function getAdminMenu()
	{
	   return array(
	       'Member' => array(
	           'name' => '会员',
	           'icon' => 'home',
	           'order' => 2,
	           'menu' => array(
	               array(
	                   'name' => '会员管理',
	                   'url' => U('Member/Index/index'),
	                   'order' => 0
	               )
	           )
	       ),
	   );	
	}
	


}
