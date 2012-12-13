<?php
defined ( 'IS_ME' ) or exit ();
$Object_template->assign(array('types'=>$app_cache ['type']));
$Object_template->display ( APP . '/index' );