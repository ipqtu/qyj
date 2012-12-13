<?php
defined ( 'IS_ME' ) or exit ();
$uid = $_USER['user_id'];
require_once LIB_URL . 'class_file.php';
$Object_file = new File ();
$uplaod_result = $Object_file->file_upload ( $_FILES ['file'], array ('zip', 'rar' ), '', APP, $uid );
if ($uplaod_result ['result']) {
	$return_array = array ('filelink' => str_replace ( WWW_URL, "/", $uplaod_result ['file'] ), 'filename' => '附件(点击下载)' );
} else {
	$return_array = array ('error' => $uplaod_result ['error'] );
}
require_once LIB_URL . 'class_json.php';
$Object_json = new Services_JSON ();
exit ( $Object_json->encode ( $return_array ) );