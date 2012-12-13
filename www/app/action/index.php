<?php
defined ( 'IS_ME' ) or exit ();
//判断action
$action_array = array ('index', 'add', 'display', 'like', 'me','look','manager','down','publisher','user','delect','edit_image_upload','edit_file_upload','edit_show_upload_image');
$action = isset ( $_URL [1] ) ? $_URL [1] : 'index';
(in_array ( $action, $action_array )) || Display::display_404_error ();
//加载action
$action_file_name = APP_URL . $APP . '/action/' . $action . '.php';
file_exists ( $action_file_name ) || Display::display_404_error ();
//加载app类
require_once 'model/action_class.php';
$action_object = new Action ();
//加载app的action处理文件
include $action_file_name;