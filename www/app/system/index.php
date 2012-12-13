<?php
defined ( 'IS_ME' ) or exit ();
//判断action
$action_array = array ('system_left','system_top','system_index','system_switchframe','system_main_top','system_main','app','member','member_edit','member_del' );
$action = isset ( $_URL [1] ) ? $_URL [1] : 'system_index';
(in_array ( $action, $action_array )) || Display::display_404_error ();
//
($Object_user->is_admin() || $Object_user->is_founder()) ||  Display::display_404_error ();
//加载action
$action_file_name = APP_URL . $APP . '/action/' . $action . '.php';
file_exists ( $action_file_name ) || Display::display_404_error ();
$title="趣友街网站公理系统";
//加载app的action处理文件
include $action_file_name;