<?php
defined ( 'IS_ME' ) or exit ();
$Object_user->is_login () || Display::display_back ( $language ['not_login'] );
empty ( $_FILES ) && Display::display_404_error ();
require_once LIB_URL . 'class_file.php';
$upload_result = File::image_upload ( $_FILES ['photo'], array ('jpg', 'jpeg', 'png' ) );
if ($upload_result ['result']) {
	$user_name = $Object_user->get_current_user_base_info ( 'user_name' );
	$user_id = $Object_user->get_current_user_base_info ( 'id' );
	$_POST ['type'] = (isset ( $_POST ['type'] )) ? abs ( intval ( $_POST ['type'] ) ) % count ( $app_cache ['photo_type'] ) : 0;
	$type_name = $app_cache ['photo_type'] [$_POST ['type']];
	$reslut = $Object_mysql->insert ( $photo_object->get_photo_table_name (), array ('photo_name' => $_POST ['title'], 'photo_author_id' => $user_id, 'photo_author' => $user_name, 'photo_type' => $_POST ['type'], 'photo_type_name' => $type_name, 'photo_content' => $_POST ['describe'], 'photo_ctime' => time (), 'photo_url' => str_replace ( UPLOAD_URL, "", $upload_result ['file'] ), 'call_num' => 0, 'interest_num' => 0, 'check' => 1 ) );
	$reslut > 0 || Display::display_back ( $language ['upload_fail'] );
	$action_id = $Object_mysql->get_insert_id ();
	require_once LIB_URL . 'class_image.php';
	$image_object = new Image ();
	list ( $width, $height, $type, $attr ) = getimagesize ( $upload_result ['file'] );
	$image_object->image_resize ( $upload_result ['file'], 70, 70, false, 1, null, 100 );
	$image_object->image_resize ( $upload_result ['file'], 140, 140, false, 2, null, 100 );
	$image_object->image_resize ( $upload_result ['file'], 280, 280, false, 3, null, 100 );
	if ($width < 900 && $height < 900) {
		rename ( $upload_result ['file'], File::get_image_name ( $upload_result ['file'], 4, null, 100 ) );
	} else {
		$image_object->image_resize ( $upload_result ['file'], 900, 900, false, 4 );
		unlink ( $upload_result ['file'] );
	}
	Display::load_url ( $Object_url->mk_url ( array ('photo', 'photo', $action_id ) ) );
}
Display::display_back ( $upload_result ['error'] );