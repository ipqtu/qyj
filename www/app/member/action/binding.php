<?php
defined ( 'IS_ME' ) or exit ();
session_start ();
isset ( $_SESSION ['binding_type'] ) || Display::display_404_error ();
isset ( $_SESSION ['binding_id'] ) || Display::display_404_error ();
($_SESSION ['binding_id'] > 0) || Display::display_404_error ();
require_once 'model/thirdlogin_class.php';
$third_login_object = new Thirdlogin ( '', $_SESSION ['binding_type'] );
$third_user_info = $third_login_object->get_binding_user_info ( $_SESSION ['binding_id'] );
(empty ( $third_user_info )) && Display::display_404_error ();
while ( ! empty ( $_POST ) ) {
	$user_error = $password_error = $email_error = "";
	if (empty ( $_POST ['user_login'] )) {
		$user_error = $language ['name_not_null'];
		break;
	}
	if (empty ( $_POST ['password'] )) {
		$password_error = $language ['passsword_not_null'];
		break;
	}
	if (empty ( $_POST ['user_email'] )) {
		$email_error = $language ['email_not_null'];
		break;
	}
	if (! $Object_filter->is_email ( $_POST ['user_email'] )) {
		$email_error = $language ['email_not_right'];
		break;
	}
	//用户名检查
	$user = $Object_user->get_user_by ( 'name', $_POST ['user_login'] );
	if (! empty ( $user )) {
		$user_error = $language ['name_exist'];
		break;
	}
	//邮箱检查
	$user = $Object_user->get_user_by ( 'email', $_POST ['user_email'] );
	if (! empty ( $user )) {
		$email_error = $language ['email_exist'];
		break;
	}
	//附加信息
	$append_info = array ($_SESSION ['binding_type'] . '_id' => $third_user_info->third_id, $_SESSION ['binding_type'] . '_name' => $third_user_info->third_name,$_SESSION ['binding_type'] . '_avatar' => $third_user_info->third_avatar, );
	//生成随机激活密钥
	$activation_key = $system_config ['open_user_activation'] ? System::get_random_string ( 12, false ) : "";
	//注册
	$result = $Object_user->regist ( $_POST ['user_login'], $_POST ['password'], $_POST ['user_email'], $activation_key, $system_config ['open_user_activation'] );
	$result || Display::display_back ( $language ['regist_fail'] );
	//绑定用户
	$third_login_object->binding_user ( $Object_user->get_regist_user_id (), $_SESSION ['binding_id'] );
	//生成邮件
	if ($system_config ['open_user_activation']) {
		$activate_url = $Object_url->mk_url ( array ('member', 'activate', $Object_user->get_regist_user_id (), $activation_key ) );
		$email_content = str_replace ( 'user_name', $_POST ['user_login'], $language ['activate_email_content'] );
		$email_content = str_replace ( 'activate_url', $activate_url, $email_content );
	} else {
		$email_content = str_replace ( 'user_name', $_POST ['user_login'], $language ['regist_email_content'] );
	}
	//发送邮件
	require_once APP_URL . 'email/email_class.php';
	Email::send_email ( $_POST ['user_email'], $_POST ['user_login'], $email_content );
	//自动登录
	$Object_user->third_user_login ( $Object_user->get_regist_user_id () );
	$_SESSION = "";
	Display::load_url ();
}
$Object_template->assign ( array ('user_third_url' => $third_login_object->get_third_user_url (), 'user_error' => $user_error, 'password_error' => $password_error, 'email_error' => $email_error, 'third_user_info' => $third_user_info ) );
$Object_template->display ( $APP . '/binding' );

