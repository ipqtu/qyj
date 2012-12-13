<?php
require_once LIB_URL . 'class_table.php';

class Contact extends Table {
	
	public function __construct() {
		parent::__construct ( 'contact_us' );
	}
}