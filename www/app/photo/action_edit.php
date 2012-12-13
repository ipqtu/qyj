<?php
defined ( 'IS_ME' ) or exit ();
isset ( $_URL [2] ) || Display::display_404_error ();
$action_id = $Object_filter->get_abs_int ( $_URL [2] );
($action_id > 0) || Display::display_404_error ();
$action_info = $photo_object->get_one_photo ( $action_id );
(!empty ( $action_info )) || Display::display_404_error ();
($Object_user->is_login ()) || Display::display_404_error ();
($action_info->photo_author_id == $_USER['user_id']) || Display::display_404_error ();
$Object_mysql->update ( $photo_object->get_photo_table_name (), array ('photo_name' => $_POST ['name'], 'photo_content' => $_POST ['content'] ), array ('id' => $action_id ) );
Display::load_url ();