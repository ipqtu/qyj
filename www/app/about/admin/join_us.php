<?php
defined ( 'IS_ME' ) or exit ();
$alert_info = "";
if (! empty ( $_POST )) {
	$join_us = $product_content = $Object_filter->filter_edit_content($Object_filter->get_real_gpc_var('about'));
	$Object_filecache->add ( array ('join_us' => $join_us ), MANAGER_APP );
	$alert_info = "修改成功";
}
$manager_app_cache = $Object_filecache->get(MANAGER_APP);
require_once APP_URL.'edit/model/edit_class.php';
$Object_template->assign ( array ('edit_html'=>Edit::create_eedit('about'),'alert_info'=>$alert_info,'join_us' => $manager_app_cache ['join_us'] ) );
$Object_template->display ( MANAGER_APP . '/admin/join_us', 0 );