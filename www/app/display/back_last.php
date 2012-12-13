<?php
defined ( 'IS_ME' ) or exit ();
$Object_template->assign ( array ('title' => '提示信息', 'images_url' => '/images/error/', 'message' => $message, 'url' => $Object_url->get_last_url () ) );
$Object_template->display ( 'display/back_last',0 );