<?php
defined ( 'IS_ME' ) or exit ();
$page = isset ( $_GET ['page'] ) ? $_GET ['page'] : 0;
$all_about = "";
if (! empty ( $_POST ['search_content'] )) {
	switch ($_POST ['search_method']) {
		case 0 :
			$all_about = $Object_user->search_user_by_user_name ( $_POST ['search_content'] );
			break;
		case 1 :
			$all_about = $Object_user->get_user_by ( 'id', $_POST ['search_contnt'] );
	}
}
$Object_template->assign ( array ('users' => $all_about ) );
$Object_template->display ( MANAGER_APP . '/admin/search_member', 0 );