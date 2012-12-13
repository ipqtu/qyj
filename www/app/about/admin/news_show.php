<?php
defined ( 'IS_ME' ) or exit ();
require_once APP_URL . MANAGER_APP . '/model/news_class.php';
$question_object = new News ();
$page = isset ( $_GET ['page'] ) ? $_GET ['page'] : 0;
if (isset ( $_URL [3] )) {
	$about_id = abs ( intval ( $_URL [3] ) );
	$question_object->del ( array ('id' => $about_id ) );
}
$alert_info = "";
while ( ! empty ( $_POST ) ) {
	if (empty ( $_POST ['title'] )) {
		$alert_info = "标题不能为空";
		break;
	}
	if (empty ( $_POST ['show_author'] )) {
		$alert_info = "作者不能为空";
		break;
	}
	if (empty ( $_POST ['content'] )) {
		$alert_info = "内容不能为空";
		break;
	}
	
	$insert_result = $question_object->insert ( array ('title' => $_POST ['title'], 'show_author' => $_POST ['show_author'], 'content' => $product_content = $Object_filter->filter_edit_content ( $Object_filter->get_real_gpc_var ( 'content' ) ), 'ctime' => time (), 'author' => $_USER ['user_name'], 'author_id' => $_USER ['user_id'] ), array ('%s', '%s', '%s', '%d', '%s', '%d' ) );
	$alert_info = ($insert_result) ? '添加文章成功' : '添加文章失败';
	break;
}
$all_about_num = $question_object->get_table_all_num ();
$star_num = System::get_page_star_num ( $page, 5, $all_about_num );
$page_html = System::get_page_html ( $page, 5, $all_about_num, $Object_url->get_url () );
$all_about = $question_object->get_all_value ( 'ctime', $star_num, 5 );
require_once APP_URL.'edit/model/edit_class.php';
$Object_template->assign ( array ('edit_html'=>Edit::create_eedit('content'),'alert_info' => $alert_info, 'page' => $page, 'all_about' => $all_about, 'page_html' => $page_html ) );
$Object_template->display ( MANAGER_APP . '/admin/news_show', 0 );