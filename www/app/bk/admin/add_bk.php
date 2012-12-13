<?php
defined ( 'IS_ME' ) or exit ();
require_once APP_URL . MANAGER_APP . '/model/bk_class.php';
$question_object = new bk ();
$alert_info = "";
while ( ! empty ( $_POST ) ) {
	if (empty ( $_POST ['title'] )) {
		$alert_info = "标题不能为空";
		break;
	}
	if (empty ( $_POST ['content'] )) {
		$alert_info = "内容不能为空";
		break;
	}
	
	$insert_result = $question_object->insert ( array ('title' => $_POST ['title'], 'content' => $_POST ['content'], 'ctime' => time (), 'author' => $_USER ['user_name'], 'author_id' => $_USER ['user_id'] ), array ('%s', '%s', '%d', '%s', '%d' ) );
	$alert_info = ($insert_result) ? '添加百科成功' : '添加百科失败';
	break;
}
$Object_template->assign ( array ('alert_info' => $alert_info ) );
$Object_template->display ( MANAGER_APP . '/admin/add_bk', 0 );