<?php 
return $app_state = array(
	'app'=>'member',
	'app_name'=>"用户app",
	'author'=>'ipqtu',
	'describe'=>'描述',
	'allow_close'=> 0,
	'defult_state'=> 1,
	'manager_app_title'=>'用户',
	'manager_action'=>array(
		'用户管理'=>array(
			'全部用户'=>'show_all_member',
			'添加用户'=>'add_member',
			'查找用户'=>'search_member',
		),
	),
);