<?php 
return $app_state = array(
	'app'=>'email',
	'app_name'=>"邮件app",
	'author'=>'ipqtu',
	'describe'=>'邮件模块,负责邮件的发生',
	'allow_close'=> 0,
	'defult_state'=> 1,
	'manager_app_title'=>'邮件',
	'manager_action'=>array(
		'基本管理'=>array(
			'邮件发送方式'=>"email_method",
			'参数设置'=>'email_setting',
		),
	),
);