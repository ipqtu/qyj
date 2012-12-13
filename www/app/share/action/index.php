<?php
defined ( 'IS_ME' ) or exit ();
$type = isset ( $_GET [1] ) ? $Object_filter->get_abs_int ( $_GET [1] ) % count ( $app_cache ['share_type'] ) : 0;
$all_share = $app_object->get_share_by_type ( $type, 0, 20 );
$Object_template->assign ( array ('title' => $title, 'all_share' => $all_share ) );
$Object_template->display ( $APP . '/index' );