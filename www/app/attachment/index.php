<?php
defined ( 'IS_ME' ) or exit ();
//判断action
$action_array = array ('edit_upload', 'edit_imageManager', 'edit_getRemoteImage' );
$action = isset ( $_URL [1] ) ? $_URL [1] : 'index';
(in_array ( $action, $action_array )) || Display::display_404_error ();
//加载action
$action_file_name = APP_URL . $APP . '/action_' . $action . '.php';
file_exists ( $action_file_name ) || Display::display_404_error ();

$title = (isset ( $_USER ['user_name'] ) ? $_USER ['user_name'] : "") . "附件";
//加载app的action处理文件
include $action_file_name;