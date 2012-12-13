<?php
require_once LIB_URL . 'class_file.php';
$all_app_dir = File::list_files ( APP_URL, 1 );
$i = 0;
foreach ( $all_app_dir as $one_app_dir ) {
	if (file_exists ( $one_app_dir . 'state.php' )) {
		$app_info = include_once $one_app_dir . 'state.php';
		$system_config ['app_describe'] [$app_info ['app']] = $app_info ['describe'];
		$system_config ['all_app'] [$app_info ['app']] = $app_info ['app_name'];
		$system_config ['all_app_author'] [$app_info ['app']] = $app_info ['author'];
		if ($app_info ['allow_close']) {
			$system_config ['allow_close_app'] [$app_info ['app']] = $i;
			$system_config ['allow_close_app_state'] [$app_info ['app']] = $app_info ['defult_state'];
		}
		($app_info ['defult_state']) && $system_config ['open_app'] [$i] = $app_info ['app'];
	}
	$i++;
}
$Object_filecache->add ( array ('app_describe' => $system_config ['app_describe'], 'allow_close_app_state' => $system_config ['allow_close_app_state'], 'all_app' => $system_config ['all_app'], 'all_app_author' => $system_config ['all_app_author'], 'allow_close_app' => $system_config ['allow_close_app'], 'open_app' => $system_config ['open_app'] ), 'system' );
echo '<script  type="text/javascript">alert("更新完毕");window.top.frames["manFrame"].location ="' . $Object_url->mk_url ( array ('admin', 'system', 'app' ) ) . '"</script>';
exit ();