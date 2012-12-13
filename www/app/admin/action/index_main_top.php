<?php
defined ( 'IS_ME' ) or exit ();
$Object_template->assign(array('app_manager_title'=>$app_cache['app_manager_title'])); 
$Object_template->display(APP.'/mainframe',0);