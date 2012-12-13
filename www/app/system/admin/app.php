<?php
defined ( 'IS_ME' ) or exit ();
$Object_template->assign ( array ('all_app'=>$system_config['all_app'],'apps_state' => $system_config ['allow_close_app_state'],'apps_describe'=>$system_config['app_describe']) );
$Object_template->display ( MANAGER_APP . '/app', 0 );