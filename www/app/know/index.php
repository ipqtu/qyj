<?php
defined ( 'IS_ME' ) or exit ();
//判断action
$action_array = array ('index', 'public', 'type', 'question', 'my', 'my_question' ,'my_answer','my_credite','search');
$action = isset ( $_URL [1] ) ? $_URL [1] : 'index';
(in_array ( $action, $action_array )) || Display::display_404_error ();
//加载action
$action_file_name = APP_URL . $APP . '/action/' . $action . '.php';
file_exists ( $action_file_name ) || Display::display_404_error ();
//基本信息加载
$title = "趣友街问问-";
//加载app的action处理文件
include $action_file_name;