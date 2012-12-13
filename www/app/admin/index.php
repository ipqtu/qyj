<?php
defined ( 'IS_ME' ) or exit ();
//判断用户
($Object_user->is_admin () || $Object_user->is_founder ()) || Display::display_404_error ();

if ($Object_user->is_founder ())
	define ( 'IS_FOUNDER', true );
else
	define ( 'IS_FOUNDER', false );

	//判断action
$action = isset ( $_URL [1] ) ? $_URL [1] : "index_index";

//app管理，加载各个模块的admin管理页面
if (isset ( $_URL [1] ) && array_key_exists ( $action, $system_config ['all_app'] )) {
	define ( "MANAGER_APP", $action );
	$app_action = isset ( $_URL [2] ) ? $_URL [2] . '.php' : 'index.php';
	$next_file_name = APP_URL . MANAGER_APP . '/admin/' . $app_action;
} else {
	//初始化管理员页面
	$next_file_name = APP_URL . APP . '/action/' . $action . '.php';
}
file_exists ( $next_file_name ) || Display::display_404_error ();
$title = "趣友街网站公理系统";
//加载app的action处理文件
include $next_file_name;