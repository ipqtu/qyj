<?php
defined ( 'IS_ME' ) or exit ();
isset ( $_URL [2] ) || Display::display_404_error ();
require_once 'model/thirdlogin_class.php';
$third_login_object = new Thirdlogin ( $Object_url->mk_url ( array ('member', 'thirdlogin', $_URL [2] ) ), $_URL [2], $Object_url->mk_url ( array ('member', 'login' ) ) );
$third_login_object->login () || Display::load_url ();
if ($third_login_object->check_binding ()) {
	$result = $Object_user->third_user_login ( $third_login_object->get_user_id () );
	Display::load_url ();
} else {
	session_start ();
	$binding_id = $third_login_object->get_binding_id ();
	($binding_id > 0) || Display::display_back ( $language ['link_fail'] );
	$_SESSION ['binding_id'] = $binding_id;
	$_SESSION ['binding_type'] = $third_login_object->get_binding_type ();
	Display::load_url ( $Object_url->mk_url ( array ('member', 'binding' ) ) );
}
exit ();