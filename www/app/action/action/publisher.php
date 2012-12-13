<?php
defined ( 'IS_ME' ) or exit ();
(isset ( $_URL [2] )) || Display::display_404_error ();
$user_id = $Object_filter->get_abs_int ( $_URL [2] );
($user_id > 0) || Display::display_404_error ();
$user_info = $Object_user->get_user_by ( 'id', $user_id );
empty ( $user_info ) && Display::display_back ( '该用户不存在' );
$type = (isset ( $_URL [3] )) ? $Object_filter->get_abs_int ( $_URL [3] ) : 1;
$all_actions = $user_action_count = "";
require_once 'model/user_action_count_class.php';
$user_actio_count_object = new Action_user_action_count ();
$user_action_count = $user_actio_count_object->get_user_action_info ( $user_info['id'] );
switch ($type) {
	case 2 :
		{
			//publish action
			if ((! empty ( $user_action_count )) && ($user_action_count->action_num > 0)) {
				$all_actions = $action_object->get_action_by_ids_str ( unserialize ( $user_action_count->action_ids ), 0, 5 );
			}
			break;
		}
	default :
		{
			$type = 1;
			//new action
			if ((! empty ( $user_action_count )) && ($user_action_count->new_action_num > 0)) {
				$all_actions = $action_object->get_action_by_ids_str ( unserialize($user_action_count->new_action_ids), 0, 5 );
			}
			break;
		}
}

if(empty($user_action_count)){
	$user_action_count = new stdClass();
	$user_action_count->user_id = $user_info['id'];
	$user_action_count->user_name=$user_info['user_name'];
	$user_action_count->new_action_num=0;
	$user_action_count->action_num=0;
} 
$Object_template->assign ( array ('all_actions' => $all_actions, 'type' => $type, 'user_action_count' => $user_action_count ) );
$Object_template->display ( $APP . '/publisher' );