<?php
defined ( 'IS_ME' ) or exit ();
$uid = $_USER['user_id'];
require_once LIB_URL . 'class_file.php';
$Object_file = new File ();
if (! empty ( $uid )) {
	$uid = abs ( intval ( $uid ) );
	$uid = sprintf ( "%09d", $uid );
	$dir1 = substr ( $uid, 0, 3 );
	$dir2 = substr ( $uid, 3, 2 );
	$dir3 = substr ( $uid, 5, 2 );
	$dir4 = substr ( $uid, - 2 );
	$all_images_url = $Object_file->list_files ( WWW_URL . 'upload/' . APP . "/images/$dir1/$dir2/$dir3/$dir4/" );
} else {
	$all_images_url = $Object_file->list_files ( WWW_URL . 'upload/' . APP . "/images/" );
}
$json_image_url = array ();
$dir = realpath ( WWW_URL );
$i = 0;
foreach ( $all_images_url as $image_url ) {
	$pos = strpos ( $image_url, "-m" );
	if ($pos !== false) {
		$json_image_url [$i] ['thumb'] = str_replace ( $dir, '', $image_url );
		$json_image_url [$i] ['image'] = str_replace ( '-m', '', $json_image_url [$i] ['thumb'] );
		$i ++;
	}
}
require_once LIB_URL . 'class_json.php';
$json_objetc = new Services_JSON ();
exit ( $json_objetc->encode ( $json_image_url ) );