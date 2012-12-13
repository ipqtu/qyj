<?php
defined ( 'IS_ME' ) or exit ();
(count ( $_URL ) < 4) && Display::display_dialog ( $language ['user_activate_fail']);
$user_id = $Object_filter->get_abs_int ( $_URL [2] );
$activate_key = $_URL [3];
$user = $Object_user->get_user_by ( 'id', $user_id );
empty ( $user ) && Display::display_dialog ( $language ['user_activate_fail']);
($user ['user_status'] == 2) && Display::display_dialog ( $language ['user_has_activate'] );
($user ['user_activation_key'] == $activate_key) || Display::display_dialog ( $language ['user_activate_fail']);
//激活用户
$Object_user->update_user ( $user_id, array ('user_status' => 2, 'user_activation_key' => "" ), array ("%d", "%s" ) );
Display::display_dialog ( $language ['user_activate_success']);