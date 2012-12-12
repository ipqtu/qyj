<?php
/**
 * 
 * Enter description here ...
 * @author IPQTU
 *
 */
class Filter {
	
	private static $self_object;
	private $is_open_quotes_gpc = false;
	private $get_array = array ();
	private $post_array = array ();
	private $cookie_array = array ();
	private $url_array = array ();
	private $gpcu_filter_array = array ('all_tags' );
	private $gpcu_name_array = array ('g' => 'get', 'p' => 'post', 'c' => 'cookie', 'u' => 'url' );
	
	public function __construct() {
		if (function_exists ( 'get_magic_quotes_gpc' ) && get_magic_quotes_gpc ()) {
			$this->gpcu_filter_array [] = 'stripcslashes';
			$this->is_open_quotes_gpc = true;
		}
		$this->filter_gpcu ();
		self::$self_object = &$this;
	}
	
	static function get_object() {
		is_object ( self::$self_object ) or die ( '请先初始化Filter类' );
		return self::$self_object;
	}
	
	private function filter_gpcu() {
		global $_URL, $_GET, $_POST, $_COOKIE;
		$this->post_array = $_POST;
		$_POST = $this->filter_data ( $_POST, $this->gpcu_filter_array );
		$this->get_array = $_GET;
		$_GET = $this->filter_data ( $_GET, $this->gpcu_filter_array );
		$this->cookie_array = $_COOKIE;
		$_COOKIE = $this->filter_data ( $_COOKIE, $this->gpcu_filter_array );
		$this->url_array = $_URL;
		$_URL = $this->filter_data ( $_URL, $this->gpcu_filter_array );
	}
	
	public function filter_data($data, $filter_array = array()) {
		if (empty ( $filter_array ))
			return $data;
		if (is_array ( $data )) {
			foreach ( $data as $k => $v ) {
				$data [$k] = $this->filter_string ( $v, $filter_array );
			}
			return $data;
		}
		if (is_string ( $data ))
			return $this->filter_string ( $data, $filter_array );
		return $data;
	}
	
	public function filter_string($string, $filter_array = array()) {
		if (empty ( $filter_array ) || ! is_string ( $string ))
			return $string;
		foreach ( ( array ) $filter_array as $v ) {
			$function_name = 'filter_' . $v;
			method_exists ( $this, $function_name ) && $string = $this->$function_name ( $string );
		}
		return $string;
	}
	
	/**
	 * 过滤全部的tag js,css,html
	 * @param $string 
	 * @param $remove_breaks true|false 过滤中间的空白换行
	 * @return string
	 */
	public function filter_all_tags($string, $remove_breaks = false) {
		$string = preg_replace ( '@<(script|style)[^>]*?>.*?</\\1>@si', '', $string );
		$string = strip_tags ( $string );
		if ($remove_breaks)
			$string = preg_replace ( '/[\r\n\t ]+/', ' ', $string );
		return trim ( $string );
	}
	
	/**
	 * 过滤空格
	 */
	public function filter_blank($string) {
		return trim ( $string );
	}
	
	/**
	 * 反转换/
	 */
	public function filter_stripcslashes($string) {
		return stripcslashes ( $string );
	}
	/**
	 * 过滤文本编辑内容
	 */
	public function filter_edit_content($string) {
		//转换/
		($this->is_open_quotes_gpc) && $string = $this->filter_stripcslashes ( $string );
		//过滤js
		$string = preg_replace ( '@<(script|style|i?frame)[^>]*?>.*?</\\1>@si', '', $string );
		//过滤on
		$string = preg_replace ( "/(<[^>]*)on[a-zA-Z] \s*=([^>]*>)/is", '', $string );
		return $string;
	}
	/**
	 * 判断email
	 */
	public function is_email($email) {
		// Test for the minimum length the email can be
		if (strlen ( $email ) < 3) {
			return false;
		}
		// Test for an @ character after the first position
		if (strpos ( $email, '@', 1 ) === false) {
			return false;
		}
		// Split out the local and domain parts
		list ( $local, $domain ) = explode ( '@', $email, 2 );
		
		// LOCAL PART
		// Test for invalid characters
		if (! preg_match ( '/^[a-zA-Z0-9!#$%&\'*+\/=?^_`{|}~\.-]+$/', $local )) {
			return false;
		}
		
		// DOMAIN PART
		// Test for sequences of periods
		if (preg_match ( '/\.{2,}/', $domain )) {
			return false;
		}
		
		// Test for leading and trailing periods and whitespace
		if (trim ( $domain, " \t\n\r\0\x0B." ) !== $domain) {
			return false;
		}
		
		// Split the domain into subs
		$subs = explode ( '.', $domain );
		
		// Assume the domain will have at least two subs
		if (2 > count ( $subs )) {
			return false;
		}
		// Loop through each sub
		foreach ( $subs as $sub ) {
			// Test for leading and trailing hyphens and whitespace
			if (trim ( $sub, " \t\n\r\0\x0B-" ) !== $sub) {
				return false;
			}
			
			// Test for invalid characters
			if (! preg_match ( '/^[a-z0-9-]+$/i', $sub )) {
				return false;
			}
		}
		// Congratulations your email made it!
		return true;
	}
	
	public function get_int($data) {
		return intval ( $data );
	}
	
	public function get_abs_int($data) {
		return abs ( intval ( $data ) );
	}
	
	public function get_real_gpc_var($var_name, $var_type = 'p') {
		$gpc_array = $this->get_real_gpc ( $var_type );
		if (empty ( $gpc_array ))
			return "";
		$var_value = (isset ( $gpc_array [$var_name] )) ? $gpc_array [$var_name] : "";
		return $var_value;
	}
	
	public function get_real_gpc($var_type = 'p') {
		if (isset ( $this->gpcu_name_array [$var_type] )) {
			$gpc_type = $this->gpcu_name_array [$var_type] . '_array';
			return $this->$gpc_type;
		}
		return array ();
	}

}