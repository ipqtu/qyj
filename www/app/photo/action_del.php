<?php
defined ( 'IS_ME' ) or exit ();
isset ( $_URL [2] ) || Display::display_404_error ();
$action_id = $Object_filter->get_abs_int ( $_URL [2] );
($action_id > 0) || Display::display_404_error ();
$action_info = $photo_object->get_one_photo ( $action_id );
(! empty ( $action_info )) || Display::display_404_error ();
($Object_user->is_login ()) || Display::display_404_error ();
($action_info->photo_author_id == $_USER['user_id']) || Display::display_404_error ();
echo $action_info->id;
$Object_mysql->delete ( $photo_object->get_photo_table_name (), array ('id' => $action_info->id ), array ("%d" ) );
//删除图片
require_once LIB_URL . 'class_file.php';
File::del_image ( $action_info->photo_url );
//删除留言
require_once 'photo_leave_class.php';
$action_leave_object = new Photo_leave ();
$Object_mysql->delete ( $action_leave_object->get_photo_leave_table_name (), array ('photo_id' => $action_info->id ) );
Display::load_url ();