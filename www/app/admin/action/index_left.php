<?php
defined ( 'IS_ME' ) or exit ();
$nav = $Object_user->is_founder() ? $app_cache['founder_nav'] : $app_cache['admin_nav'];
$Object_template->assign(array('nav'=>$nav,'app_manager_title'=>$app_cache['app_manager_title'],'all_app'=>$app_cache['all_app']));
$Object_template->display(APP.'/left',0);