<?php
defined ( 'IS_ME' ) or exit ();
($Object_user->is_login ()) || Display::display_nologin();
require_once APP_URL.'/'.APP.'/model/friends_class.php';
$firends_object = new Friends ();
$user_friends = $firends_object->list_user_friends ( $_USER ['user_id'], 0, 100 );
$Object_template->assign ( $_USER );
$Object_template->assign ( array ('type' => 3, 'user_friends' => $user_friends ) );
$Object_template->display ( $APP . '/friends' );