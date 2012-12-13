<?php
defined ( 'IS_ME' ) or exit ();
$Object_template->assign ( array ('title' => '信息', 'images_url' => '/images/error/') );
$Object_template->display ( 'display/nologin',0 );