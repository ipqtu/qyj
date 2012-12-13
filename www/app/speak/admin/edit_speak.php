<?php
defined ( 'IS_ME' ) or exit ();
require_once APP_URL . MANAGER_APP . '/model/speak_class.php';
$speak_object = new speak ();
(isset ( $_URL [3] )) || Display::display_404_error ();
$alert_info = "";
$speak_id = abs ( intval ( $_URL [3] ) );
$speak_cache = $Object_filecache->get ( MANAGER_APP );
while ( ! empty ( $_POST ) ) {
	if (empty ( $_POST ['title'] )) {
		$alert_info = "标题不能为空";
		break;
	}
	if (empty ( $_POST ['content'] )) {
		$alert_info = "内容不能为空";
		break;
	}
	if (! isset ( $_POST ['type'] )) {
		$alert_info = "请选择分类";
		break;
	}
	$type = abs ( intval ( $_POST ['type'] ) );
	(key_exists ( $type, $speak_cache ['type'] )) || Display::display_404_error ();
	$edit_result = $speak_object->edit ( array ('title' => $_POST ['title'], 'content' => $_POST ['content'], 'ctime' => time (), 'author' => $_USER ['user_name'], 'author_id' => $_USER ['user_id'], 'type' => $type ), array ('id' => $speak_id ), array ('%s', '%s', '%d', '%s', '%d', '%d' ) );
	$alert_info = ($edit_result) ? '编辑文章成功' : '编辑文章失败';
	break;
}
$speak_info = $speak_object->get_value_by_field ( 'id', $speak_id );
(! empty ( $speak_info )) || Display::display_404_error ();
$Object_template->assign ( array ('speak_info' => $speak_info [0], 'alert_info' => $alert_info, 'type' => $speak_cache ['type'] ) );
$Object_template->display ( MANAGER_APP . '/admin/edit_speak', 0 );