<?php
defined ( 'IS_ME' ) or exit ();
$Object_template->assign(array('type'=>$app_cache['type']));
$Object_template->display ( APP . '/add' );