<?php
defined ( 'IS_ME' ) or exit ();
//样式
$style = array (array (4, 3, 3 ), array (3, 3, 4, 4 ), array (4, 4, 4, 4 ) );
//3层的样式
$first_style = 0;
$seconde_style = System::get_rand ( 1, (count ( $style ) - 1) );
$third_style = System::get_rand ( 1, (count ( $style ) - 1) );
//3层的图片大小
$first_photo_size = $style [$first_style];
$seconde_photo_size = $style [$seconde_style];
$third_photo_size = $style [$third_style];
//3层的图片数
$first_photo_num = 3;
$seconde_photo_num = count ( $seconde_photo_size );
$third_photo_num = count ( $third_photo_size );
//总共的图片数
$all_photo_num = 3 + $seconde_photo_num + $third_photo_num;
//获取图片
$type = (isset ( $_URL [2] )) ? $Object_filter->get_abs_int ( $_URL [2] ) % count ( $app_cache ['photo_type'] ) : 0;
$photos = $photo_object->get_type_photo ( $type, 0, $all_photo_num );
//划分图片
$first_photos = $seconde_photos = $third_photos = array ();
require_once LIB_URL . 'class_file.php';
$i = 0;
foreach ( $photos as $v ) {
	$v->id = $Object_url->mk_url ( array ('photo', 'photo', $v->id ) );
	if ($i < $first_photo_num) {
		$photo_size = $first_photo_size [$i];
		$v->photo_url = File::get_image_name ( $v->photo_url, $photo_size );
		$first_photos [] = $v;
	} elseif ($i < ($first_photo_num + $seconde_photo_num)) {
		$photo_size = $seconde_photo_size [$i - $first_photo_num];
		$v->photo_url = File::get_image_name ( $v->photo_url, $photo_size );
		$seconde_photos [] = $v;
	} elseif ($i < $all_photo_num) {
		$photo_size = $third_photo_size [($i - $seconde_photo_num - $first_photo_num)];
		$v->photo_url = File::get_image_name ( $v->photo_url, $photo_size );
		$third_photos [] = $v;
	}
	$i ++;
}
$data ['first_style'] = $first_style;
$data ['first_photos'] = $first_photos;

$data ['seconde_style'] = $seconde_style;
$data ['seconde_photos'] = $seconde_photos;

$data ['third_style'] = $third_style;
$data ['third_photos'] = $third_photos;
$data ['photo_type'] = $app_cache ['photo_type'];

$data['title'] = $title.$app_cache ['photo_type'][$type].'图片';
$data ['type'] = $type;
$Object_template->assign ( $data );
$Object_template->display ( $APP . '/type' );