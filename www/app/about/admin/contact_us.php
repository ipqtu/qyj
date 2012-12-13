<?php
defined ( 'IS_ME' ) or exit ();
require_once APP_URL . MANAGER_APP . '/model/contact_class.php';
$contact_object = new Contact ();
$page = (isset ( $_GET ['page'] )) ? abs ( intval ( $_GET ['page'] ) ) : 0;
$all_num = $contact_object->get_table_all_num ( array ('replay_fid' => 0 ) );
$star_num = System::get_page_star_num ( $page, 10, $all_num );
$page_html = System::get_page_html ( $page, 10, $all_num, $Object_url->get_url () );

$all_contact = $contact_object->get_value_by_where ( array ('replay_fid' => 0 ), 'ctime', 0, 10 );
$replay_contact = array ();
foreach ( $all_contact as $contact ) {
	$replay_contact [$contact->id] = $contact_object->get_value_by_field ( 'replay_fid', $contact->id, 'floor' );
}
$Object_template->assign ( array ('page_html'=>$page_html,'all_contact' => $all_contact, 'replay_contact' => $replay_contact ) );
$Object_template->display ( MANAGER_APP . '/admin/contact_show' );
