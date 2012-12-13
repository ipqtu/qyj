<?php
defined ( 'IS_ME' ) or exit ();
//top 
$top_actions = $action_object->get_all_action_by_limit ( 0, 4 );
//center
$type = (isset ( $_URL [2] )) ? $Object_filter->get_abs_int ( $_URL [2] ) % count ( $app_cache ['action_type'] ) : - 1;
$center_actions = ($type == - 1) ? $action_object->get_all_action_by_limit ( 0, 10 ) : $action_object->get_all_action_by_type ( $type, 0, 10 );
//user action
require_once 'model/user_action_count_class.php';
$user_new_action_count_object = new Action_user_action_count ();
$users_new_action_info = $user_new_action_count_object->get_users_action_info_by_page ( 0, 5 );

$Object_template->assign ( array ('type' => $type, 'top_actions' => $top_actions, 'center_actions' => $center_actions, 'action_types' => $app_cache ['action_type'], 'users_new_action_info' => $users_new_action_info ) );
$Object_template->display ( APP . '/index' );