<?php
defined ( 'IS_ME' ) or exit ();
require_once APP_URL . MANAGER_APP . '/model/bk_class.php';
$question_object = new bk ();
$page = isset ( $_GET ['page'] ) ? $_GET ['page'] : 0;
if(isset($_URL[3])){
	$bk_id = abs(intval($_URL[3]));
	$question_object->del(array('id'=>$bk_id));
}
$all_bk_num = $question_object->get_table_all_num ();
$star_num = System::get_page_star_num ( $page, 10, $all_bk_num );
$page_html = System::get_page_html ( $page, 10, $all_bk_num, $Object_url->mk_url ( array ('admin', 'bk', 'show_all_bk' ) ) . '?page=' . $page );
$all_bk = $question_object->get_all_value ( 'ctime', $star_num, 10 );
$Object_template->assign ( array ('page' => $page, 'all_bk' => $all_bk, 'page_html' => $page_html ) );
$Object_template->display ( MANAGER_APP . '/admin/show_all_bk', 0 );