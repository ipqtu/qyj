<?php
defined ( 'IS_ME' ) or exit ();
$error = $share_id = "";
if ($Object_user->is_login ()) {
	//添加纸条
	if (! empty ( $_POST )) {
		if (empty ( $_POST ['title'] ) || empty ( $_POST ['content'] )) {
			$error = '相关信息不能为空';
		} else {
			$type = $Object_filter->get_abs_int ( $_POST ['type'] ) % count ( $app_cache ['share_type'] );
			$share_id = $app_object->add ( $_USER ['user_id'], $_USER ['user_name'], $_POST ['title'], $type, $_POST ['content'] );
		}
	}
} else {
	$error = '你还没登录不能发经验纸条';
}

$Object_template->assign ( array ('title' => $title, 'error' => $error, 'share_type' => $app_cache ['share_type'], 'share_id' => $share_id ) );
$Object_template->display ( $APP . '/public' );