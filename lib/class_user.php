<?php
require_once 'class_phpass.php';
class User extends PasswordHash {
	
	private $db;
	
	private $cache;
	
	private $user_table_name;
	
	private $user_append_table_name;
	
	private $current_user;
	
	private $is_admin = false;
	
	private $is_founder = false;
	
	private $cookie_append_hash;
	
	private $cookie_var;
	
	private $expiration;
	
	private $site_url;
	
	private $admin_ids;
	
	private $login_fail_error_info;
	
	private $user_base_info = array ('id', 'user_name', 'user_pass', 'user_email', 'user_registered', 'user_activation_key', 'user_status', 'user_grade' );
	
	public function __construct($site_url, $admin_ids) {
		$this->admin_ids = ( array ) $admin_ids;
		parent::PasswordHash ( 8, TRUE );
		$this->cache = Cache::get_object ();
		$this->db = Mysql::get_object ();
		$this->site_url = $site_url;
		$this->user_table_name = $this->db->prefix . "users";
		$this->user_append_table_name = $this->db->prefix . "usermeta";
		$this->cookie_append_hash = md5 ( $site_url );
		$this->expiration = time ();
	}
	
	public function login($user_name, $password, $remember = true) {
		$user_name = $this->filter_user_name ( $user_name );
		if (empty ( $user_name ) || empty ( $password ))
			return 'login_fail';
		$user = $this->get_user_by ( 'name', $user_name );
		if (empty ( $user ))
			return 'name_not_exist';
		if (! $this->CheckPassword ( $password, $user ['user_pass'] ))
			return 'password_error';
		if ($user ['user_status'] == 1)
			return 'need_activate';
		if ($user ['user_status'] != 2)
			return 'name_freeze';
		$this->current_user = $user;
		$this->set_cookie ( "user", $remember );
		($this->current_user ['id'] == FOUNDER_ID) && $this->set_cookie ( 'founder', $remember );
		(in_array ( $this->current_user ['id'], $this->admin_ids )) && $this->set_cookie ( 'admin', $remember );
		return 'login_success';
	}
	
	public function third_user_login($user_id) {
		$user = $this->get_user_by ( 'id', $user_id );
		if (empty ( $user ))
			return 'name_not_exist';
		if ($user ['user_status'] == 1)
			return 'need_activate';
		if ($user ['user_status'] != 2)
			return 'name_freeze';
		$this->current_user = $user;
		$this->set_cookie ( "user" );
		($this->current_user ['id'] == FOUNDER_ID) && $this->set_cookie ( 'founder' );
		(in_array ( $this->current_user ['id'], $this->admin_ids )) && $this->set_cookie ( 'admin' );
		return 'login_success';
	}
	
	public function is_founder() {
		if ($this->is_founder && ($this->current_user ['id'] == FOUNDER_ID))
			return true;
		if ($this->check_cookie_value ( 'founder' ) && ($this->current_user ['id'] == FOUNDER_ID)) {
			$this->is_founder = true;
			return true;
		}
		return false;
	}
	
	public function is_admin() {
		if ($this->is_admin && (in_array ( $this->current_user ['id'], $this->admin_ids )))
			return true;
		if ($this->check_cookie_value ( 'admin' ) && (in_array ( $this->current_user ['id'], $this->admin_ids ))) {
			$this->is_admin = true;
			return true;
		}
		return false;
	}
	
	public function is_login() {
		if (! empty ( $this->current_user ))
			return true;
		if ($this->check_cookie_value ())
			return true;
		return false;
	}
	
	public function logout() {
		$this->current_user = array ();
		$cookie_scheme_array = array ('user', 'admin', 'founder' );
		foreach ( $cookie_scheme_array as $scheme ) {
			$cookie_var = $this->get_cookie_var ( $scheme );
			setcookie ( $cookie_var ['cookie_name'], '', time () - 3600, $cookie_var ['cookie_path'], SITECOOKIEPATH );
		}
	}
	
