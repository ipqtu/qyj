<?php
require_once LIB_URL . 'class_table.php';

class Leave extends Table {
	
	public function __construct() {
		parent::__construct ( 'bk_leave' );
	}
}