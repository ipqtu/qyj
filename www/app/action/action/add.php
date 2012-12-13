<?php
defined ( 'IS_ME' ) or exit ();
$Object_user->is_login () || Display::display_nologin ();
if (! empty ( $_POST )) {
	//
	(empty ( $_POST ['action_name'] )) && Display::display_back( "活动名称不能为空" );
	$action_type_id = $Object_filter->get_abs_int ( $_POST ['action_type'] ) % count ( $app_cache ['action_type'] );
	$action_type_name = $app_cache ['action_type'] [$action_type_id];
	(empty ( $_POST ['action_host'] )) && Display::display_back ( "活动主办方不能为空" );
	(empty ( $_POST ['action_target'] )) && Display::display_back ( "活动对象不能为空" );
	(empty ( $_POST ['action_address'] )) && Display::display_back ( "活动地址不能为空" );
	(empty ( $_POST ['action_star_time'] ) || empty ( $_POST ['action_end_time'] )) && Display::display_back ( "活动时间不能为空" );
	
	$action_star_time = strtotime ( $_POST ['action_star_time'] );
	$action_over_time = $action_end_time = strtotime ( $_POST ['action_end_time'] );
	(($action_star_time > $action_end_time) || ($action_end_time < time ())) && Display::display_back ( "你要搞穿越活动?" );
	//处理时间
	if (($action_end_time - $action_star_time) > 86400) {
		$action_time_str = date ( "m月d日", $action_star_time );
		$action_time_str .= ' - ' . date ( "m月d日", $action_end_time );
	} else {
		$action_time_str = date ( "m月d日", $action_star_time );
		$action_time_str .= date ( "H:i", $action_star_time ) . ' - ' . date ( "H:i", $action_end_time );
	}
	(empty ( $_POST ['content'] )) && Display::display_back ( "活动内容请描述一下好吗?" );
	$action_content = $Object_filter->filter_edit_content ( $Object_filter->get_real_gpc_var ( 'content' ) );
	//action online
	$action_online = 0;
	if (isset ( $_POST ['action_online'] )) {
		$action_online = 1;
		$action_needs = array ();
		$action_needs ['need_user_name'] = isset ( $_POST ['need_user_name'] ) ? 1 : 0;
		$action_needs ['need_user_class'] = isset ( $_POST ['need_user_class'] ) ? 1 : 0;
		$action_needs ['need_user_email'] = isset ( $_POST ['need_user_email'] ) ? 1 : 0;
		$action_needs ['need_user_tel'] = isset ( $_POST ['need_user_tel'] ) ? 1 : 0;
		$action_needs ['need_user_sex'] = isset ( $_POST ['need_user_sex'] ) ? 1 : 0;
		$action_needs ['need_user_works'] = isset ( $_POST ['need_user_works'] ) ? 1 : 0;
		(array_sum ( $action_needs ) == 0) && Display::display_back ( "参加你的活动不需要填点相关信息吗?" );
	}
	//logo
	require_once LIB_URL . 'class_file.php';
	$upload_result = File::image_upload ( $_FILES ['action_logo'], array ('jpg', 'jpeg', 'png' ), array (160, 220 ) );
	($upload_result ['result']) || Display::display_back ( $upload_result ['error'] );
	//cut image
	require_once LIB_URL . 'class_image.php';
	$Object_image = new Image ();
	$action_logo = $Object_image->image_resize ( $upload_result ['file'], 160, 220 );
	unlink ( $upload_result ['file'] );
	$action_logo = str_replace ( UPLOAD_URL, HTML_UPLOAD_URL, $action_logo );
	//check_user_real
	$action_publisher_name = $_USER ['user_name'];
	$action_publisher_type = 0;
	$action_publisher_real = 0;
	$action_check = 0;
	require_once 'model/real_publisher_class.php';
	$real_publisher_object = new Real_publisher ();
	$publisher_real_info = $real_publisher_object->get_real_publisher_info ( $_USER ['user_id'] );
	if (! empty ( $publisher_real_info )) {
		$action_publisher_name = $publisher_real_info->user_real_name;
		$action_publisher_type = $publisher_real_info->user_type;
		$action_publisher_real = 1;
		$action_check = 1;
	}
	//add
	$Object_mysql->insert ( $action_object->get_db_table_name (), array ('action_name' => $_POST ['action_name'], 'action_publisher_id' => $_USER ['user_id'], 'action_publisher_name' => $action_publisher_name, 'action_publisher_type' => $action_publisher_type, 'action_publisher_real' => $action_publisher_real, 'action_host' => $_POST ['action_host'], 'action_star_time' => $action_star_time, 'action_end_time' => $action_end_time, 'action_time_str' => $action_time_str, 'action_address' => $_POST ['action_address'], 'action_type_id' => $action_type_id, 'action_type_name' => $action_type_name, 'action_logo' => $action_logo, 'action_content' => $action_content, 'action_online' => $action_online, 'action_need_class' => $action_needs ['need_user_class'], 'action_need_works' => $action_needs ['need_user_works'], 'action_need_sex' => $action_needs ['need_user_sex'], 'action_need_tel' => $action_needs ['need_user_tel'], 'action_need_email' => $action_needs ['need_user_email'], 'action_need_name' => $action_needs ['need_user_name'], 'action_ctime' => date ( "Y-m-d H:i:s" ), 'call_num' => 0, 'interest_num' => 0, 'join_num' => 0, 'is_over' => 0, 'check' => $action_check ) );
	$action_id = $Object_mysql->get_insert_id ();
	//add用户的统计		
	require_once 'model/user_action_count_class.php';
	$user_new_action_count_object = new Action_user_action_count ();
	$user_new_action_count_object->add_user_action ( $_USER ['user_id'], $action_publisher_name, $action_id );
	Display::load_url ( $Object_url->mk_url ( array ('action', 'display', $action_id ) ) );

}
$Object_template->assign ( array ('action_type' => $app_cache ['action_type'] ) );
$Object_template->display ( APP . '/add' );