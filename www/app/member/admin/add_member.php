<?php
defined ( 'IS_ME' ) or exit ();
$alert_info = "";
while ( ! empty ( $_POST ) ) {
	if (empty ( $_POST ['user_login'] )) {
		$alert_info = "用户名不能为空";
		break;
	}
	if (empty ( $_POST ['password'] )) {
		$alert_info = "密码不能为空";
		break;
	}
	if (empty ( $_POST ['user_email'] )) {
		$alert_info = "邮箱不能为空";
		break;
	}
	if (! $Object_filter->is_email ( $_POST ['user_email'] )) {
		$alert_info = "邮箱格式不正确";
		break;
	}
	//用户名检查
	$user = $Object_user->get_user_by ( 'name', $_POST ['user_login'] );
	if (!empty ( $user )) {
		$alert_info = "用户名已经存在";
		break;
	}
	//邮箱检查
	$user = $Object_user->get_user_by ( 'email', $_POST ['user_email'] );
	if (!empty ( $user )) {
		$alert_info = "该邮箱已经注册,请换其他邮箱谢谢";
		break;
	}
	
	//生成随机激活密钥
	$activation_key = $system_config ['open_user_activation'] ? System::get_random_string ( 12, false ) : "";
	//注册
	$result = $Object_user->regist ( $_POST ['user_login'], $_POST ['password'], $_POST ['user_email'], $activation_key, 0 );
	if (! $result) {
		$alert_info = "添加用户失败";
		break;
	}
	$user_id = $Object_user->get_regist_user_id ();
	$title = $content = "恭喜你,你已经是我们趣友街的一份子了,赶快体验一下吧!";
	$Object_message_box->system_send_message ( $user_id, $_POST ['user_login'], $content, $title );
	$alert_info = "添加用户成功";
	break;
}
$Object_template->assign(array('alert_info'=>$alert_info));
$Object_template->display ( MANAGER_APP . '/admin/add_member', 0 );