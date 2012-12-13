<?php
defined ( 'IS_ME' ) or exit ();
$index_nav = array ('校园生活', '大学学习', '浪漫爱情', '周边美食', '疯狂购物', '自在旅行' );
$manager_app_cache = $Object_filecache->get ( MANAGER_APP );
if (isset ( $_POST ['type_id'] )) {
	$index_nav_id = abs ( intval ( $_POST ['index_nav_id'] ) );
	$type_id = abs ( intval ( $_POST ['type_id'] ) );
	$manager_app_cache ['index_nav_type'] [$index_nav_id] = $type_id;
	$Object_filecache->add ( $manager_app_cache ,MANAGER_APP);
}
$Object_template->assign ( array ('index_nav' => $index_nav, 'index_nav_type' => $manager_app_cache ['index_nav_type'], 'type' => $manager_app_cache ['type'] ) );
$Object_template->display ( MANAGER_APP . '/admin/index' );