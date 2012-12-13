<?php
defined ( 'IS_ME' ) or exit ();
//样式
$style = array (array (4, 3, 3 ), array (3, 3, 4, 4 ), array (4, 4, 4, 4 ), array (3, 3, 3, 3, 4 ), array (4, 3, 3, 3, 3 ) );
//样式
$first_style = System::get_rand ( 1, (count ( $style ) - 1) );
//图片大小
$first_photo_size = $style [$first_style];
//图片数
$first_photo_num = count ( $first_photo_size );
//总共的图片数
$all_photo_num = $first_photo_num;
//开始的位置
$photo_star_num = $Object_filter->get_abs_int ( $_GET ['photo_star_num'] );
$photo_type = $Object_filter->get_abs_int ( $_GET ['photo_type'] );
//获取图片
$type = (isset ( $_URL [2] )) ? $photo_type % 5 : 2;
switch ($type) {
	case 0 :
		$photos = $photo_object->get_all_photo_by_interest_num ( $photo_star_num - 1, ($all_photo_num + 1) );
		break;
	case 1 :
		$photos = $photo_object->get_all_photo_by_time ( $photo_star_num - 1, ($all_photo_num + 1) );
		break;
	case 2 :
		$photos = $photo_object->get_all_photo_by_random ( $photo_star_num - 1, ($all_photo_num + 1) );
		break;
	case 3 :
		$photos = $photo_object->get_all_photo_by_interest_num_asc ( $photo_star_num - 1, ($all_photo_num + 1) );
		break;
	case 4 :
		$photos = $photo_object->get_all_photo_by_mm ( $photo_star_num - 1, ($all_photo_num + 1) );
		break;
}

//划分图片
$first_photos = array ();
require_once LIB_URL . 'class_file.php';
$i = 0;
foreach ( $photos as $v ) {
	$v->id = $Object_url->mk_url ( array ('photo', 'photo', $v->id ) );
	if ($i < $first_photo_num) {
		$photo_size = $first_photo_size [$i];
		$v->photo_url = File::get_image_name ( $v->photo_url, $photo_size );
		$first_photos [] = $v;
	}
	$i ++;
}

$data ['style'] = $first_style;
$data ['photos'] = $first_photos;
$Object_template->assign ( $data );
$json_array ['html'] = $Object_template->get_display_content ( $APP . '/style', 0 );
$json_array ['more_photos'] = ($i == ($all_photo_num + 1)) ? true : false;
$json_array ['photo_star_num'] = $all_photo_num + $photo_star_num;
require_once LIB_URL . 'class_json.php';
$json_object = new Services_JSON ();
echo $json_object->encode ( $json_array );
exit ();