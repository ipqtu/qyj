<?php
defined ( 'IS_ME' ) or exit ();
//样式
$style = array (array (4, 3, 3 ), array (3, 3, 4, 4 ), array (4, 4, 4, 4 ), array (3, 3, 3, 3, 4 ), array (4, 3, 3, 3, 3 ) );
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
$type = (isset ( $_URL [2] )) ? $Object_filter->get_abs_int ( $_URL [2] ) % 5 : 2;
switch ($type) {
	case 0 :
		$photos = $photo_object->get_all_photo_by_interest_num ( 0, ($all_photo_num + 1) );
		break;
	case 1 :
		$photos = $photo_object->get_all_photo_by_time ( 0, ($all_photo_num + 1) );
		break;
	case 2 :
		$photos = $photo_object->get_all_photo_by_random ( 0, ($all_photo_num + 1) );
		break;
	case 3 :
		$photos = $photo_object->get_all_photo_by_interest_num_asc ( 0, ($all_photo_num + 1) );
		break;
	case 4 :
		$photos = $photo_object->get_all_photo_by_mm ( 0, ($all_photo_num + 1) );
		break;
}

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
$data ['more_photos'] = ($i == ($all_photo_num + 1)) ? true : false;
$data ['all_photo_num'] = $i;
$data ['first_style'] = $first_style;
$data ['first_photos'] = $first_photos;

$data ['seconde_style'] = $seconde_style;
$data ['seconde_photos'] = $seconde_photos;

$data ['third_style'] = $third_style;
$data ['third_photos'] = $third_photos;
$type_array = array('热门','最新','随机','冷门','女生');
$data ['title'] = $title . $type_array[$type] . '图片';
$data ['type'] = $type;
if (isset ( $_COOKIE ['is_first'] )) {
	$data ['is_first'] = false;
} else {
	$data ['is_first'] = true;
	setcookie ( 'is_first', "true", time () + 24 * 3600 );
}
$Object_template->assign ( $data );
$Object_template->display ( $APP . '/index' );