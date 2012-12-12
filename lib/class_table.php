<?php
class Table {
	
	private $db;
	
	private $filter;
	
	private $table_name = "";
	
	private $search_num = 0;
	
	private $search_sql = "";
	
	private $search_var_array = array ();
	
	public function __construct($table_name) {
		$this->db = Mysql::get_object ();
		$this->filter = Filter::get_object ();
		$this->table_name = $table_name;
		$this->table_name = $this->db->prefix . $this->table_name;
	}
	
	public function get_value_by_ids($ids) {
		$sql = 'SELECT * FROM ' . $this->table_name . ' WHERE `id` IN (%s)';
		return $this->db->get_results ( $this->db->prepare ( $sql, $ids ) );
	}
	
	public function get_value_by_field($field, $field_value, $order = 'id', $desc = 1) {
		$sql = 'SELECT * FROM ' . $this->table_name . ' WHERE `' . $field . '` = %s ORDER BY ' . $order . (($desc == 1) ? ' DESC' : ' ASC');
		return $this->db->get_results ( $this->db->prepare ( $sql, array ($field_value ) ) );
	}
	
	public function get_value_by_where($where, $order = "", $star_num = 0, $per_num = 1, $desc = 1) {
		$where_str = " WHERE ";
		if (! empty ( $where )) {
			foreach ( $where as $field => $value ) {
				$where_str .= is_int ( $value ) ? "`{$field}` = %d AND " : "`{$field}` = %s AND ";
			}
		}
		$where_str .= " 1=1";
		$order_str = (empty ( $order )) ? ' ' : ' ORDER BY `' . $order . '`' . (($desc == 1) ? ' DESC' : ' ASC');
		$sql = "SELECT * FROM " . $this->table_name . $where_str . $order_str . ' LIMIT %d,%d';
		$sql_var_array = $where;
		$sql_var_array [] = $star_num;
		$sql_var_array [] = $per_num;
		return $this->db->get_results ( $this->db->prepare ( $sql, $sql_var_array ), Mysql::$OBJECT_K );
	}
	
	public function get_all_value($order, $star_num, $per_num) {
		$sql = "SELECT * FROM " . $this->table_name . ' ORDER BY `' . $order . '` DESC LIMIT %d,%d';
		return $this->db->get_results ( $this->db->prepare ( $sql, array ($star_num, $per_num ) ), Mysql::$OBJECT_K );
	}
	
	public function get_table_all_num($where = "") {
		return $this->db->get_table_num ( $this->table_name, $where );
	}
	
	public function get_table_name() {
		return $this->table_name;
	}
	
	public function insert($data, $format = null) {
		return $this->db->insert ( $this->table_name, $data, $format );
	}
	
	public function del($where, $format = null) {
		return $this->db->delete ( $this->table_name, $where, $format );
	}
	
	public function edit($data, $where, $format = null, $where_format = null) {
		return $this->db->update ( $this->table_name, $data, $where, $format, $where_format );
	}
	
	public function auto_add_field($field, $where = array(), $step = 1) {
		$sql = 'UPDATE ' . $this->table_name . ' SET `' . $field . '` = `' . $field . '`+1 WHERE ';
		foreach ( ( array ) $where as $field => $value ) {
			$sql .= '`' . $field . '` = %s AND ';
		}
		$sql .= '1=1';
		$this->db->query ( $this->db->prepare ( $sql, ( array ) $where ) );
	}
	
	public function search($like, $order = "", $desc = 1, $where = array()) {
		$sql_var_array = array ();
		$sql = "SELECT * FROM " . $this->table_name . ' WHERE ';
		if (! empty ( $where ) && is_array ( $where )) {
			$sql_var_array = $where;
			foreach ( $where as $field => $value ) {
				$sql .= "`{$field}` = " . (is_int ( $value ) ? '%d' : '%s') . ' AND ';
			}
		}
		if (empty ( $like ) || ! is_array ( $like ))
			return array ();
		foreach ( $like as $field => $value ) {
			$sql_var_array [] = $value;
			$sql .= "`{$field}` LIKE '%%%s%%' AND ";
		}
		$sql .= '1=1 ';
		$sql .= (empty ( $order )) ? ' ' : ' ORDER BY `' . $order . '`' . (($desc == 1) ? ' DESC' : ' ASC');
		$get_num_sql = str_replace ( '*', 'count(*)
		', $sql );
		$this->search_num = $this->db->get_var ( $this->db->prepare ( $get_num_sql, $sql_var_array ), 0, 0 );
		$this->search_sql = $sql;
		$this->search_var_array = $sql_var_array;
	}
	
	public function get_serach_value($star_num, $per_num) {
		$this->search_sql .= ' LIMIT %d,%d';
		$this->search_var_array [] = $star_num;
		$this->search_var_array [] = $per_num;
		return $this->db->get_results ( $this->db->prepare ( $this->search_sql, $this->search_var_array ), MYSQL::$OBJECT_K );
	}
	
	public function get_search_num() {
		return $this->search_num;
	}
	
	public function get_inser_id (){
		return $this->db->get_insert_id();
	}

}