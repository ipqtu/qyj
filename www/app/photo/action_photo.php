<?php
defined ( 'IS_ME' ) or exit ();
isset ( $_URL [2] ) || Display::display_back ( $language ['photo_not_exit'] );
$action_id = abs ( intval ( $_URL [2] ) );
$error = "";
if (! empty ( $_GET )) {
	$action_info = ($_GET ['action'] == 1) ? $photo_object->get_next_photo ( $action_id ) : $photo_object->get_prev_photo ( $action_id );
	$message = "";
	if (empty ( $action_info )) {
		$error = ($_GET ['action'] == 1) ? "这已经是最后一张了" : '这已经是第一张了';
		$action_info = $photo_object->get_one_photo ( $action_id );
	}
} else {
	$action_info = $photo_object->get_one_photo ( $action_id );
	empty ( $action_info ) && Display::display_back ( $language ['photo_not_exit'] );
}
//添加访问人数
$photo_object->add_call_num ( $action_info->id );
//获取用户的图片
$author_other_photos = $photo_object->get_user_photo ( $action_info->photo_author_id, 0, 3 );
require_once LIB_URL . 'class_file.php';
$action_info->photo_url = File::get_image_name ( $action_info->photo_url, 4 );
//加载留言
require_once 'photo_leave_class.php';
$action_leave_object = new Photo_leave ();
if (! empty ( $_POST )) {
	$Object_user->is_login () || Display::display_nologin ();
	isset ( $_POST ['replay_id'] ) && $replay_leave_info = $action_leave_object->get_one_leave ( $_POST ['replay_id'] );
	if (isset ( $_POST ['replay_id'] ) && (! empty ( $replay_leave_info )) && (! empty ( $_POST ['content'] ))) {
		$replay_user_id = $replay_leave_info->photo_leave_author_id;
		$replay_user_name = $replay_leave_info->photo_leave_author;
		$replay_leave_content = $replay_leave_info->photo_leave_content;
		$leave_content = "<div class='replay_leave_content'>回复:<b><i>{$replay_user_name}</i></b><br/><p>" . $replay_leave_content . '</p></div>' . $_POST ['content'];
		$Object_mysql->insert ( $action_leave_object->get_photo_leave_table_name (), array ('photo_leave_reply_id' => $replay_leave_info->id, 'photo_id' => $action_info->id, 'photo_leave_author_id' => $_USER ['user_id'], 'photo_leave_author' => $_USER ['user_name'], 'photo_leave_content' => $leave_content, 'photo_leave_ctime' => time () ) );
		$message_per_content = $message_title = '你的评论被' . $_USER ['user_name'] . '回复';
		$message_content = '你的评论被<a href="' . $Object_url->mk_url ( array ('member', 'user', $_USER ['user_id'] ) ) . '">' . $_USER ['user_name'] . '</a>回复,<a href="' . $Object_url->mk_url ( array ('photo', 'photo', $action_info->id ) ) . '">点击此处查看</a>';
		$Object_message_box->system_send_message ( $replay_user_id, $replay_user_name, $message_content, $message_title );
	} else {
		$Object_mysql->insert ( $action_leave_object->get_photo_leave_table_name (), array ('photo_id' => $action_info->id, 'photo_leave_author_id' => $_USER ['user_id'], 'photo_leave_author' => $_USER ['user_name'], 'photo_leave_content' => $_POST ['content'], 'photo_leave_ctime' => time () ) );
		$content = "你的图片\"{$action_info->photo_name}\"被{$_USER['user_name']}评论了,<a href='{$Object_url->mk_url(array('photo','photo',$action_info->id))}'>点击查看此处</a>";
		$title = "你的图片\"{$action_info->photo_name}\"被{$_USER['user_name']}评论了...";
		$Object_message_box->system_send_message ( $action_info->photo_author_id, $action_info->photo_author, $content, $title );
	}
}
$photo_leave_info = $action_leave_object->get_one_photo_leave ( $action_info->id );
foreach ( $photo_leave_info as $k => $v ) {
	$photo_leave_info [$k] ['author_info'] = $Object_user->get_user_append_by ( $v ['photo_leave_author_id'] );
}
$Object_template->assign ( array ('title'=>$title.$action_info->photo_author.'的"'.$action_info->photo_name.'"图片' ,'error' => $error, 'type' => - 1, 'author_other_photos' => $author_other_photos, 'photo_leave_info' => $photo_leave_info, 'load_php_url' => $Object_url->mk_url ( array ('photo', 'upload' ) ) ) );
$Object_template->assign ( $action_info );
$Object_template->display ( 'photo/photo');