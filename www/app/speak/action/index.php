<?php
defined ( 'IS_ME' ) or exit ();
$page = (isset ( $_GET ['page'] )) ? abs ( intval ( $_GET ['page'] ) ) : 0;
$all_num = $speak_object->get_table_all_num ();
$page_html = System::get_page_html ( $page, 20, $all_num, $Object_url->mk_url ( array ('speak', 'index' ) ) . '?page=' . $page );
$start_num = System::get_page_star_num ( $page, 20, $all_num );
$all_speak = $speak_object->get_all_value ( 'ctime', $start_num, 20 );
$Object_template->assign ( array ('all_speak' => $all_speak,'page_html'=>$page_html,'type'=>$app_cache['type']) );
$Object_template->display ( APP . '/index' );