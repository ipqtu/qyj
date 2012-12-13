<?php
class Photo_leave {
	
	private $db;
	private $filter;
	private $photo_leave_table_name = "photo_leave";
	
	public function __construct() {
		$this->db = Mysql::get_object ();
		$this->filter = Filter::get_object ();
		$this->photo_leave_table_name = $this->db->prefix . $this->photo_leave_table_name;
	}
	
	public function get_photo_leave_table_name() {
		return $this->photo_leave_table_name;
	}
	
	public function get_one_photo_leave($photo_id) {
		$photo_id = $this->filter->get_abs_int ( $photo_id );
		$sql = "SELECT * FROM `" . $this->photo_leave_table_name . '` WHERE `photo_id`=' . $photo_id . ' ORDER BY `photo_leave_ctime` ASC';
		return $this->db->get_results ( $sql, Mysql::$ARRAY_A );
	}
	
	
	public function get_one_leave($leave_id) {
		$sql = "SELECT * FROM `" . $this->photo_leave_table_name . '` WHERE `id`= %d ';
		return $this->db->get_row ( $this->db->prepare ( $sql, $leave_id ) );
	}

}