	public function regist($user_name, $password, $email, $activation_key, $open_user_activation, $append_info = array(), $append_format = array()) {
		$user_name = $this->filter_user_name ( $user_name );
		$hash_password = $this->HashPassword ( $password );
		$user_status = $open_user_activation ? 1 : 2;
		$result = $this->db->insert ( $this->user_table_name, array ('user_name' => $user_name, 'user_pass' => $hash_password, 'user_email' => $email, 'user_registered' => date ( "Y-m-d H:i:s" ), 'user_status' => $user_status, 'user_grade' => 0, 'user_activation_key' => $activation_key ) );
		if ($result && ! empty ( $append_info )) {
			$user_id = $this->db->get_insert_id ();
			$this->add_user_append_info ( $user_id, $append_info, $append_format );
		}
		return $result;
	}
	
	public function get_regist_user_id() {
		return $this->db->get_insert_id ();
	}
	
	public function update_user($user_id, $data, $data_format = NULL) {
		$user_id = abs ( intval ( $user_id ) );
		$result = $this->db->update ( $this->user_table_name, $data, array ('id' => $user_id ), $data_format, array ('%d' ) );
		$result && $this->cache->del_cache ( 'user', $user_id );
		return $result;
	}
	
	public function get_user_append_info($user_id, $meta_key) {
		$user_id = abs ( intval ( $user_id ) );
		return $this->db->get_row ( $this->db->prepare ( 'SELECT * FROM ' . $this->user_append_table_name . ' WHERE `user_id`=%d AND `meta_key`=%s', array ($user_id, $meta_key ) ) );
	}
	
	public function update_user_append_info($user_id, $meta_key, $meta_value) {
		$user_id = abs ( intval ( $user_id ) );
		$result = $this->get_user_append_info ( $user_id, $meta_key );
		if (empty ( $result ))
			$this->add_user_append_info ( $user_id, $meta_key, $meta_value );
		else {
			if ($result->meta_value == $meta_value)
				return;
			else
				$this->db->update ( $this->user_append_table_name, array ('meta_value' => $meta_value ), array ('user_id' => $user_id, 'meta_key' => $meta_key ) );
		}
		$this->cache->del_cache ( 'user_append', $user_id );
	}
	
	public function update_user_append_infos($user_id, $data) {
		foreach ( $data as $k => $v ) {
			$this->update_user_append_info ( $user_id, $k, $v );
		}
	}
	
	public function add_user_append_info($user_id, $meta_key, $meta_value) {
		$user_id = abs ( intval ( $user_id ) );
		$result = $this->db->insert ( $this->user_append_table_name, array ('user_id' => $user_id, 'meta_key' => $meta_key, 'meta_value' => $meta_value ), array ('%d', '%s', '%s' ) );
		$this->cache->del_cache ( 'user_append', $user_id );
	}
	
	public function get_current_user_base_info($field) {
		if (in_array ( $field, $this->user_base_info ))
			return $this->current_user [$field];
		return "";
	}
	
	public function get_current_user_append_info($field) {
		$user_append_info = $this->get_user_append_by ( $this->current_user ['id'] );
		if (isset ( $user_append_info [$field] ))
			return $user_append_info [$field];
		return "";
	}
	
	public function update_current_user_append_info($field, $value) {
		$field_value = $this->get_current_user_append_info ( $field );
		if (empty ( $field_value ))
			$this->add_current_user_append_info ( $field, $value );
		elseif ($field_value == $value)
			return;
		else
			$this->db->update ( $this->user_append_table_name, array ('meta_value' => $value ), array ('user_id' => $this->current_user ['id'], 'meta_key' => $field ) );
		$this->cache->del_cache ( 'user_append', $this->current_user ['id'] );
	}
	
