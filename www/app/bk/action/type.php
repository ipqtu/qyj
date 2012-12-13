<?php
defined ( 'IS_ME' ) or exit ();
$type_id = isset ( $_URL [2] ) ? abs ( intval ( $_URL [2] ) ) : array_pop ( array_keys ( $app_cache ['type'] ) );
isset ( $app_cache ['type'] [$type_id] ) || Display::display_dialog ( '你访问的信息不存在' );
require_once APP_URL . APP . '/model/bk_class.php';
$bk_object = new Bk ();

$page = (isset ( $_GET ['page'] )) ? abs ( intval ( $_GET ['page'] ) ) : 0;
$all_num = $bk_object->get_table_all_num ( array ('type_id' => $type_id ) );
$star_num = System::get_page_star_num ( $page, 10, $all_num );
$page_html = System::get_page_html ( $page, 10, $all_num, $Object_url->get_url () );

$bks = $bk_object->get_value_by_where ( array ('type_id' => $type_id ), 'ctime', $star_num, 10 );
foreach ($bks as $bk){
	$bk->content = System::sub_str(strip_tags($bk->content),100);
}
$Object_template->assign ( array ( 'type_id' => $type_id, 'bks' => $bks, 'page_html' => $page_html ) );
$Object_template->display ( APP . '/type' );