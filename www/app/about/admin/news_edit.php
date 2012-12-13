<?php
defined ( 'IS_ME' ) or exit ();
require_once APP_URL . MANAGER_APP . '/model/news_class.php';
$question_object = new News ();
(isset ( $_URL [3] )) || Display::display_404_error ();
$alert_info = "";
$about_id = abs ( intval ( $_URL [3] ) );
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
	
	$edit_result = $question_object->edit ( array ('title' => $_POST ['title'], 'show_author' => $_POST ['show_author'], 'content' => $product_content = $Object_filter->filter_edit_content($Object_filter->get_real_gpc_var('content')), 'ctime' => time (), 'author' => $_USER ['user_name'], 'author_id' => $_USER ['user_id'] ), array ('id' => $about_id ), array ('%s', '%s', '%s', '%d', '%s', '%d' ) );
	$alert_info = ($edit_result) ? '编辑文章成功' : '编辑文章失败';
	break;
}
$about_info = $question_object->get_value_by_field ( 'id', $about_id );
(! empty ( $about_info )) || Display::display_404_error ();
require_once APP_URL.'edit/model/edit_class.php';
$Object_template->assign ( array ('edit_html'=>Edit::create_eedit('content'),'about_info' => $about_info [0], 'alert_info' => $alert_info ) );
$Object_template->display ( MANAGER_APP . '/admin/news_edit', 0 );