	public function add_current_user_append_info($field, $value) {
		$this->db->insert ( $this->user_append_table_name, array ('user_id' => $this->current_user ['id'], 'meta_key' => $field, 'meta_value' => $value ), array ('%d', '%s', '%s' ) );
		$this->cache->del_cache ( 'user_append', $this->current_user ['id'] );
	}
	
	public function update_current_user_append_infos($data) {
		foreach ( $data as $k => $v ) {
			$this->update_current_user_append_info ( $k, $v );
		}
	}
	
	public function get_user_by($field, $value) {
		if ('id' == $field) {
			if (! is_numeric ( $value ))
				return array ();
			$value = abs ( intval ( $value ) );
		} else {
			$value = trim ( $value );
		}
		if (empty ( $value ))
			return array ();
		switch ($field) {
			case 'id' :
				$cache_user_key = $value;
				$db_field = 'id';
				break;
			case 'name' :
				$value = $this->filter_user_name ( $value );
				$cache_user_key = $this->cache->search_cache ( 'user', 'user_name', $value );
				$db_field = 'user_name';
				break;
			case 'email' :
				$cache_user_key = $this->cache->search_cache ( 'user', 'user_email', $value );
				$db_field = 'user_email';
				break;
			default :
				return array ();
		}
		if (! empty ( $cache_user_key )) {
			$user = $this->cache->get_cache ( 'user', $cache_user_key );
			if (! empty ( $user ))
				return $user;
		}
		$user = $this->db->get_row ( $this->db->prepare ( "SELECT * FROM $this->user_table_name WHERE $db_field = %s", $value ), Mysql::$ARRAY_A );
		if (empty ( $user ))
			return array ();
		$this->cache->add_cache ( 'user', $user ['id'], $user );
		return $user;
	}
	
	public function get_user_append_by($user_id) {
		$user_id = abs ( intval ( $user_id ) );
		$user_append_info = $this->cache->get_cache ( 'user_append', $user_id );
		if (empty ( $user_append_info )) {
			$sql = "SELECT * FROM " . $this->user_append_table_name . ' WHERE `user_id`=%d';
			$result = $this->db->get_results ( $this->db->prepare ( $sql, $user_id ) );
			$user_append_info = array ();
			foreach ( $result as $row ) {
				$user_append_info [$row->meta_key] = $row->meta_value;
			}
			$this->cache->add_cache ( 'user_append', $user_id, $user_append_info );
		}
		return $user_append_info;
	}
	
	private function check_cookie_value($scheme = "user") {
		$cookie_elemets_array = $this->get_cookie_value ( $scheme );
		if (empty ( $cookie_elemets_array ))
			return false;
		$user = $this->get_user_by ( 'name', $cookie_elemets_array ['username'] );
		if (empty ( $user ))
			return false;
		$pass_frag = substr ( $user ['user_pass'], 8, 4 );
		$cookie_var = $this->get_cookie_var ( $scheme );
		$key = hash_hmac ( 'md5', $cookie_elemets_array ['username'] . $pass_frag . '|' . $cookie_elemets_array ['expiration'], $cookie_var ['salt'] );
		$hash = hash_hmac ( 'md5', $cookie_elemets_array ['username'] . '|' . $cookie_elemets_array ['expiration'], $key );
		if ($hash == $cookie_elemets_array ['hmac']) {
			$this->current_user = $user;
			return true;
		}
		return false;
	}
	
	private function create_cookie_value($scheme = "user") {
		$pass_frag = substr ( $this->current_user ['user_pass'], 8, 4 );
		$cookie_var = $this->get_cookie_var ( $scheme );
		$key = hash_hmac ( 'md5', $this->current_user ['user_name'] . $pass_frag . '|' . $this->expiration, $cookie_var ['salt'] );
		$hash = hash_hmac ( 'md5', $this->current_user ['user_name'] . '|' . $this->expiration, $key );
		return $hash;
	}
	
