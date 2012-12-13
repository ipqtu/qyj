<?php
defined ( 'IS_ME' ) or exit ();
$alert_info = "";
$email_cache_data = $Object_filecache->get ( MANAGER_APP );
$email_method = $email_cache_data['email_method'];
if (! empty ( $_POST )) {
	switch ($email_method) {
		case 0 : //SMTP
			{
				$email_char = ($_POST ['char'] == '1') ? 'gb2312' : 'utf-8';
				$email_auth = isset ( $_POST ['email_auth'] ) ? true : false;
				if (! $Object_filter->is_email ( $_POST ['from_email'] )) {
					$alert_info = "发件人邮箱地址不正确";
					break;
				}
				$email_wordwrap = abs ( intval ( $_POST ['wordwrap'] ) );
				if (! $Object_filter->is_email ( $_POST ['replay_email'] )) {
					$alert_info = "回复人邮箱地址不正确" . $_POST ['replay_email'];
					break;
				}
				$email_cache_data = $update_email_info = array('SMTP' =>array ('email_char' => $email_char, 'email_auth' => $email_auth, 'email_host' => $_POST ['email_host'], 'email_port' => abs ( intval ( $_POST ['email_port'] ) ), 'email_user' => $_POST ['email_user_name'], 'email_password' => $_POST ['email_password'], 'from_email' => $_POST ['from_email'], 'from_name' => $_POST ['from_name'], 'from_email_title' => $_POST ['from_email_title'], 'email_wordwrap' => $email_wordwrap, 'replay_email' => $_POST ['replay_email'], 'replay_name' => $_POST ['replay_name'] ));
				$Object_filecache->add ( $update_email_info , MANAGER_APP );
				$alert_info = "修改成功";
				break;
			}
	}
}

$Object_template->assign ( array ('alert_info' => $alert_info, 'email_cache_data' => $email_cache_data, 'email_method' => $email_method ) );
$Object_template->display ( MANAGER_APP . '/admin/setting', 0 );