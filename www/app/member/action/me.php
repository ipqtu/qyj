<?php
defined ( 'IS_ME' ) or exit ();
($Object_user->is_login ()) || Display::display_nologin();
if (! empty ( $_POST )) {
	$update_data ['user_college'] = $Object_filter->get_abs_int ( $_POST ['user_college'] ) % 2 + 1;
	$update_data ['user_autograph'] = $_POST ['user_autograph'];
	$update_data ['user_blogs'] = implode ( "||", $_POST ['user_blogs'] );
	require_once LIB_URL . 'class_file.php';
	$avatar_pre_path = 'avatars/' . File::get_avatar_file_pre_path ( $_USER ['user_id'] );
	$avatar_path = UPLOAD_URL . $avatar_pre_path . 'small.jpg';
	file_exists ( $avatar_path ) && $update_data ['user_avatar'] = $_USER ['user_avatar'] = HTML_UPLOAD_URL . $avatar_pre_path;
	$Object_user->update_current_user_append_infos ( $update_data );
}
$_USER ['user_college'] = $Object_user->get_current_user_append_info ( 'user_college' );
$_USER ['user_autograph'] = $Object_user->get_current_user_append_info ( 'user_autograph' );
$_USER ['user_blogs'] = explode ( '||', $Object_user->get_current_user_append_info ( 'user_blogs' ) );
$Object_template->assign ( $_USER );
$Object_template->assign ( array ('type' => 0, 'user_email' => $Object_user->get_current_user_base_info ( 'user_email' ) ) );
$Object_template->display ( $APP . '/me' );