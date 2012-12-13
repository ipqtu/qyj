<?php 
return $app_state = array(
	'app'=>'speak',
	'app_name'=>"说说app",
	'author'=>'ipqtu',
	'describe'=>'说说管理',
	'allow_close'=> 1,
	'defult_state'=> 1,
	'manager_app_title'=>'说说',
	'manager_action'=>array(
		'基本管理'=>array(
			'分类管理'=>'type_manager',
			'全部信息'=>'show_all_speak',
			'添加信息'=>'add_speak',
		),
	),
);