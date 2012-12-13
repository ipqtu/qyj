<?php
defined ( 'IS_ME' ) or exit ();
$Object_user->is_login () || Display::display_nologin ();
$type = (isset ( $_URL [2] )) ? $Object_filter->get_abs_int ( $_URL [2] ) : 1;
$all_actions = "";
switch ($type) {
	case 2 :
		{
			//join
			require_once 'model/action_join_class.php';
			$actio_join_object = new Action_join ();
			$all_num = $actio_join_object->get_user_join_num ( $_USER ['user_id'] );
			if ($all_num > 0) {
				$action_ids_str = $actio_join_object->get_user_join_action_ids_str ( $_USER ['user_id'] );
				$all_actions = $action_object->get_action_by_ids_str ( $action_ids_str, 0, 5 );
			}
			break;
		}
	case 3 :
		{
			//publish
			require_once 'model/user_action_count_class.php';
			$user_actio_count_object = new Action_user_action_count();
			$user_action_count = $user_actio_count_object->get_user_action_info( $_USER ['user_id'] );
			if (!empty($user_action_count) && $user_action_count->action_num > 0) {
				$all_actions = $action_object->get_action_by_ids_str ( unserialize($user_action_count->action_ids), 0, 5 );
			}
			break;
		}
	default :
		{
			$type = 1;
			require_once 'model/action_like_class.php';
			$actio_like_object = new Action_like ();
			$all_num = $actio_like_object->get_user_like_num ( $_USER ['user_id'] );
			if ($all_num > 0) {
				$action_ids_str = $actio_like_object->get_user_like_action_ids_str ( $_USER ['user_id'] );
				$all_actions = $action_object->get_action_by_ids_str ( $action_ids_str, 0, 5 );
			}
		}
}
$Object_template->assign ( array ('all_actions' => $all_actions, 'type' => $type ) );
$Object_template->display ( $APP . '/me' );