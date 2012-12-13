<?php
defined ( 'IS_ME' ) or exit ();
require_once APP_URL.APP.'/model/user_class.php';
$know_user_info_object = new User_info ();
$user_credits = $know_user_info_object->get_user_info ( $_USER ['user_id'], 'credits' );
$Object_template->assign ( array ('app_cache' => $app_cache));
$Object_template->display ( APP . '/my_credite' );