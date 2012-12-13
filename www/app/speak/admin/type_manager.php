<?php
defined ( 'IS_ME' ) or exit ();
$speak_cache = $Object_filecache->get ( MANAGER_APP );
if(!empty($_POST['type'])){
	$speak_cache ['type'][] = $_POST['type'];
	$type_id = array_pop(array_keys($speak_cache['type']));
	$speak_cache['type_color'][$type_id] = $_POST['color'];
	$Object_filecache->add(array('type' => $speak_cache ['type'], 'type_color' => $speak_cache ['type_color']),MANAGER_APP);
}
$Object_template->assign ( array ('type' => $speak_cache ['type'], 'type_color' => $speak_cache ['type_color'] ) );
$Object_template->display ( MANAGER_APP . '/admin/type_manager' );
