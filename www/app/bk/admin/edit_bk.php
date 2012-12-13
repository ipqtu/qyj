<?php
defined ( 'IS_ME' ) or exit ();
require_once APP_URL . MANAGER_APP . '/model/bk_class.php';
$question_object = new bk ();
(isset ( $_URL [3] )) || Display::display_404_error ();
$alert_info = "";
$bk_id = abs ( intval ( $_URL [3] ) );
while ( ! empty ( $_POST ) ) {
	if (empty ( $_POST ['title'] )) {
		$alert_info = "标题不能为空";
		break;
	}
	if (empty ( $_POST ['content'] )) {
		$alert_info = "内容不能为空";
		break;
	}
	
	$edit_result = $question_object->edit ( array ('title' => $_POST ['title'], 'content' => $_POST ['content'], 'ctime' => time (), 'author' => $_USER ['user_name'], 'author_id' => $_USER ['user_id'] ), array ('id' => $bk_id ), array ('%s', '%s', '%d', '%s', '%d' ) );
	$alert_info = ($edit_result) ? '编辑百科成功' : '编辑百科失败';
	break;
}
$bk_info = $question_object->get_value_by_field ( 'id', $bk_id );
(! empty ( $bk_info )) || Display::display_404_error ();
$Object_template->assign ( array ('bk_info' => $bk_info [0], 'alert_info' => $alert_info ) );
$Object_template->display ( MANAGER_APP . '/admin/edit_bk', 0 );