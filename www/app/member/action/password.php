<?php
defined ( 'IS_ME' ) or exit ();
($Object_user->is_login ()) || Display::display_nologin ();
$password_error = $new_password_error = $re_password_error = "";
while ( ! empty ( $_POST ) ) {
	//密码不能为空
	if (empty ( $_POST ['password'] )) {
		$password_error = "密码不能为空";
		break;
	}
	if (empty ( $_POST ['new_password'] )) {
		$new_password_error = "密码不能为空";
		break;
	}
	if (empty ( $_POST ['re_password'] )) {
		$re_password_error = "密码不能为空";
		break;
	}
	if ($_POST ['new_password'] != $_POST ['re_password']) {
		$re_password_error = "与上不吻合";
		break;
	}
	$user_password = $Object_user->get_current_user_base_info ( 'user_pass' );
	if (! $Object_user->CheckPassword ( $_POST ['password'], $user_password )) {
		$password_error = "密码错误";
		break;
	}
	$user_new_password = $Object_user->HashPassword ( $_POST ['new_password'] );
	$Object_user->update_user ( $_USER ['user_id'], array ('user_pass' => $user_new_password ) );
	$Object_user->logout ();
	Display::display_dialog ( "修改密码成功,请重新登录", $Object_url->mk_url ( array ('member', 'login' ) ) );
}
$Object_template->assign ( array ('type' => 1, 're_password_error' => $re_password_error, 'new_password_error' => $new_password_error, 'password_error' => $password_error ) );
$Object_template->display ( $APP . '/password' );