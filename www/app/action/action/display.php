<?php
defined ( 'IS_ME' ) or exit ();
isset ( $_URL [2] ) || Display::display_404_error ();
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
$action_object->add_call_num ( $action_info->id );
//加载留言
require_once 'model/action_leave_class.php';
$action_leave_object = new Action_leave ();
$error_type = $error_content = $join_result = "";
if (! empty ( $_POST ) && ($action_info->check == 1)) {
	if (isset ( $_POST ['join'] )) {
		//join 
		require_once 'model/action_join_class.php';
		$action_join_object = new Action_join ();
		while ( 1 ) {
			if ($action_info->action_need_name && (empty ( $_POST ['user_name'] ))) {
				$error_type = 'user_name';
				$error_content = '姓名不能为空';
				break;
			}
			$action_info->action_need_name && $data ['user_name'] = $_POST ['user_name'];
			
			if ($action_info->action_need_class && (empty ( $_POST ['user_class'] ))) {
				$error_type = 'user_class';
				$error_content = '班级不能为空';
				break;
			}
			
			$action_info->action_need_class && $data ['user_class'] = $_POST ['user_class'];
			
			if ($action_info->action_need_sex && (empty ( $_POST ['user_sex'] ))) {
				$error_type = 'user_sex';
				$error_content = '性别不能为空';
				break;
			}
			$action_info->action_need_sex && ($data ['user_sex'] = $Object_filter->get_abs_int ( $_POST ['user_sex'] ) == 1 ? "男" : '女');
			
			if ($action_info->action_need_tel && (empty ( $_POST ['user_tel'] ))) {
				$error_type = 'user_tel';
				$error_content = '电话不能为空';
				break;
			}
			$action_info->action_need_tel && $data ['user_tel'] = $_POST ['user_tel'];
			
			if ($action_info->action_need_email) {
				if (empty ( $_POST ['user_email'] )) {
					$error_type = 'user_email';
					$error_content = '邮箱不能为空';
					break;
				}
				if (! $Object_filter->is_email ( $_POST ['user_email'] )) {
					$error_type = 'user_email';
					$error_content = '邮箱不合规范';
					break;
				}
			}
			$action_info->action_need_email && $data ['user_email'] = $_POST ['user_email'];
			
			if ($action_info->action_need_works) {
				require_once LIB_URL . 'class_file.php';
				$upload_result = File::file_upload ( $_FILES ['user_works'], array ('rar', 'zip' ), 0, UPLOAD_URL . 'works/' );
				if (! $upload_result ['result']) {
					$error_type = 'user_works';
					$error_content = $upload_result ['error'];
					break;
				}
				$data ['user_works'] = $upload_result ['file'];
			}
			$data ['user_append_info'] = $_POST ['user_append_info'];
			$data ['ctime'] = date("Y-m-d H:i:s");
			$data ['action_id'] = $action_info->id;
			$data ['user_id'] = $_USER ['user_id'];
			$inser_result = $Object_mysql->insert ( $action_join_object->get_table_name (), $data );
			print_r($inser_result);
			if ($inser_result) {
				$action_object->add_join_num ( $action_info->id );
				$join_result = "恭喜你成功参加了该活动了";
				$action_info->join_num += 1;
			}else{
				$join_result = "参加活动失败请重新提交";
			}
			break;
		}
	} else {
		$Object_user->is_login () || Display::display_nologin ();
		isset ( $_POST ['replay_id'] ) && $replay_leave_info = $action_leave_object->get_one_leave ( $_POST ['replay_id'] );
		if (isset ( $_POST ['replay_id'] ) && (! empty ( $replay_leave_info )) && (! empty ( $_POST ['content'] ))) {
			//回复
			$replay_user_id = $replay_leave_info->action_leave_author_id;
			$replay_user_name = $replay_leave_info->action_leave_author;
			$replay_leave_content = $replay_leave_info->action_leave_content;
			$leave_content = "<div class='replay_leave_content'>回复:<b><i>{$replay_user_name}</i></b><br/><p>" . $replay_leave_content . '</p></div>' . $_POST ['content'];
			$Object_mysql->insert ( $action_leave_object->get_action_leave_table_name (), array ('action_leave_reply_id' => $replay_leave_info->id, 'action_id' => $action_info->id, 'action_leave_author_id' => $_USER ['user_id'], 'action_leave_author' => $_USER ['user_name'], 'action_leave_content' => $leave_content, 'action_leave_ctime' => time () ) );
			//发消息
			$message_per_content = $message_title = '你的评论被' . $_USER ['user_name'] . '回复';
			$message_content = '你的评论被<a href="' . $Object_url->mk_url ( array ('member', 'user', $_USER ['user_id'] ) ) . '">' . $_USER ['user_name'] . '</a>回复,<a href="' . $Object_url->mk_url ( array ('action', 'display', $action_info->id ) ) . '">点击此处查看</a>';
			$Object_message_box->system_send_message ( $replay_user_id, $replay_user_name, $message_content, $message_title );
		} else {
			//留言
			$Object_mysql->insert ( $action_leave_object->get_action_leave_table_name (), array ('action_id' => $action_info->id, 'action_leave_author_id' => $_USER ['user_id'], 'action_leave_author' => $_USER ['user_name'], 'action_leave_content' => $_POST ['content'], 'action_leave_ctime' => time () ) );
			$content = "你的活动\"{$action_info->action_name}\"被{$_USER['user_name']}评论了,<a href='{$Object_url->mk_url(array('action','display',$action_info->id))}'>点击查看此处</a>";
			$title = "你的活动\"{$action_info->action_name}\"被{$_USER['user_name']}评论了...";
			$Object_message_box->system_send_message ( $action_info->action_publisher_id, $action_info->action_publisher_name, $content, $title );
		}
		$action_object->add_leave_num($action_info->id);
	}
}
$action_leave_info = $action_leave_object->get_one_action_leave ( $action_info->id );
foreach ( $action_leave_info as $k => $v ) {
	$action_leave_info [$k] ['author_info'] = $Object_user->get_user_append_by ( $v ['action_leave_author_id'] );
}
$Object_template->assign ( array ('join_result'=>$join_result,'action_leave_info' => $action_leave_info, 'error_content' => $error_content, 'error_type' => $error_type ) );
$Object_template->assign ( $action_info );
$Object_template->display ( APP . '/display' );