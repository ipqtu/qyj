<?php
class Tag {
	
	private $db;
	private $filter;
	private $db_table_name = "tag";
	
	public function __construct() {
		$this->db = Mysql::get_object ();
		$this->filter = Filter::get_object ();
		$this->db_table_name = $this->db->prefix . $this->db_table_name;
	}
	
	public function get_tage_by_id($tag_id) {
		$sql = "SELECT * FROM " . $this->db_table_name . ' WHERE `id` = %d';
		return $this->db->get_results ( $this->db->prepare ( $sql, $tag_id ) );
	}
	
	public function get_tage_by_name($tag_name) {
		$sql = "SELECT * FROM " . $this->db_table_name . ' WHERE `tag_name` = %s';
		return $this->db->get_results ( $this->db->prepare ( $sql, $tag_name ) );
	}
	
	public function add_tage($tag_name) {
		$tag_info = $this->get_tage_by_name ( $tag_name );
		if (! empty ( $tag_info ))
			return $tag_info->id;
		$this->db->insert ( $this->db_table_name, array ('tag_name' => $tag_name, 'ctime' => date ( "Y-m-d H:i:s" ) ), array ('%s', '%s' ) );
		return $this->db->get_insert_id ();
	}
	

}