<?php 
return $app_state = array(
	'app'=>'system',
	'app_name'=>"系统app",
	'author'=>'ipqtu',
	'describe'=>'描述',
	'allow_close'=> 0,
	'defult_state'=> 0,
	'manager_app_title'=>'系统',
	'manager_action'=>array(
		'基本管理'=>array(
			'更新app'=>'app_upload',
			'app管理'=>'app',
			'系统邮箱管理'=>"email",
		),
	),
);