<?php
class Action_leave {
	
	private $db;
	private $filter;
	private $action_leave_table_name = "action_leave";
	
	public function __construct() {
		$this->db = Mysql::get_object ();
		$this->filter = Filter::get_object ();
		$this->action_leave_table_name = $this->db->prefix . $this->action_leave_table_name;
	}
	
	public function get_action_leave_table_name() {
		return $this->action_leave_table_name;
	}
	
	public function get_one_action_leave($action_id) {
		$action_id = $this->filter->get_abs_int ( $action_id );
		$sql = "SELECT * FROM `" . $this->action_leave_table_name . '` WHERE `action_id`=' . $action_id . ' ORDER BY `action_leave_ctime` ASC';
		return $this->db->get_results ( $sql, Mysql::$ARRAY_A );
	}
	
	public function get_one_leave($leave_id) {
		$sql = "SELECT * FROM `" . $this->action_leave_table_name . '` WHERE `id`= %d ';
		return $this->db->get_row ( $this->db->prepare ( $sql, $leave_id ) );
	}

	public function delect_leave_by_user_id($user_id){
		$sql = 'DELECT * FROM `'.$this->action_leave_table_name.'` WHERE `action_leave_author_id` = %d';
		$this->db->query($this->db->prepare($sql,$user_id));
	}
}