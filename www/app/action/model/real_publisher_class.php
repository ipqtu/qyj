<?php
class Real_publisher {
	
	private $db;
	private $filter;
	private $publishers = array ();
	private $db_table_name = "action_real_publisher";
	
	public function __construct() {
		$this->db = Mysql::get_object ();
		$this->filter = Filter::get_object ();
		$this->db_table_name = $this->db->prefix . $this->db_table_name;
	}
	
	public function get_real_publisher_info($user_id) {
		$sql = "SELECT * FROM " . $this->db_table_name . ' WHERE `user_id` = %d';
		return $this->db->get_row ( $this->db->prepare ( $sql, $user_id ) );
	}
}