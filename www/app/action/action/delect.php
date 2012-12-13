<?php
defined ( 'IS_ME' ) or exit ();
$return_info = "你还没有登录";
if ($Object_user->is_login ()) {
	$return_info = "该活动不存在".$Object_filter->get_abs_int ( $_URL [2] );
	if (isset ( $_URL [2] )) {
		$action_id = $Object_filter->get_abs_int ( $_URL [2] );
		$action_info = $action_object->get_one_action ( $action_id );
		if (!empty ( $action_info ) && ($action_info->action_publisher_id == $_USER ['user_id'])) {
			//del action
			$action_object->delect_action_by_ids_str ( $action_info->id );
			//del logo 
			$action_logo_url = str_replace ( HTML_UPLOAD_URL, UPLOAD_URL, $action_info->action_logo );
			unlink ( $action_logo_url );
			//del like 
			require_once 'model/action_like_class.php';
			$action_like_object = new Action_like ();
			$action_like_object->delect_like_by_action_ids ( $action_info->id );
			
			//del join
			require_once 'model/action_join_class.php';
			$action_join_object = new Action_join ();
			//del works
			if ($action_info->action_need_works == 1) {
				$join_action_user_info = $action_join_object->get_action_join_info ( $action_info->id );
				foreach ( $join_action_user_info as $one_user ) {
					unlink ( $one_user->user_works );
				}
			}
			$action_join_object->delect_action_join ( $action_info->id );
			//del user count
			require_once 'model/user_action_count_class.php';
			$user_count_object = new Action_user_action_count ();
			$user_count_object->del_user_action ( $_USER ['user_id'], $action_info->id );
			$return_info = 'success';
		}
	}
}
require_once LIB_URL . 'class_json.php';
$json_object = new Services_JSON ();
echo $json_object->encode ( $return_info );
exit ();