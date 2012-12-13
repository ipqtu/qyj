<?php
ob_start ();

//设置网页编码
@header ( 'Content-type:text/html;charset=utf-8' );

//定义路径
define ( 'IS_ME', true );
define ( 'ROOT', realpath ( dirname ( __FILE__ ) . '/../' ) );
define ( 'APP_URL', ROOT . '/www/app/' );
define ( 'DATA_URL', ROOT . '/data/' );
define ( 'LANGUAGE_URL', DATA_URL . '/language/' );
define ( 'LIB_URL', ROOT . '/lib/' );
define ( 'MODEL_URL', ROOT . '/model/' );
define ( 'PLUGIN_URL', ROOT . '/plugins/' );
define ( 'TEMPLATE_URL', ROOT . '/template/' );
define ( 'CACHE_URL', DATA_URL . 'cache/' );
define ( 'CREATE_TEMPLATE_URL', DATA_URL . 'template/' );
define ( 'WWW_URL', ROOT . '/www/' );
//设置错误显示
//error_reporting ( 0 );
//加载系统函数


require_once LIB_URL . 'class_system.php';

//加载错误信息处理类
require_once LIB_URL . 'class_display.php';

//加载配置文件
require_once DATA_URL . 'config.php';

//加载系统缓存类
require_once LIB_URL . 'class_cache.php';
$Object_cache = new Cache ();

//加载文件缓存类
require_once LIB_URL . 'class_filecache.php';
$Object_filecache = new File_Cache ( DATA_URL . 'cache/' );
$system_config = $Object_filecache->get ();
define ( "SITE_NAME", $system_config ['site_name'] );
//加载url解析路由
require_once LIB_URL . 'class_url.php';
$Object_url = new Url ( $system_config ['url_method'] );
$_URL = $Object_url->get_query_data ();
define ( 'SITE_URL', $Object_url->get_site_url () );
define ( 'SITECOOKIEPATH', preg_replace ( '|https?://[^/]+|i', '', SITE_URL ) );
//加载数据过滤
require_once LIB_URL . 'class_filter.php';
$Object_filter = new Filter ();

//设置时区
date_default_timezone_set ( $system_config ['time_zone'] );

//初始化数据库
require_once LIB_URL . 'class_mysql.php';
$Object_mysql = new Mysql ( DB_HOST, DB_USER, DB_PASS, DB_NAME, DB_CHAR, DB_PER, ADMIN_EMAIL, DB_BUG );

//加载用户
require_once LIB_URL . 'class_user.php';
$Object_user = new User ( SITE_URL, $system_config ['admin_ids'] );

//加载模板引擎
require_once LIB_URL . 'class_template.php';
$Object_template = new Template ( TEMPLATE_URL, CREATE_TEMPLATE_URL, "%{", "}%", 'html' );

//加载消息盒子
require_once LIB_URL . 'class_message_box.php';
$Object_message_box = new MessageBox ();

//加载日志log


//加载seo
//$Object_template->assign(array('app_title'=>$system_cache));


//定义公共数据路径
define ( 'PUBLIC_JS_URL', '/js/public/' );

//加载app
$APP = isset ( $_URL [0] ) ? ((in_array ( $_URL [0], $system_config ['open_app'] )) ? $_URL [0] : "index") : "index";
$app_index_file = realpath ( APP_URL . $APP . '/index.php' );

if (file_exists ( $app_index_file )) {
	//app存在
	define ( 'APP', $APP );
	define ( 'CSS_URL', '/css/' . $APP . '/' );
	define ( 'IMAGES_URL', '/images/' . $APP . '/' );
	define ( 'JS_URL', '/js/' . $APP . '/' );
	define ( 'HTML_UPLOAD_URL', '/upload/' . $APP . '/' );
	define ( 'UPLOAD_URL', ROOT . '/www/upload/' . $APP . '/' );
	set_include_path ( get_include_path () . ";" . APP_URL . APP . '/' );
	//加载app 缓存数据
	$app_cache = $Object_filecache->get ( $APP );
	//加载语言包
	file_exists ( LANGUAGE_URL . $APP . '_language.php' ) && $app_language = include LANGUAGE_URL . $APP . '_language.php';
	$Object_template->assign ( array ('lang' => $app_language ) );
	//判断是否登录
	$regist_url = $Object_url->mk_url ( array ('member', 'regist' ) );
	$login_url = $Object_url->mk_url ( array ('member', 'login' ) );
	$_USER = array ('user_new_message_num' => 0, 'user_avatar' => "/images/member/noavatar_", 'user_login' => 0, 'user_regist_url' => $regist_url, 'user_login_url' => $login_url, 'user_id' => 0 );
	if ($Object_user->is_login ()) {
		$_USER ['user_login'] = 1;
		$_USER ['user_name'] = $Object_user->get_current_user_base_info ( 'user_name' );
		$_USER ['user_id'] = $Object_user->get_current_user_base_info ( 'id' );
		$_USER ['user_avatar'] = $Object_user->get_current_user_append_info ( 'user_avatar' );
		(empty ( $_USER ['user_avatar'] )) && $_USER ['user_avatar'] = "/images/member/noavatar_";
		$_USER ['user_logout_url'] = $Object_url->mk_url ( array ('member', 'logout' ) );
		$_USER ['user_my_url'] = $Object_url->mk_url ( array ('member', 'me', $_USER ['user_id'] ) );
		$_USER ['user_new_message_num'] = $Object_message_box->get_user_new_message_num ( $_USER ['user_id'] );
	}
	$Object_template->assign ( array ('_USER' => $_USER ) );
	include_once $app_index_file;
} else {
	//app不存在调用错误机制
	Display::display_404_error ();
}
exit ();