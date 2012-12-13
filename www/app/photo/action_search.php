<?php
defined ( 'IS_ME' ) or exit ();
empty ( $_POST ) && Display::display_404_error ();
empty ( $_POST ['content'] ) && Display::load_url ();
$type = $Object_filter->get_abs_int ( $_POST ['type'] ) % 2;
$photos = ($type == 0) ? $photo_object->get_all_photo_by_search_author ( $_POST ['content'], 0, 100 ) : $photo_object->get_all_photo_by_search_name ( $_POST ['content'], 0, 100 );
$Object_template->assign ( array ('photos' => $photos ,'type'=>3,'title'=>$title.'"'.$_POST ['content'].'"查询结果') );
$Object_template->display ( 'photo/search' );