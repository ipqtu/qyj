<?php
defined ( 'IS_ME' ) or exit ();
$message_type = 0;
($Object_user->is_login ()) || Display::display_nologin ();
if (isset ( $_GET ['delete'] )) {
	$message_id = $Object_filter->get_abs_int ( $_GET ['delete'] );
	$Object_message_box->del_message ( $message_id, $_USER ['user_id'] );
} elseif (isset ( $_GET ['type'] )) {
	isset ( $_GET ['type'] ) && $message_type = $Object_filter->get_abs_int ( $_GET ['type'] ) % 3;
} elseif (isset ( $_URL [2] )) {
	$message_id = $Object_filter->get_abs_int ( $_URL [2] );
	if (! empty ( $_POST )) {
		$content = $_POST ["content"];
		$title = System::sub_str ( $content, 32 );
		$message_id = $Object_filter->get_abs_int ( $_POST ['message_id'] );
		$message_id = $Object_message_box->replay_message ( $message_id, $_USER ['user_id'], $_USER ['user_name'], $title, $content );
		empty ( $message_id ) && Display::display_404_error ();
	}
	$message_info = $Object_message_box->get_message ( $_USER ['user_id'], $message_id );
	(empty ( $message_info )) && Display::load_url ( '/' );
	$Object_template->assign ( array ('type' => 2, 'message_info' => $message_info, 'message_id' => $message_id ) );
	$Object_template->display ( $APP . '/message_show' );
	exit ();
}

$user_all_message = $Object_message_box->list_user_message ( $_USER ['user_id'], 0, 20, $message_type );
$Object_template->assign ( array ('type' => 2, 'user_all_message' => $user_all_message, 'message_type' => $message_type ) );
$Object_template->display ( $APP . '/message' );