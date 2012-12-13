<?php
class Photo_like {
	
	private $db;
	private $filter;
	private $time = '';
	private $photo_like_table_name = "photo_like";
	
	public function __construct() {
		$this->db = Mysql::get_object ();
		$this->filter = Filter::get_object ();
		$this->time = strtotime ( "today" );
		$this->photo_like_table_name = $this->db->prefix . $this->photo_like_table_name;
	}
	
	public function get_photo_like_table_name() {
		return $this->photo_like_table_name;
	}
	
	public function check_user_today_is_like($user_id, $photo_id) {
		$photo_id = $this->filter->get_abs_int ( $photo_id );
		$sql = "SELECT * FROM `" . $this->photo_like_table_name . '` WHERE `photo_id`=' . $photo_id . ' AND `user_id` = '.$user_id.' AND `ctime`>' . $this->time;
		$result = $this->db->get_results ( $sql );
		if (empty ( $result )) {
			$this->db->insert ( $this->photo_like_table_name, array ('photo_id' => $photo_id, 'user_id' => $user_id, 'ctime' => time () ) );
			return true;
		}
		return false;
	}

}