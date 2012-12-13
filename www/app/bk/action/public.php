<?php
defined ( 'IS_ME' ) or exit ();
($Object_user->is_login ()) || Display::display_nologin ();
if (! empty ( $_POST )) {
	(empty ( $_POST ['title'] )) && Display::display_back ( "标题不能为空" );
	$content = $Object_filter->filter_edit_content ( $Object_filter->get_real_gpc_var ( 'content' ) );
	(empty ( $content )) && Display::display_back ( "内容不能为空" );
	$type_id = abs ( intval ( $_POST ['type_id'] ) );
	require_once APP_URL . APP . '/model/bk_class.php';
	$bk_object = new Bk ();
	$insert_result = $bk_object->insert ( array ('title' => $_POST ['title'], 'type_id' => $type_id, 'content' => $content, 'ctime' => time (), 'author' => $_USER ['user_name'], 'author_id' => $_USER ['user_id'] ), array ('%s', '%d', '%s', '%d', '%s', '%d' ) );
	if ($insert_result) {
		$alert_info = '恭喜你，你已经成功添加一个百科';
		$url = $Object_url->mk_url ( array ('bk', 'bk', $bk_object->get_inser_id () ) );
		Display::display_dialog ( $alert_info );
	} else {
		$alert_info = '非常抱歉，百科失败,请重新添加';
		Display::display_back ( $alert_info );
	}
}

require_once APP_URL . 'edit/model/edit_class.php';
$Object_template->assign ( array ('edit_html' => Edit::create_eedit ( 'content' ) ) );
$Object_template->display ( APP . '/public' );