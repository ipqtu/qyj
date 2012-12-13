<?php
defined ( 'IS_ME' ) or exit ();
$step = 0;
$alert_info = "";
while ( ! empty ( $_POST ) ) {
	if (isset ( $_POST ['user_email'] )) {
		$user_email = $_POST ['user_email'];
		$user_info = $Object_user->get_user_by ( 'email', $user_email );
		if (empty ( $user_info )) {
			$alert_info = "该邮箱不存在";
			break;
		}
		if ($user_info ['user_status'] == 1) {
			$alert_info = "该帐号还处于未激活状态,请先激活谢谢";
			break;
		}
		if ($user_info ['user_status'] != 2) {
			$alert_info = "该帐号已经被管理员屏蔽,请联系管理员";
			break;
		}
		//生成激活key
		$activation_key = System::get_random_string ( 12, false );
		$Object_user->update_user ( $user_info ['id'], array ('user_activation_key' => $activation_key ) );
		$valid_time = time () + 24 * 3600;
		$find_password_url = $Object_url->mk_url ( array ('member', 'find_password', $activation_key, $user_info ['id'], $valid_time ) );
		$email_content = $user_info ['user_name'] . "你好:<br/>&nbsp;&nbsp;&nbsp;你在趣友街网站(www.quyoujie.com)召回密码,请点击该链接修改密码:" . $find_password_url;
		require APP_URL . 'email/index.php';
		send_email ( $user_email, $user_info ['user_name'], $email_content );
		$alert_info = '找回密码邮件已经发生到你的邮箱,请查收你的邮件';
		break;
	}elseif(isset($_POST['user_new_password'])){
		if(empty($_COOKIE['user_id'])||(empty($_COOKIE['user_activation']))){
			$alert_info = "你没权利修改该用户的密码";
			break;
		}
		$user_info = $Object_user->get_user_by('id', $_COOKIE['user_id']);
		if(empty($user_info) || $user_info['user_activation_key'] != $_COOKIE['user_activation']){
			$alert_info = "你没权利修改该用户的密码";
			break;
		}
		$user_new_enpassword = $Object_user->HashPassword($_POST['user_new_password']);
		$Object_user->update_user($user_info['id'], array('user_activation_key'=>'','user_pass'=>$user_new_enpassword));
		Display::display_dialog("你已经成功修改密码了",$_USER['user_login_url']);
		break;
	}
	break;
}
while ( isset ( $_URL [4] ) ) {
	//密码修改
	$activation_key = $_URL [2];
	$user_id = $_URL [3];
	$valid_time = $_URL [4];
	if ($valid_time < time ()) {
		$alert_info = "该链接已经失效,请重新找回密码,谢谢";
		break;
	}
	$user_info = $Object_user->get_user_by ( 'id', $user_id );
	if ($activation_key != $user_info ['user_activation_key']) {
		$alert_info = "该链接已经失效,请重新找回密码,谢谢";
		break;
	}
	$auth_cookie_value = md5 ( System::get_random_string ( 12, false ) );
	$Object_user->update_user ( $user_id, array ('user_activation_key' => $auth_cookie_value ) );
	setcookie ( 'user_id', $user_id, time () + 24 * 3600, USER_COOKIE_PATH, SITECOOKIEPATH );
	setcookie ( 'user_activation', $auth_cookie_value, time () + 24 * 3600, USER_COOKIE_PATH, SITECOOKIEPATH );
	$step = 1;
	break;
}
$Object_template->assign ( array ('alert_info' => $alert_info, 'step' => $step ) );
$Object_template->display ( $APP . '/find_password', 0 );