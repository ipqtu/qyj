<?php
class Action_like {
	
	private $db;
	private $filter;
	private $table_name = "action_like";
	
	public function __construct() {
		$this->db = Mysql::get_object ();
		$this->filter = Filter::get_object ();
		$this->table_name = $this->db->prefix . $this->table_name;
	}
	
	public function get_photo_like_table_name() {
		return $this->table_name;
	}
	
	public function check_user_is_like($user_id, $action_id) {
		$action_id = $this->filter->get_abs_int ( $action_id );
		$sql = "SELECT * FROM `" . $this->table_name . '` WHERE `action_id`=' . $action_id . ' AND `user_id` = ' . $user_id;
		$result = $this->db->get_results ( $sql );
		if (empty ( $result )) {
			$this->db->insert ( $this->table_name, array ('action_id' => $action_id, 'user_id' => $user_id, 'ctime' => time () ) );
			return true;
		}
		return false;
	}
	
	public function get_user_like_num($user_id) {
		return $this->db->get_table_num ( $this->table_name, array ('user_id' => $user_id ), array ('%d' ) );
	}
	
	public function get_user_like_action_ids_str($user_id) {
		$sql = 'SELECT `action_id` FROM ' . $this->table_name . ' WHERE `user_id` = %d';
		$reslut = $this->db->get_results ( $this->db->prepare ( $sql, $user_id ) );
		$actin_ids_str = "";
		foreach ( $reslut as $one ) {
			$actin_ids_str .= $one->action_id . ',';
		}
		return $actin_ids_str . '0';
	}
	
	public function delect_like_by_action_ids($ids_str) {
		$sql = 'DELETE FROM `' . $this->table_name . '` WHERE `user_id` IN (' . $ids_str . ')';
		$this->db->query ( $sql );
	}
}