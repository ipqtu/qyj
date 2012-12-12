<?php
class Cache {
	
	private $cache = array ();
	
	private static $self_object;
	
	public function __construct() {
		self::$self_object = &$this;
	}
	
	static function get_object() {
		is_object ( self::$self_object ) or die ( '请先初始化Cache类' );
		return self::$self_object;
	}
	
	public function add_cache($group, $key, $data) {
		$this->cache [$group] [$key] = $data;
	}
	
	public function search_cache($group, $field, $value) {
		if (! isset ( $this->cache [$group] ))
			return "";
		foreach ( $this->cache [$group] as $key => $one_cache ) {
			if (in_array ( $field, $one_cache ) && ($one_cache [$field] == $value))
				return $key;
		}
		return "";
	}
	
	public function get_cache($group, $key) {
		if (! isset ( $this->cache [$group] ) || ! isset ( $this->cache [$group] [$key] ))
			return "";
		return $this->cache [$group] [$key];
	}
	
	public function update_cache($group, $key, $data) {
		if (! isset ( $this->cache [$group] ))
			return "";
		$this->cache [$group] [$key] = $data;
	}
	
	public function del_cache($group, $key) {
		if (! isset ( $this->cache [$group] ))
			return "";
		if (isset ( $this->cache [$group] [$key] ))
			unset ( $this->cache [$group] [$key] );
	}
}
