<?php
IS_FOUNDER or die ();
require_once LIB_URL . 'class_file.php';
$all_app_dir = File::list_files ( APP_URL, 1 );
$all_app_mangaer_action = $all_app = $app_manger_title = $founder_nav = array ();
foreach ( $all_app_dir as $one_app_dir ) {
	if (file_exists ( $one_app_dir . 'state.php' )) {
		$app_info = include_once $one_app_dir . 'state.php';
		$Object_filecache->add(array($app_info ['app']=>$app_info ['manager_action']),APP);
		$all_app [$app_info ['app']] = $app_info ['app_name'];
		$app_manager_title [$app_info ['app']] = $app_info ['manager_app_title'];
		$founder_nav[$app_info ['app']] = $app_info ['manager_action'];
	}
}
$Object_filecache->add ( array ('founder_nav'=>$founder_nav, 'all_app' => $all_app, 'app_manager_title' => $app_manager_title ) ,APP);
echo '<script  type="text/javascript">alert("更新完毕");window.top.frames["manFrame"].location ="'.$Object_url->mk_url(array('admin','index_main')).'"</script>';
exit ();