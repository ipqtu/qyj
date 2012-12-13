<?php
defined ( 'IS_ME' ) or exit ();
$cur_key = isset ( $_URL [2] ) ? abs ( intval ( $_URL [2] ) ) : 0;
if (key_exists ( $cur_key ,$app_cache ['about_product'])) {
	$cur_value = $app_cache ['about_product'] [$cur_key];
}else{
	$cur_value = array_pop($cur_value);
	$keys = key($app_cache['about_product']);
	$cur_key = array_pop($keys);
}
$Object_template->assign ( array ('cur_value'=>$cur_value,'type' => 1, 'about_product' => $app_cache ['about_product'], 'title' => '趣友街我们的产品', 'cur_key' => $cur_key ) );
$Object_template->display ( APP . '/product' );