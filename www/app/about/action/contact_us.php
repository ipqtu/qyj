<?php
defined ( 'IS_ME' ) or exit ();
require_once APP_URL.APP.'/model/contact_class.php';
$contact_object = new Contact ();
$alert_info = "";
if (! empty ( $_POST ['content'] )) {
	if (isset ( $_POST ['replay_id'] )) {
		$replay_id = abs ( intval ( $_POST ['replay_id'] ) );
		$replay_info = $contact_object->get_value_by_field ( 'id', $replay_id );
		if (isset ( $replay_info [0] )) {
			$replay_info = $replay_info [0];
			$f_id = ($replay_info->floor == 1) ? $replay_info->id : $replay_info->replay_fid;
			//获取最近楼
			$last_replay = $contact_object->get_value_by_where ( array ('replay_fid' => $f_id ), 'ctime', 0, 1 );
			$foor = (empty ( $last_replay )) ? 2 : array_pop ( $last_replay )->floor + 1;
			$contact_object->insert ( array ('admin' => 0, 'user_email' => '', 'content' => $_POST ['content'], 'replay_fid' => $f_id, 'replay_floor' => $replay_info->floor, 'floor' => $foor, 'ctime' => time () ) );
			$alert_info = "谢谢你的意见,我们会尽快处理的。";
		}
	} else {
		$contact_object->insert ( array ('admin' => 0, 'user_email' => $_POST ['email'], 'content' => $_POST ['content'], 'replay_fid' => 0, 'replay_floor' => 0, 'floor' => 1, 'ctime' => time () ) );
		$alert_info = "谢谢你的意见,我们会尽快处理的。";
	}
}
//删除
if (isset ( $_GET ['del'] ) && ($_USER ['user_id'] == FOUNDER_ID)) {
	$del_id = abs ( intval ( $_GET ['del'] ) );
	$replay_info = $contact_object->get_value_by_field ( 'id', $del_id );
	if (isset ( $replay_info [0] )) {
		$replay_info = $replay_info [0];
		$contact_object->del ( array ('id' => $replay_info->id ) ); //本身
		$contact_object->del ( array ('replay_fid' => $replay_info->id ) ); //楼主
		$contact_object->del ( array ('replay_fid' => $replay_info->replay_fid, 'replay_floor' => $replay_info->floor ) ); //回复
	}
}
$page = (isset ( $_GET ['page'] )) ? abs ( intval ( $_GET ['page'] ) ) : 0;
$all_num = $contact_object->get_table_all_num ( array ('replay_fid' => 0 ) );
$star_num = System::get_page_star_num ( $page, 10, $all_num );
$page_html = System::get_page_html ( $page, 10, $all_num, $Object_url->get_url () );
$all_contact = $contact_object->get_value_by_where ( array ('replay_fid' => 0 ), 'ctime', 0, 10 );
$replay_contact = array ();
foreach ( $all_contact as $contact ) {
	$replay_contact [$contact->id] = $contact_object->get_value_by_field ( 'replay_fid', $contact->id, 'ctime', 0 );
}
$Object_template->assign ( array ('alert_info' => $alert_info, 'title' => "趣友街-意见反馈", 'type' => 3, 'page_html' => $page_html, 'all_contact' => $all_contact, 'replay_contact' => $replay_contact ) );
$Object_template->display (TEMPLATE_URL. 'contact_show' );
