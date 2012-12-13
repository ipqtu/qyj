<?php
defined ( 'IS_ME' ) or exit ();
$first_id = 93;
$second_id = '212,321';
$third_id = '324,325,323';
require_once LIB_URL . 'class_file.php';
$first_photo = $photo_object->get_one_photo ( $first_id );
$first_photo->photo_url = File::get_image_name ( $first_photo->photo_url, 3 );

$second_photo = $photo_object->get_photo_by_ids ( $second_id );
$second_photo[0]->photo_url = File::get_image_name ( $second_photo[0]->photo_url, 3 );
$second_photo[1]->photo_url = File::get_image_name ( $second_photo[1]->photo_url, 3 );

$third_photo = $photo_object->get_photo_by_ids ( $third_id );
$third_photo[0]->photo_url = File::get_image_name ( $third_photo[0]->photo_url, 3 );
$third_photo[1]->photo_url = File::get_image_name ( $third_photo[1]->photo_url, 3 );
$third_photo[2]->photo_url = File::get_image_name ( $third_photo[2]->photo_url, 3 );

$Object_template->assign ( array ('title'=>$title.'获奖名单' , 'type' => 7,'first_photo'=>$first_photo,'second_photo'=>$second_photo,'third_photo'=>$third_photo));
$Object_template->display ( 'photo/reward');
?>