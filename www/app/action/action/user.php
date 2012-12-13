<?php
defined ( 'IS_ME' ) or exit ();
(isset ( $_URL [2] )) || Display::display_404_error ();
$user_id = $Object_filter->get_abs_int ( $_URL [2] );
($user_id > 0) || Display::display_404_error ();
$user_info = $Object_user->get_user_by ( 'id', $user_id );
empty ( $user_info ) && Display::display_back ( '该用户不存在' );
$type = (isset ( $_URL [3] )) ? $Object_filter->get_abs_int ( $_URL [3] ) : 1;
require_once 'model/user_action_count_class.php';
$user_actio_count_object = new Action_user_action_count ();
$user_action_count = $user_actio_count_object->get_user_action_info ( $user_id );
$all_actions = "";
switch ($type) {
	case 2 :
		{
			//join
			require_once 'model/action_join_class.php';
			$actio_join_object = new Action_join ();
			$all_num = $actio_join_object->get_user_join_num ( $user_info['id'] );
			if ($all_num > 0) {
				$action_ids_str = $actio_join_object->get_user_join_action_ids_str ( $user_info['id'] );
				$all_actions = $action_object->get_action_by_ids_str ( $action_ids_str, 0, 5 );
			}
			break;
		}
	case 3 :
		{
			//publish
			if (!empty($user_action_count) && $user_action_count->action_num > 0) {
				$all_actions = $action_object->get_action_by_ids_str ( unserialize ( $user_action_count->action_ids ), 0, 5 );
			}
			break;
		}
	default :
		{
			$type = 1;
			require_once 'model/action_like_class.php';
			$actio_like_object = new Action_like ();
			$all_num = $actio_like_object->get_user_like_num ( $user_info['id'] );
			if ($all_num > 0) {
				$action_ids_str = $actio_like_object->get_user_like_action_ids_str ( $user_info['id'] );
				$all_actions = $action_object->get_action_by_ids_str ( $action_ids_str, 0, 5 );
			}
		}
}
if(empty($user_action_count)){
	$user_action_count = new stdClass();
	$user_action_count->user_id = $user_info['id'];
	$user_action_count->user_name=$user_info['user_name'];
	$user_action_count->new_action_num=0;
	$user_action_count->action_num=0;
}
$Object_template->assign ( array ('user_action_count'=>$user_action_count,'all_actions' => $all_actions,'user_id'=>$user_info['id'], 'type' => $type ) );
$Object_template->display ( $APP . '/user' );