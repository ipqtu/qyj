<?php
defined ( 'IS_ME' ) or exit ();
($Object_user->is_login ()) || Display::display_nologin ();
require_once APP_URL . APP . '/model/question_class.php';
$question_object = new Question ();
$page = isset ( $_GET ['page'] ) ? intval ( $_GET ['page'] ) : 0;
$all_num = $question_object->get_table_all_num ( array ('author_id' => $_USER ['user_id'] ) );
$star_num = System::get_page_star_num ( $page, 10, $all_num );
$page_html = System::get_page_html ( $page, 10, $all_num, $Object_url->get_url () );
$user_questions = $question_object->get_value_by_where ( array ('author_id' => $_USER ['user_id'] ), 'ctime', $star_num, 10 );
$Object_template->assign ( array ('app_cache' => $app_cache,'use_quetions' => $user_questions,'page_html'=>$page_html ) );
$Object_template->display ( APP . '/my_question' );