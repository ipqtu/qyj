<?php
require_once LIB_URL . 'class_table.php';

class News extends Table {
	
	public function __construct() {
		parent::__construct ( 'us_news' );
	}
}