<?php
require_once LIB_URL . 'class_table.php';

class Question extends Table {
	
	public function __construct() {
		parent::__construct ( 'know_question' );
	}
}