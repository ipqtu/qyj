<?php
require_once LIB_URL . 'class_table.php';

class Answer extends Table {
	
	public function __construct() {
		parent::__construct ( 'know_answer' );
	}
}