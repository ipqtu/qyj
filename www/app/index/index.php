<?php
defined ( 'IS_ME' ) or exit ();
$Object_template->assign ( array ('title' => '首页' ) );
$Object_template->display ( 'index/index' );