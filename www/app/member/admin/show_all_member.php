<?php
defined ( 'IS_ME' ) or exit ();
$page = isset ( $_GET ['page'] ) ? $_GET ['page'] : 0;
$alert_info = "";
//删除
while ( isset ( $_URL [3] ) ) {
	isset ( $_URL [4] ) || Display::display_404_error ();
	$allow_action = array ('del' => "删除", 'shield' => "屏蔽", 'normal' => "解除" );
	array_key_exists ( $_URL [3], $allow_action ) || Display::display_404_error ();
	$deal_user_id = abs ( intval ( $_URL [4] ) );
	if (! IS_FOUNDER) {
		// admin
		$member_cache_data = $Object_filecache->get ( MANAGER_APP );
		if ($deal_user_id != $_USER ['user_id']) {
			if (($deal_user_id == FOUNDER_ID) || in_array ( $deal_user_id, $member_cache_data ['admin_ids'] )) {
				$alert_info = "对该用户你没有" . $allow_action [$_URL [3]] . '权限';
				break;
			}
		}
	} elseif ($deal_user_id == FOUNDER_ID) {
		$alert_info = "对创始人你没有" . $allow_action [$_URL [3]] . '权限';
		break;
	}
	switch ($_URL [3]) {
		case 'del' :
			$Object_user->delet_user ( $deal_user_id );
			break;
		case 'shield' :
			$Object_user->shield_user ( $deal_user_id );
			break;
		case 'normal' :
			$Object_user->normal_user($deal_user_id);
			break;
	}
	break;
}
//屏蔽
$all_about_num = $Object_user->get_all_user_num ()-1;
require_once LIB_URL . 'class_system.php';
$star_num = System::get_page_star_num ( $page, 10, $all_about_num );
$page_html = System::get_page_html ( $page, 10, $all_about_num, $Object_url->mk_url ( array ('admin', 'member', 'show_all_member' ) ) . '?page=' . $page );
$all_about = $Object_user->get_user_by_limit ( $star_num, 10 );
$Object_template->assign ( array ('alert_info' => $alert_info, 'page' => $page, 'users' => $all_about, 'all_user_num' => $all_about_num, 'page_html' => $page_html ) );
$Object_template->display ( MANAGER_APP . '/admin/member', 0 );