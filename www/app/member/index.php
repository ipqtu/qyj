<?php
defined ( 'IS_ME' ) or exit ();
$action_array = array ('find_password','send_message','add_friends','friends','message','password','look', 'login', 'logout', 'update', 'me', 'regist', 'activate', 'thirdlogin', 'binding', 'bindingh','avatar');
$action = isset ( $_URL [1] ) ? $_URL [1] : 'me';
(in_array ( $action, $action_array )) || Display::display_404_error ();
$action_file_name = APP_URL . $APP . '/action/' . $action . '.php';
file_exists ( $action_file_name ) || Display::display_404_error ();
$language = include LANGUAGE_URL . $APP . '_language.php';
$Object_template->assign ( array ('lang' => $language ) );
include $action_file_name;