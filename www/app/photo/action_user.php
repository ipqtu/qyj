<?php
defined ( 'IS_ME' ) or exit ();
isset ( $_URL [2] ) || Display::display_404_error ();
$user_id = abs ( intval ( $_URL [2] ) );
$user_photos = $photo_object->get_user_all_photo ( $user_id, 0, 100 );
$author_info = $Object_user->get_user_by ( 'id', $user_id );
$data ['author_info'] = $author_info;
$data ['user_photos'] = $user_photos;
$data ['title'] = $title.$author_info['user_name'].'的图片';
$data ['type'] = -2;
$Object_template->assign ( $data );
$Object_template->display ( $APP . '/user' );

