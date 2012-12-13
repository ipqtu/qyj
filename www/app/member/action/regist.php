<?php
defined ( 'IS_ME' ) or exit ();
$error = "";
while ( ! empty ( $_POST ) ) {
	if (empty ( $_POST ['user_login'] )) {
		$error = $language ['name_not_null'];
		break;
	}
	if (empty ( $_POST ['password'] )) {
		$error = $language ['passsword_not_null'];
		break;
	}
	if (empty ( $_POST ['user_email'] )) {
		$error = $language ['email_not_null'];
		break;
	}
	if (! $Object_filter->is_email ( $_POST ['user_email'] )) {
		$error = $language ['email_not_right'];
		break;
	}
	//用户名检查
	$user = $Object_user->get_user_by ( 'name', $_POST ['user_login'] );
	if (!empty ( $user )) {
		$error = $language ['name_exist'];
		break;
	}
	//邮箱检查
	$user = $Object_user->get_user_by ( 'email', $_POST ['user_email'] );
	if (!empty ( $user )) {
		$error = $language ['email_exist'];
		break;
	}
	//附加信息
	

	//生成随机激活密钥
	$activation_key = $system_config ['open_user_activation'] ? System::get_random_string ( 12, false ) : "";
	//注册
	$result = $Object_user->regist ( $_POST ['user_login'], $_POST ['password'], $_POST ['user_email'], $activation_key, $system_config ['open_user_activation'] );
	if (! $result) {
		$error = $language ['regist_fail'];
		break;
	}
	$user_id = $Object_user->get_regist_user_id ();
	$title = $content = "恭喜你,你已经是我们趣友街的一份子了,赶快体验一下吧!";
	$Object_message_box->system_send_message ( $user_id, $_POST ['user_login'], $content, $title );
	
	//生成邮件
	if ($system_config ['open_user_activation']) {
		$activate_url = $Object_url->mk_url ( array ('member', 'activate', $user_id, $activation_key ) );
		$email_content = str_replace ( 'user_name', $_POST ['user_login'], $language ['activate_email_content'] );
		$email_content = str_replace ( 'activate_url', $activate_url, $email_content );
	} else {
		$email_content = str_replace ( 'user_name', $_POST ['user_login'], $language ['regist_email_content'] );
	}
	//发送邮件
	require_once APP_URL . 'email/index.php';
	send_email ( $_POST ['user_email'], $_POST ['user_login'], $email_content );
	Display::display_dialog( $language ['regist_success'],$_USER['user_login_url'] );
}
$Object_template->assign ( array ('error' => $error ) );
$Object_template->display ( $APP . '/regist', 0 );
