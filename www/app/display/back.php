<?php
defined ( 'IS_ME' ) or exit ();
$Object_template->assign ( array ('title' => '信息返回', 'images_url' => '/images/error/', 'message' => $message ) );
$Object_template->display ( 'display/back',0 );