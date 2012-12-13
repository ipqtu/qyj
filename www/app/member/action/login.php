<?php
defined ( 'IS_ME' ) or exit ();
($Object_user->is_login ()) && Display::load_url ();
$error = "";
while ( ! empty ( $_POST ) ) {
	if (empty ( $_POST ['user_login'] )) {
		$error = $language ['name_not_null'];
		break;
	}
	if(empty ( $_POST ['user_pass'] )){
		$error = $language ['passsword_not_null'];
		break;	
	}
	//用户名检查
	$login_result = $Object_user->login ( $_POST ['user_login'], $_POST ['user_pass'] );
	($login_result == 'login_success') && Display::load_url ();
	$error = $language [$login_result];
	break;
}
$user_name = (isset ( $_COOKIE ['user_name'] )) ? $_COOKIE ['user_name'] : "";
$Object_template->assign ( array ('user_name' => $user_name, 'error' => $error ) );
$Object_template->display ( $APP . '/login', 0 );