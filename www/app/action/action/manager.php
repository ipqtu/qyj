<?php
defined ( 'IS_ME' ) or exit ();
isset ( $_URL [2] ) || Display::display_404_error ();
$Object_user->is_login () || Display::display_nologin ();
$action_info = $action_object->get_one_action ( $_URL [2] );
empty ( $action_info ) && Display::display_back ( '该活动已经不存在了' );
//判断是否过期
if (($action_info->is_over == 0) && ($action_info->action_end_time < time ())) {
	$action_object->over_action ( $action_info->id );
	$action_info->is_over = 1;
	//修改最新活动统计
	require_once 'model/user_action_count_class.php';
	$user_new_action_count_object = new Action_user_action_count ();
	$user_new_action_count_object->over_user_action ( $action_info->action_publisher_id, $action_info->id );
}
//判断活动是不是他的
($action_info->action_publisher_id == $_USER ['user_id']) || Display::display_dialog ( '你完全查看该活动情况' );
require_once 'model/action_join_class.php';
$action_join_object = new Action_join ();
$join_user_info = $action_join_object->get_action_join_info ( $action_info->id );
$Object_template->assign ( array ('join_user_info' => $join_user_info ) );
$Object_template->assign ( $action_info );
$Object_template->display ( 'action/manager' );