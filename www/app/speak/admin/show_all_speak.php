<?php
defined ( 'IS_ME' ) or exit ();
require_once APP_URL . MANAGER_APP . '/model/speak_class.php';
$speak_object = new speak ();
$page = isset ( $_GET ['page'] ) ? $_GET ['page'] : 0;
if(isset($_URL[3])){
	$speak_id = abs(intval($_URL[3]));
	$speak_object->del(array('id'=>$speak_id));
}
$all_speak_num = $speak_object->get_table_all_num ();
$star_num = System::get_page_star_num ( $page, 10, $all_speak_num );
$page_html = System::get_page_html ( $page, 10, $all_speak_num, $Object_url->mk_url ( array ('admin', 'speak', 'show_all_speak' ) ) . '?page=' . $page );
$all_speak = $speak_object->get_all_value ( 'ctime', $star_num, 10 );
$speak_cache = $Object_filecache->get ( MANAGER_APP );
$Object_template->assign ( array ('page' => $page, 'all_speak' => $all_speak, 'page_html' => $page_html,'type'=>$speak_cache['type'] ) );
$Object_template->display ( MANAGER_APP . '/admin/speak', 0 );