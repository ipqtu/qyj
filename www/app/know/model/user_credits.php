<?php
require_once LIB_URL . 'class_table.php';

class User_credits extends Table {
	
	public function __construct() {
		parent::__construct ( 'know_credits' );
	}
	
	public function get_spend_credits_by_month($uid) {
		$time = strtotime ( 'next month' );
		return $this->get_value_by_where ( array ('uid' => $uid, 'ctime' > $time ) );
	}
	
	public function get_add_credits_by_month($uid) {
		$time = strtotime ( 'next month' );
		return $this->get_value_by_where ( array ('to_uid' => $uid, 'ctime' > $time ) );
	}
}