<?php
defined ( 'IS_ME' ) or exit ();
($Object_user->is_login ()) || Display::display_nologin ();
$error = "";
if (isset ( $_URL [2] )) {
	$accept_user_id = $Object_filter->get_abs_int ( $_URL [2] );
	$accept_user = $Object_user->get_user_by ( 'id', $accept_user_id );
	if (empty ( $accept_user )) {
		$error = "你发送的用户不存在";
	} elseif (! empty ( $_POST )) {
		$content = $_POST ["content"];
		$title = System::sub_str ( $content, 32 );
		$message_id = $Object_message_box->send_message ( $accept_user ['id'], $accept_user ['user_name'], $_USER ['user_id'], $_USER ['user_name'], $content, $title );
		if (! $message_id) {
			$error = '发送失败';
		} else {
			Display::load_url ( $Object_url->mk_url ( array ('member', 'message', $message_id ) ) );
		}
	}
	$Object_template->assign ( array ('error' => $error, 'accept_user' => $accept_user, 'type' => 4 ) );
	$Object_template->display ( $APP . '/send_message' );
} else {
	Display::display_404_error ();
}
