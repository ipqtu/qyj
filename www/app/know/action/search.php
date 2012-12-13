<?php
defined ( 'IS_ME' ) or exit ();
require_once APP_URL . APP . '/model/question_class.php';
$question_object = new Question ();
$question_object->search ( array ('title' => $_POST ['search'] ), 'ctime' );
$page = (isset ( $_GET ['page'] )) ? abs ( intval ( $_GET ['page'] ) ) : 0;
$all_num = $question_object->get_search_num ();
$star_num = System::get_page_star_num ( $page, 10, $all_num );
$page_html = System::get_page_html ( $page, 10, $all_num, $Object_url->get_url () );
$questions = $question_object->get_serach_value ( $star_num, 10 );
$Object_template->assign ( array ('app_cache' => $app_cache, 'questions' => $questions, 'page_html' => $page_html ) );
$Object_template->display ( APP . '/search' );