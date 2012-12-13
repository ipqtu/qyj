<?php
defined ( 'IS_ME' ) or exit ();
//判断action
$action_array = array ('search','xm','gd','index', 'user', 'upload', 'photo', 'type', 'index_ajax', 'like', 'edit', 'del','reward' );
$action = isset ( $_URL [1] ) ? $_URL [1] : 'index';
(in_array ( $action, $action_array )) || Display::display_404_error ();
//加载action
$action_file_name = APP_URL . $APP . '/action_' . $action . '.php';
file_exists ( $action_file_name ) || Display::display_404_error ();
//加载app类
require_once 'photo_class.php';
$photo_object = new Photo ();
//个人网站的个人用户中心
if (isset ( $_USER ['user_id'] )) {
	$Object_template->assign ( array ('user_my_app_url' => $Object_url->mk_url ( array ('photo', 'user', $_USER ['user_id'] ) ) ) );
}
$title="矿大春季图片展--";
//加载app的action处理文件
include $action_file_name;