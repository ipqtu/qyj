<?php
defined ( 'IS_ME' ) or exit ();
$manager_app_cache = $Object_filecache->get ( MANAGER_APP );
if(isset($_POST['type_id'])){
	$type_id = abs(intval($_POST['type_id']));
	$manager_app_cache['type'][$type_id] = $_POST['new_type'];
	$Object_filecache->add($manager_app_cache,MANAGER_APP);
}elseif(isset($_POST['type'])){
	$manager_app_cache['type'][] = $_POST['type'];
	$Object_filecache->add($manager_app_cache,MANAGER_APP);
}elseif(isset($_GET['del'])){
	$type_id = abs(intval($_GET['del']));
	unset($manager_app_cache['type'][$type_id]);
	$Object_filecache->add($manager_app_cache,MANAGER_APP);
}

$Object_template->assign ( array ('type' => $manager_app_cache ['type'] ) );
$Object_template->display ( MANAGER_APP . '/admin/type' );