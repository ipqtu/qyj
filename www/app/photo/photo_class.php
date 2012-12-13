<?php
class Photo {
	
	private $db;
	private $filter;
	private $photo_table_name = "photo";
	private $mm_ids = array (846,844,842,839,838,749,726,719,709,706,700,699,687,686,685,684,683,682,681,604,594,554,550,543,542,541,539,539,463,456,439,436,435,433,428,408,403,390,367,357,352,338,304,303,301,295,251,230,229,189,188,185,138,102,93,87,81);
	
	public function __construct() {
		$this->db = Mysql::get_object ();
		$this->filter = Filter::get_object ();
		$this->photo_table_name = $this->db->prefix . $this->photo_table_name;
	}
	
	public function get_photo_table_name() {
		return $this->photo_table_name;
	}
	
	public function get_one_photo($photo_id) {
		$photo_id = $this->filter->get_abs_int ( $photo_id );
		$sql = "SELECT * FROM `" . $this->photo_table_name . '` WHERE `id`=' . $photo_id;
		return $this->db->get_row ( $sql );
	}

	public function get_photo_by_ids ($ids){
		$sql = "SELECT * FROM `" . $this->photo_table_name . '` WHERE `id` IN (' . $ids.')';
		return $this->db->get_results ( $sql );
	}
	
	public function add_call_num($photo_id) {
		$photo_id = $this->filter->get_abs_int ( $photo_id );
		$sql = "UPDATE `" . $this->photo_table_name . '` SET `call_num` = `call_num`+1 WHERE `id`=' . $photo_id;
		$this->db->query ( $sql );
	}
	
	public function get_all_photo_by_time($star_num, $end_num) {
		$sql = "SELECT * FROM `" . $this->photo_table_name . '` ORDER BY `photo_ctime` DESC LIMIT ' . $star_num . ',' . $end_num;
		return $this->db->get_results ( $sql );
	}
	
	public function get_all_photo_by_interest_num($star_num, $end_num) {
		$sql = "SELECT * FROM `" . $this->photo_table_name . '` ORDER BY `interest_num` DESC LIMIT ' . $star_num . ',' . $end_num;
		return $this->db->get_results ( $sql );
	}
	
	public function get_all_photo_by_interest_num_asc($star_num, $end_num) {
		$sql = "SELECT * FROM `" . $this->photo_table_name . '` ORDER BY `interest_num` ASC LIMIT ' . $star_num . ',' . $end_num;
		return $this->db->get_results ( $sql );
	}
	
	public function get_all_photo_by_mm($star_num, $end_num) {
		$id_str = implode(',', $this->mm_ids);
		$sql = "SELECT * FROM `" . $this->photo_table_name . '` WHERE `id` IN (0' . $id_str . ') ORDER BY `interest_num` DESC LIMIT '. $star_num . ',' . $end_num;
		return $this->db->get_results ( $sql );
	}
	
	public function get_all_photo_by_search_author($search_content, $star_num, $end_num) {
		$sql = "SELECT * FROM `" . $this->photo_table_name . '`WHERE `photo_author` LIKE \'%%%s%%\' LIMIT ' . $star_num . ',' . $end_num;
		return $this->db->get_results ( $this->db->prepare ( $sql, $search_content, $search_content ) );
	}
	
	public function get_all_photo_by_search_name($search_content, $star_num, $end_num) {
		$sql = "SELECT * FROM `" . $this->photo_table_name . '`WHERE `photo_name` LIKE \'%%%s%%\' LIMIT ' . $star_num . ',' . $end_num;
		return $this->db->get_results ( $this->db->prepare ( $sql, $search_content, $search_content ) );
	}
	
	public function get_type_photo($type, $star_num, $end_num) {
		$sql = "SELECT * FROM `" . $this->photo_table_name . "` WHERE `photo_type` ={$type} ORDER BY `photo_ctime` DESC LIMIT " . $star_num . ',' . $end_num;
		return $this->db->get_results ( $sql );
	}
	
	public function get_all_photo_by_random($star_num, $end_num) {
		$sql = "select   *   from   {$this->photo_table_name}   order   by   rand()   limit   {$star_num},{$end_num}";
		return $this->db->get_results ( $sql );
	}
	
	public function get_user_all_photo($user_id, $star_num, $end_num) {
		$user_id = $this->filter->get_abs_int ( $user_id );
		$sql = "select   *   from   {$this->photo_table_name} WHERE `photo_author_id`={$user_id} order by `interest_num` DESC limit   {$star_num},{$end_num}";
		return $this->db->get_results ( $sql, Mysql::$ARRAY_A );
	}
	
	public function get_user_photo($user_id, $star_num, $end_num) {
		$user_id = $this->filter->get_abs_int ( $user_id );
		$sql = "select   *   from   {$this->photo_table_name} WHERE `photo_author_id`={$user_id} order by rand() limit   {$star_num},{$end_num}";
		return $this->db->get_results ( $sql, Mysql::$ARRAY_A );
	}
	
	public function get_next_photo($photo_id) {
		$sql = "SELECT * FROM " . $this->photo_table_name . ' WHERE `id`>%d ORDER BY `id` ASC LIMIT 0,1';
		return $this->db->get_row ( $this->db->prepare ( $sql, $photo_id ) );
	}
	
	public function get_prev_photo($photo_id) {
		$sql = "SELECT * FROM " . $this->photo_table_name . ' WHERE `id`<%d ORDER BY `id` DESC LIMIT 0,1';
		return $this->db->get_row ( $this->db->prepare ( $sql, $photo_id ) );
	}
	
	public function add_photo_like_num($photo_id) {
		$photo_id = $this->filter->get_abs_int ( $photo_id );
		$sql = "UPDATE `" . $this->photo_table_name . '` SET `interest_num` = `interest_num`+1 WHERE `id`=' . $photo_id;
		$this->db->query ( $sql );
	}

}