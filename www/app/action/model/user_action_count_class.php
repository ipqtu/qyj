<?php
class Action_user_action_count {
	
	private $db;
	private $filter;
	private $table_name = "action_user_count";
	private $user_new_action_info = array ();
	
	public function __construct() {
		$this->db = Mysql::get_object ();
		$this->filter = Filter::get_object ();
		$this->table_name = $this->db->prefix . $this->table_name;
	}
	
	public function get_table_name() {
		return $this->table_name;
	}
	
	public function get_user_action_info($user_id) {
		$user_id = $this->filter->get_abs_int ( $user_id );
		if (isset ( $this->user_new_action_info [$user_id] ))
			return $this->user_new_action_info [$user_id];
		$sql = 'SELECT * FROM ' . $this->table_name . ' WHERE `user_id` = ' . $user_id;
		$this->user_new_action_info [$user_id] = $this->db->get_row ( $sql );
		return $this->user_new_action_info [$user_id];
	}
	
	public function add_real_user_action($user_id, $user_name, $action_id) {
		$result = $this->get_user_action_info ( $user_id );
		if (empty ( $result )) {
			$this->db->insert ( $this->table_name, array ('user_id' => $user_id, 'user_name' => $user_name, 'new_action_num' => 1, 'new_action_ids' => serialize ( $action_id ), 'action_num' => 1, 'action_ids' => serialize ( $action_id ), 'last_time' => time () ) );
		} else {
			$action_num = $result->action_num + 1;
			$old_action_ids = unserialize ( $result->action_ids );
			$action_ids = empty ( $old_action_ids ) ? $action_id : $old_action_ids . ',' . $action_id;
			
			$new_action_num = $result->new_action_num + 1;
			$old_new_action_ids = unserialize ( $result->new_action_ids );
			$new_action_ids = empty ( $old_new_action_ids ) ? $action_id : $old_new_action_ids . ',' . $action_id;
			$this->db->update ( $this->table_name, array ('new_action_num' => $new_action_num, 'new_action_ids' => serialize ( $new_action_ids ), 'action_num' => $action_num, 'action_ids' => serialize ( $action_ids ), 'last_time' => time () ), array ('id' => $result->id ) );
		}
	}

	public function add_user_action($user_id, $user_name, $action_id) {
		$result = $this->get_user_action_info ( $user_id );
		if (empty ( $result )) {
			$this->db->insert ( $this->table_name, array ('user_id' => $user_id, 'user_name' => $user_name, 'new_action_num' => 0, 'new_action_ids' => "", 'action_num' => 1, 'action_ids' => serialize ( $action_id ), 'last_time' => time () ) );
		} else {
			$action_num = $result->action_num + 1;
			$old_action_ids = unserialize ( $result->action_ids );
			$action_ids = empty ( $old_action_ids ) ? $action_id : $old_action_ids . ',' . $action_id;
			$this->db->update ( $this->table_name, array ('action_num' => $action_num, 'action_ids' => serialize ( $action_ids ), 'last_time' => time () ), array ('id' => $result->id ) );
		}
	}

	public function check_user_action(){
	
	}
	/**
	 * 活动结束时删除相应的新活动统计
	 */
	public function over_user_action($user_id, $action_id) {
		$result = $this->get_user_action_info ( $user_id );
		if (empty ( $result )) {
			return;
		} else {
			$new_action_num = $result->new_action_num - 1;
			$new_action_ids = preg_replace ( '/,?' . $action_id . '/', '', unserialize ( $result->new_action_ids ) );
			$this->db->update ( $this->table_name, array ('new_action_num' => $new_action_num, 'new_action_ids' => serialize ( $new_action_ids ), 'last_time' => time () ), array ('id' => $result->id ) );
		}
	}
	
	/**
	 * 用户删除活动时删除相应的活动统计
	 */
	public function del_user_action($user_id, $action_id) {
		$result = $this->get_user_action_info ( $user_id );
		if (empty ( $result )) {
			return;
		} else {
			$action_num = $result->action_num - 1;
			$action_ids = preg_replace ( '/,?' . $action_id . '/', '', unserialize ( $result->action_ids ) );
			$old_new_action_ids = unserialize ( $result->new_action_ids );
			if (preg_match ( '/,?' . $action_id . '/', $old_new_action_ids )) {
				$new_action_num = $result->new_action_num - 1;
				$new_action_ids = preg_replace ( '/,?' . $action_id . '/', '', $old_new_action_ids );
			}
			$this->db->update ( $this->table_name, array ('new_action_num' => $new_action_num, 'new_action_ids' => serialize ( $new_action_ids ), 'action_num' => $action_num, 'action_ids' => serialize ( $action_ids ), 'last_time' => time () ), array ('id' => $result->id ) );
		}
	}
	
	public function get_users_action_info_by_page($star_num, $end_num) {
		$sql = "SELECT * FROM " . $this->table_name . ' ORDER BY `new_action_num` DESC LIMIT %d,%d';
		return $this->db->query ( $this->db->prepare ( $sql, $star_num, $end_num ) );
	}

}