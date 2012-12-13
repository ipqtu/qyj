<?php
class Friends {
	
	private $db;
	private $filter;
	private $member_friends_table_name = "user_friends";
	
	public function __construct() {
		$this->db = Mysql::get_object ();
		$this->filter = Filter::get_object ();
		$this->member_friends_table_name = $this->db->prefix . $this->member_friends_table_name;
	}
	
	/**
	 * 添加好友关系
	 * Enter description here ...
	 * @param unknown_type $user_id
	 * @param unknown_type $firend_id
	 */
	public function add_friend($user_id, $friend_id, $friend_name) {
		if (! $this->is_friend_relation ( $user_id, $friend_id ))
			$this->db->insert ( $this->member_friends_table_name, array ('friend_name' => $friend_name, 'user_id' => $user_id, 'friend_id' => $friend_id, 'ctime' => time () ), array ("%s", "%d", "%d", '%s' ) );
	}
	
	public function is_friend_relation($user_id, $friend_id) {
		$sql = "SELECT * FROM `" . $this->member_friends_table_name . '` WHERE `user_id` = %d AND `friend_id` = %d';
		$result = $this->db->get_row ( $this->db->prepare ( $sql, array ($user_id, $friend_id ) ) );
		if (empty ( $result ))
			return false;
		return true;
	}
	
	public function del_friend($user_id, $friend_id) {
		if ($this->is_friend_relation ( $user_id, $friend_id )) {
			$this->db->delete ( $this->member_friends_table_name, array ('user_id' => $user_id, 'friend_id' => $friend_id ), array ("%d", "%d" ) );
		}
	}
	
	public function list_user_friends($user_id, $star_num, $end_num) {
		$sql = "SELECT * FROM `" . $this->member_friends_table_name . '` WHERE `user_id` = %d ORDER BY `ctime` DESC LIMIT %d,%d';
		return $this->db->get_results ( $this->db->prepare ( $sql, array ($user_id, $star_num, $end_num ) ) );
	}
}