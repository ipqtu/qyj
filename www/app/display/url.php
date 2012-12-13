<?php
defined ( 'IS_ME' ) or exit ();
$Object_template->assign ( array ('title' => '提示信息', 'images_url' => '/images/error/', 'message' => $message, 'url_message' => $url_message, 'url' => $url ) );
$Object_template->display ( 'display/url' ,0);