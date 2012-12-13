<?php
defined ( 'IS_ME' ) or exit ();
$alert_info = "" ;
if (! empty ( $_POST )) {
	$email_method = abs ( intval ( $_POST ['email_method'] ) ) % 1;
	$Object_filecache->add ( array ('email_method' => $email_method ), MANAGER_APP );
	$alert_info = "修改成功";
}
$Object_template->assign ( array ('alert_info' => $alert_info ) );
$Object_template->display ( MANAGER_APP . '/admin/method', 0 );