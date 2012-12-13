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
	if (empty ( $_POST ['user_login'] )) {
		$user_error = $language ['name_not_null'];
		break;
	}
	if (empty ( $_POST ['password'] )) {
		$password_error = $language ['passsword_not_null'];
		break;
	}
	//用户名检查
	$login_result = $Object_user->login ( $_POST ['user_login'], $_POST ['password'] );
	if ($login_result != 'login_success') {
		$login_error = $language [$login_result];
		break;
	}
	//已经绑定的了用户不能再绑定了
	$third_name = $Object_user->get_current_user_append_info ( $_SESSION ['binding_type'] . '_name' );
	if(!empty($third_name)){
		$login_error = $language ['have_binding_renren'].$third_name;
		break;
	}
	//绑定用户
	$user_id = $Object_user->get_current_user_base_info ( 'id' );
	$third_login_object->binding_user ( $user_id, $_SESSION ['binding_id'] );
	$append_info = array ($_SESSION ['binding_type'] . '_id' => $third_user_info->third_id, $_SESSION ['binding_type'] . '_name' => $third_user_info->third_name, 'avatar' => $third_user_info->third_avatar );
	$Object_user->add_user_append_info ( $user_id, $append_info );
	Display::load_url ();
	exit ();
}
$Object_template->assign ( array ('user_error' => $user_error, 'password_error' => $password_error, 'login_error' => $login_error, 'user_third_url' => $third_login_object->get_third_user_url (), 'user_error' => $user_error, 'password_error' => $password_error, 'email_error' => $email_error, 'third_user_info' => $third_user_info ) );
$Object_template->display ( $APP . '/bindingh' );