	private function get_cookie_value($scheme = "user") {
		$cookie_var = $this->get_cookie_var ( $scheme );
		if (empty ( $_COOKIE [$cookie_var ['cookie_name']] ))
			return array ();
		$cookie_elements = explode ( '|', $_COOKIE [$cookie_var ['cookie_name']] );
		if (count ( $cookie_elements ) != 3)
			return array ();
		list ( $username, $expiration, $hmac ) = $cookie_elements;
		return array ('username' => $username, 'expiration' => $expiration, 'hmac' => $hmac, 'scheme' => $scheme );
	}
	
	private function set_cookie($scheme = "user", $remember = true) {
		$cookie_value = $this->current_user ['user_name'] . "|" . $this->expiration . "|" . $this->create_cookie_value ( $scheme );
		$cookie_var = $this->get_cookie_var ( $scheme );
		$cookie_time = ($remember) ? 3600 * 24 : 3600 * 4;
		setcookie ( $cookie_var ['cookie_name'], $cookie_value, time () + $cookie_time, $cookie_var ['cookie_path'], SITECOOKIEPATH );
		($remember) && setcookie ( 'user_name', $this->current_user ['user_name'], time () + 3600 * 24 * 30, $cookie_var ['cookie_path'], SITECOOKIEPATH );
	}
	
	private function filter_user_name($user_name) {
		$user_name = strip_tags ( $user_name );
		$user_name = trim ( $user_name );
		// Kill octets
		$user_name = preg_replace ( '|%([a-fA-F0-9][a-fA-F0-9])|', '', $user_name );
		$user_name = preg_replace ( '/&.+?;/', '', $user_name ); // Kill entities
		return $user_name;
	}
	
	private function get_cookie_var($scheme = "user") {
		$cookie_name = $cookie_apth = $salt = "";
		if (isset ( $this->cookie_var [$scheme] )) {
			return $this->cookie_var [$scheme];
		}
		switch ($scheme) {
			case 'admin' :
				{
					$cookie_path = ADMIN_COOKIE_PATH;
					$salt = ADMIN_KEY . ADMIN_SALT;
					$cookie_name = 'admin_' . $this->cookie_append_hash;
					break;
				}
			case 'user' :
				{
					$cookie_path = USER_COOKIE_PATH;
					$salt = USER_KEY . USER_SALT;
					$cookie_name = 'user_' . $this->cookie_append_hash;
					break;
				}
			case 'founder' :
				{
					$cookie_path = FOUNDER_COOKIE_PATH;
					$salt = FOUNDER_KEY . FOUNDER_SALT;
					$cookie_name = 'founder_' . $this->cookie_append_hash;
					break;
				}
		}
		return $this->cookie_var [$scheme] = array ('cookie_name' => $cookie_name, "cookie_path" => $cookie_path, 'salt' => $salt );
	}
	
	public function get_user_by_limit($star_num, $end_num) {
		$sql = 'SELECT * FROM ' . $this->user_table_name . ' WHERE `id` != '.FOUNDER_ID.' LIMIT ' . $star_num . ',' . $end_num;
		return $this->db->get_results ( $sql );
	}
	
	public function get_all_user_num() {
		return $this->db->get_table_num ( $this->user_table_name );
	}
	
	public function delet_user($user_id) {
		$this->db->delete ( $this->user_table_name, array ('id' => $user_id ), array ('%d' ) );
		$this->db->delete ( $this->user_append_table_name, array ('user_id' => $user_id ), array ('%d' ) );
	}
	
	public function shield_user($user_id){
		$this->update_user($user_id, array('user_status'=>3));
	}
	
	public function normal_user($user_id){
		$this->update_user($user_id, array('user_status'=>2));
	}
	
	public function search_user_by_user_name($user_name){
		$sql = "SELECT * FROM ".$this->user_table_name.' WHERE `user_name` LIKE \'%%%s%%\' AND `id` != '.FOUNDER_ID;
		return $this->db->query($this->db->prepare($sql,array($user_name)));
	}
	
}