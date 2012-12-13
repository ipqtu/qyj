<?php
defined ( 'IS_ME' ) or exit ();
$alert_info = "";
$manager_app_cache = $Object_filecache->get ( MANAGER_APP );
$edit_product = array ();
$edit_profuct_id = 0;
if (isset ( $_GET ['edit'] )) {
	$edit_profuct_id = abs ( intval ( $_GET ['edit'] ) );
	if (isset ( $manager_app_cache ['about_product'] [$edit_profuct_id] )) {
		if (! empty ( $_POST )) {
			$product_name = $_POST ['product_name'];
			$product_content = $Object_filter->filter_edit_content($Object_filter->get_real_gpc_var('product_content'));
			$manager_app_cache ['about_product'] [$edit_profuct_id] = array ('product_name' => $product_name, 'product_content' => $product_content );
			$Object_filecache->add ( array ('about_product' => $manager_app_cache ['about_product'] ), MANAGER_APP );
			$alert_info = "修改成功";
		} else {
			$edit_product = $manager_app_cache ['about_product'] [$edit_profuct_id];
		}
	}
} else {
	if (! empty ( $_POST )) {
		$product_name = $_POST ['product_name'];
		$product_content = $Object_filter->filter_edit_content($Object_filter->get_real_gpc_var('product_content'));
		$manager_app_cache ['about_product'] [] = array ('product_name' => $product_name, 'product_content' => $product_content );
		$Object_filecache->add ( array ('about_product' => $manager_app_cache ['about_product'] ), MANAGER_APP );
		$alert_info = "添加成功";
	}
}
require_once APP_URL.'edit/model/edit_class.php';
$Object_template->assign ( array ('edit_html'=>Edit::create_eedit('product_content'),'edit_profuct_id' => $edit_profuct_id, 'edit_product' => $edit_product, 'alert_info' => $alert_info, 'about_product' => $manager_app_cache ['about_product'] ) );
$Object_template->display ( MANAGER_APP . '/admin/about_product', 0 );