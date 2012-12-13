<?php
defined ( 'IS_ME' ) or exit ();
isset ( $_URL [2] ) || Display::display_dialog ( '你访问的信息不存在' );
$about_id = abs ( intval ( $_URL [2] ) );
$about_info = $about_object->get_value_by_field ( 'id', $about_id );
(empty ( $about_info [0] )) && Display::display_dialog ( '你访问的信息不存在' );
$about_object->auto_add_field ( 'call_num', array ('id' => $about_id ) );
$Object_template->assign ( array ('about_info' => $about_info [0] ) );
$Object_template->display ( TEMPLATE_URL . 'article' );