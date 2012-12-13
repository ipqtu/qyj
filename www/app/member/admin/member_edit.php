<?php
defined ( 'IS_ME' ) or exit ();
isset ( $_URL [3] ) || Display::display_404_error ();
$user_id = abs ( intval ( $_URL [3] ) );
if (! IS_FOUNDER) {
	// admin
	$member_cache_data = $Object_filecache->get ( MANAGER_APP );
	if ($user_id != $_USER ['user_id']) {
		if (($user_id == FOUNDER_ID) || in_array ( $user_id, $member_cache_data ['admin_ids'] ))
			Display::display_dialog ( "你没有权限更改该用户信息", $Object_url->make_url ( array ('admin', 'member', 'show_all_member' ) ) );
	}
}
$alert_info = "";
if (! empty ( $_POST )) {
	$user_paswword = $_POST ['password'];
	$user_email = $_POST ['email'];
	if (empty ( $user_email )) {
		$alert_info = "邮箱不能为空";
	} elseif (! $Object_filter->is_email ( $user_email )) {
		$alert_info = "邮箱地址不合法";
	} else {
		$update_data_array = array ('user_email' => $user_email );
		if (! empty ( $user_paswword )) {
			$enpassword = $Object_user->HashPassword ( $user_paswword );
			$update_data_array ['user_pass'] = $enpassword;
		}
		$Object_user->update_user ( $user_id, $update_data_array );
		$alert_info = "修改成功";
	}
}
$user_info = $Object_user->get_user_by ( 'id', $user_id );
$Object_template->assign ( array ('user_info' => $user_info, 'alert_info' => $alert_info ) );
$Object_template->display ( MANAGER_APP . '/admin/member_edit', 0 );