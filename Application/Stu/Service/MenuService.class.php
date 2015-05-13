<?php
namespace Stu\Service;
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
	       'Stu' => array(
	           'name' => '学生',
	           'icon' => 'home',
	           'order' => 1,
	           'menu' => array(
	               array(
	                   'name' => '学生管理',
	                   'url' => U('Stu/Index/index'),
	                   'order' => 0
	               )
	           )
	       ),
	   );	
	}
	


}
