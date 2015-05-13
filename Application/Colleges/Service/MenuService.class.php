<?php
namespace Colleges\Service;
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
	       'Content' => array(
	           'name' => '院校',
	           'icon' => 'home',
	           'order' => 1,
	           'menu' => array(
	               array(
	                   'name' => '院校大全',
	                   'url' => U('Colleges/Index/index'),
	                   'order' => 0
	               )
	           )
	       ),
	   );	
	}
	


}
