<?php
class Action_join {
	
	private $db;
	private $filter;
	private $table_name = "action_join";
	
	public function __construct() {
		$this->db = Mysql::get_object ();
		$this->filter = Filter::get_object ();
		$this->table_name = $this->db->prefix . $this->table_name;
	}
	
	public function get_table_name() {
		return $this->table_name;
	}
	
	public function get_user_join_num($user_id) {
		$sql = 'SELECT count(*) FROM ' . $this->table_name . ' WHERE `user_id` =%d GROUP BY `action_id`';
		return $this->db->get_var ( $this->db->prepare ( $sql, $user_id ) );
	}
	
	public function get_user_join_action_ids_str($user_id) {
		$sql = 'SELECT `action_id` FROM ' . $this->table_name . ' WHERE `user_id` = %d GROUP BY `action_id`';
		$reslut = $this->db->get_results ( $this->db->prepare ( $sql, $user_id ) );
		$actin_ids_str = "";
		foreach ( $reslut as $one ) {
			$actin_ids_str .= $one->action_id . ',';
		}
		return $actin_ids_str . '0';
	}
	
	public function get_action_join_info($action_id) {
		$sql = "SELECT * FROM " . $this->table_name . ' WHERE `action_id` = %d ';
		return $this->db->get_results ( $this->db->prepare ( $sql, $action_id ) );
	}
	
	public function delect_user_join_action($user_id) {
		$sql = "SELET FROM `" . $this->table_name . '` WHERE `user_id` = %d';
		$this->db->query ( $this->db->prepare ( $sql, $user_id ) );
	}
	
	public function delect_action_join($action_id){
		$sql = 'DELET FROM `'.$this->table_name.'` WHERE `action_id` = %d';
		$this->db->query($this->db->prepare($sql,$action_id));
	}

}