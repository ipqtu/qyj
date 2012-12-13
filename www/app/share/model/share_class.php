<?php
class Share {
	private $db;
	private $table_name = "share";
	
	public function __construct() {
		$this->db = Mysql::get_object ();
		$this->table_name = $this->db->prefix . $this->table_name;
	}
	
	public function add($uid, $u_name, $title, $type, $content) {
		$this->db->insert ( $this->table_name, array ('uid' => $uid, 'u_name' => $u_name, 'title' => $title, 'type' => $type, 'content' => $content, 'ctime' => time () ), array ('%d', '%s', '%s', '%d', '%s', '%s' ) );
		return $this->db->get_insert_id ();
	}
	
	public function edit($uid, $u_name, $title, $type, $content) {
		$this->db->update ( $this->table_name, array ('uid' => $uid, 'u_name' => $u_name, 'title' => $title, 'type' => $type, 'content' => $content, 'ctime' => time () ) );
	}
	
	public function del($uid, $id) {
		$this->db->delete ( $this->table_name, array ('id' => $id, 'uid' => $uid ), array ('%d', '%d' ) );
	}
	
	public function get_share_by_type($type, $star_num, $end_num) {
		$sql = "SELECT * FROM " . $this->table_name . ' WHERE `type` = %d ORDER BY `ctime` DESC LIMIT %d,%d';
		return $this->db->get_results ( $this->db->prepare ( $sql, array ($type, $star_num, $end_num ) ) );
	}
}