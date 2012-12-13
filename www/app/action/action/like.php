<?php
defined ( 'IS_ME' ) or exit ();
$data = array ();
$data ['result'] = false;
require_once LIB_URL . 'class_json.php';
$json_object = new Services_JSON ();
$action_id = $Object_filter->get_abs_int ( $_GET ['action_id'] );
if (empty ( $action_id )) {
	$data ['error'] = $language ["error"];
	echo $json_object->encode ( $data );
	exit ();
}
if ($Object_user->is_login ()) {
	$action_info = $action_object->get_one_action ( $action_id );
	if (empty ( $action_info ))
		$data ['error'] = "该活动不存在";
	elseif ($action_info->check != 1) {
		$data ['error'] = "该活动还没有审核通过";
	}if ($action_info->is_over == 1) {
		$data ['error'] = "该活动已经结束";
	}else {
		require_once 'model/action_like_class.php';
		$action_like_object = new Action_like ();
		if ($action_like_object->check_user_is_like ( $_USER ['user_id'], $action_id )) {
			$action_object->add_like_num ( $action_id );
			$content = "你的活动\"" . $action_info->action_name . '"被' . $_USER ['user_name'] . '标为喜欢,<a href="' . $Object_url->mk_url ( array ('action', 'display', $action_info->id ) ) . '"点击此处查看</a>';
			$title = "你的活动\"" . $action_info->action_name . '"被' . $_USER ['user_name'] . '标为喜欢...';
			$Object_message_box->system_send_message ( $action_info->action_publisher_id, $action_info->action_publisher_name, $content, $title );
			$data ['result'] = true;
		} else {
			$data ['error'] = '你已经感兴趣过了';
		}
	}
} else {
	$data ['error'] = '还没有登录是不可以喜欢的哦';
}
echo $json_object->encode ( $data );
exit ();