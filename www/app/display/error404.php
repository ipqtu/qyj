<?php
defined ( 'IS_ME' ) or exit ();
$Object_template->assign ( array ('images_url' => '/images/error/' ) );
$Object_template->display ( 'display/error404',0 );