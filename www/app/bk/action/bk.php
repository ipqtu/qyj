<?php
defined ( 'IS_ME' ) or exit ();
$bk_id = isset ( $_URL [2] ) ? abs ( intval ( $_URL [2] ) ) : Display::display_404_error ();
require APP_URL . APP . '/model/bk_class.php';
$bk_object = new Bk ();
$bk_result = $bk_object->get_value_by_field ( 'id', $bk_id );
(! empty ( $bk_result )) || Display::display_dialog ( "你访问的数据不存在" );
$bk_result = array_pop ( $bk_result );
$bk_object->auto_add_field ( 'call_num', array ('id' => $bk_result->id ) );
//留言 
require_once MODEL_URL . 'leave/leave_class.php';
$leave_model_object = LEAVE_MODEL::get_object ( 'bk_leave', 'bk_leave' );
if ($leave_model_object->check_create ()) {
	$leave_model_object->add_html_var ( 'leave_content', LEAVE_MODEL::$HTML_in_textarea, '留言', '不能为空' );
	$leave_model_object->add_deal_var ( 'leave_content', LEAVE_MODEL::$PHP_deal_edit_textarea );
	$leave_model_object->add_check_var ( 'leave_content', LEAVE_MODEL::$PHP_check_empty, '留言不能为空' );
	$leave_model_object->add_db_var ( array ('content' => 'leave_content' ), array ('author_id' => '$_USER["user_id"]', 'author_name' => '$_USER["user_name"]', 'ctime' => 'time()' ) );
}
$bk_leave_result = $leave_model_object->create_system ();
if (! empty ( $_POST )) {
	require $bk_leave_result ['deal_php'];
	$message = $leave_model_result ? "留言成功" : "留言失败";
	Display::display_dialog ( $message );
}
$Object_template->assign ( array ('bk_result' => $bk_result, 'leave_html' => $bk_leave_result ['in_html'],'list_leave_html' => $bk_leave_result ['list_html'] ) );
$Object_template->display ( APP . '/bk' );