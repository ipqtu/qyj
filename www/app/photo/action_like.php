<?php
defined ( 'IS_ME' ) or exit ();
$data = array ();
$data ['result'] = false;
require_once LIB_URL . 'class_json.php';
$json_object = new Services_JSON ();
$action_id = $Object_filter->get_abs_int ( $_GET ['photo_id'] );
if (empty ( $action_id )) {
	$data ['error'] = $language ["error"];
	echo $json_object->encode ( $data );
	exit ();
}
if ($Object_user->is_login ()) {
	$action_info = $photo_object->get_one_photo ( $action_id );
	if (empty ( $action_info ))
		$data ['error'] = "该图片不存在";
	else {
		require_once 'photo_like_class.php';
		$action_like_object = new Photo_like ();
		if ($action_like_object->check_user_today_is_like ( $_USER ['user_id'], $action_id )) {
			$photo_object->add_photo_like_num ( $action_id );
			$content = "你的照片\"" . $action_info->photo_name . '"今天被' . $_USER ['user_name'] . '标为喜欢,<a href="'.$Object_url->mk_url(array('photo','photo',$action_info->id)).'"点击此处查看</a>';
			$title = "你的照片\"" . $action_info->photo_name . '"今天被' . $_USER ['user_name'] . '标为喜欢...';
			$Object_message_box->system_send_message ( $action_info->photo_author_id, $action_info->photo_author_name, $content, $title );
			$data ['result'] = true;
		} else {
			$data ['error'] = $language ["has_like"];
		}
	}
} else {
	$data ['error'] = $language ["no_login"];
}
echo $json_object->encode ( $data );
exit ();