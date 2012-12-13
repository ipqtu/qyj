<?php
defined ( 'IS_ME' ) or exit ();
($Object_user->is_login ()) || Display::display_nologin ();
require_once APP_URL . APP . '/model/user_class.php';
$know_user_info_object = new User_info ();
$user_info = $know_user_info_object->get_user_all_info ( $_USER ['user_id'] );
$Object_template->display ( APP.'/my' );