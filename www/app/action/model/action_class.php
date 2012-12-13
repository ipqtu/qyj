<?php
class Action {
	
	private $db;
	private $filter;
	private $table_name = "action";
	
	public function __construct() {
		$this->db = Mysql::get_object ();
		$this->filter = Filter::get_object ();
		$this->table_name = $this->db->prefix . $this->table_name;
	}
	
	public function get_db_table_name() {
		return $this->table_name;
	}
	
	public function get_one_action($action_id) {
		$action_id = $this->filter->get_abs_int ( $action_id );
		$sql = "SELECT * FROM `" . $this->table_name . '` WHERE `id`=' . $action_id;
		return $this->db->get_row ( $sql );
	}
	
	public function add_call_num($action_id) {
		$action_id = $this->filter->get_abs_int ( $action_id );
		$sql = "UPDATE `" . $this->table_name . '` SET `call_num` = `call_num`+1 WHERE `id`=' . $action_id;
		$this->db->query ( $sql );
	}
	
	public function add_like_num($action_id) {
		$action_id = $this->filter->get_abs_int ( $action_id );
		$sql = "UPDATE `" . $this->table_name . '` SET `interest_num` = `interest_num`+1 WHERE `id`=' . $action_id;
		$this->db->query ( $sql );
	}
	
	public function add_join_num($action_id) {
		$action_id = $this->filter->get_abs_int ( $action_id );
		$sql = "UPDATE `" . $this->table_name . '` SET `join_num` = `join_num`+1 WHERE `id`=' . $action_id;
		$this->db->query ( $sql );
	}
	
	public function add_leave_num($action_id) {
		$action_id = $this->filter->get_abs_int ( $action_id );
		$sql = "UPDATE `" . $this->table_name . '` SET `leave_num` = `call_num`+1 WHERE `id`=' . $action_id;
		$this->db->query ( $sql );
	}
	
	/**
	 * 活动到期结束活动
	 * Enter description here ...
	 * @param unknown_type $action_id
	 */
	public function over_action($action_id) {
		$this->db->update ( $this->table_name, array ('is_over' => 1 ), array ('id' => $action_id ), array ('%d' ), array ('%d' ) );
	}
	
	public function get_all_action_by_limit($star_num, $end_num, $admin = 0) {
		$check_str = ($admin == 0) ? 'WHERE `check`= 1 AND `action_end_time` >' . time () : '';
		$sql = "SELECT * FROM `" . $this->table_name . '` ' . $check_str . ' ORDER BY `action_ctime` DESC LIMIT %d,%d';
		return $this->db->get_results ( $this->db->prepare ( $sql, $star_num, $end_num ) );
	}
	
	public function get_all_action_by_type($type, $star_num, $end_num, $admin = 0) {
		$check_str = ($admin == 0) ? 'WHERE `check`= 1 AND `action_end_time` >' . time () . ' AND ' : 'WHERE ';
		$sql = "SELECT * FROM `" . $this->table_name . '` ' . $check_str . '`action_type_id`=%d ORDER BY `action_ctime` DESC LIMIT %d,%d';
		return $this->db->get_results ( $this->db->prepare ( $sql, $type, $star_num, $end_num ) );
	}
	
	public function get_action_by_ids_str($ids_str, $star_num, $end_num) {
		$sql = "SELECT * FROM " . $this->table_name . ' WHERE `id` IN (' . $ids_str . ') ORDER BY `action_star_time` DESC LIMIT %d,%d';
		return $this->db->get_results ( $this->db->prepare ( $sql, array ($star_num, $end_num ) ) );
	}
	
	public function get_action_by_where($where_str, $star_num, $end_num) {
		$sql = "SELECT * FROM `" . $this->table_name . '` WHERE `check` = 1 AND `is_over` = 0 ' . $where_str . " ORDER BY `action_ctime` LIMIT {$star_num},{$end_num}";
		return $this->db->get_results ( $sql );
	}
	
	public function delect_action_by_ids_str($id_str) {
		$sql = "DELETE FROM `" . $this->table_name . '` WHERE `id` IN (' . $id_str . ')';
		$this->db->query ( $sql );
	}

}