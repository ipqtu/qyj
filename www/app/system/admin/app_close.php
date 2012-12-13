<?php
defined ( 'IS_ME' ) or exit ();
(isset($_URL[3])) || Display::display_404_error();
$open_app = $_URL[3];
array_key_exists($open_app, $system_config['allow_close_app_state']) || Display::display_404_error();
$system_config['allow_close_app_state'][$open_app] = 0;
$app_id = $system_config['allow_close_app'][$open_app];
unset($system_config['open_app'][$app_id]);
$Object_filecache->add(array('open_app'=>$system_config['open_app'],'allow_close_app_state'=>$system_config['allow_close_app_state']),MANAGER_APP);
include_once 'app.php';