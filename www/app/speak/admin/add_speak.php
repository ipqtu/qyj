<?php
defined ( 'IS_ME' ) or exit ();
require_once APP_URL . MANAGER_APP . '/model/speak_class.php';
$speak_object = new speak ();
$alert_info = "";
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
	if (!isset( $_POST ['type'] )) {
		$alert_info = "请选择分类";
		break;
	}
	$type = abs ( intval ( $_POST ['type'] ) );
	(key_exists ( $type, $speak_cache ['type'] )) || Display::display_404_error ();
	$insert_result = $speak_object->insert ( array ('title' => $_POST ['title'], 'content' => $_POST ['content'], 'ctime' => time (), 'author' => $_USER ['user_name'], 'author_id' => $_USER ['user_id'], 'type' => $type ), array ('%s', '%s', '%d', '%s', '%d', '%d' ) );
	$alert_info = ($insert_result) ? '添加文章成功' : '添加文章失败';
	break;
}

$Object_template->assign ( array ('alert_info' => $alert_info, 'type' => $speak_cache ['type'] ) );
$Object_template->display ( MANAGER_APP . '/admin/add_speak', 0 );