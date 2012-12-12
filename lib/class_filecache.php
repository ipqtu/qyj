<?php
class File_Cache {
	
	private $cache_path;
	
	private $cache_data_array = array ();
	
	public function __construct($cache_path, $cache_exist_time = "") {
		file_exists ( $cache_path ) || exit ( "缓存路径不正确，不支持缓存" );
		$this->cache_path = $cache_path;
	}
	
	public function add($cache_data_array, $app = "system") {
		if (! is_array ( $cache_data_array ))
			return false;
		$this->get ( $app );
		empty ( $this->cache_data_array [$app] ) && $this->cache_data_array [$app] = array ();
		$this->cache_data_array [$app] = array_merge ( $this->cache_data_array [$app], $cache_data_array );
		return $this->write_cache ( $app );
	}
	
	public function get($app = "system") {
		isset ( $this->cache_data_array [$app] ) || $this->read_cache ( $app );
		return $this->cache_data_array [$app];
	}
	
	private function get_cache_file_path($app = "system") {
		return $this->cache_path . "/" . $app . "_cache.php";
	}
	
	private function write_cache($app = "system") {
		$content_array = $this->cache_data_array [$app];
		$content = "<?php\r\n//createTime:" . date ( 'Y-m-d H:i:s' ) . "\r\n defined ( 'IS_ME' ) or exit (); \r\n \$cache_data =";
		$content .= var_export ( $content_array, true );
		$content .= "\r\n?>";
		$file_hand = fopen ( $this->get_cache_file_path ( $app ), 'w' );
		fwrite ( $file_hand, $content );
		fclose ( $file_hand );
		return true;
	}
	
	private function read_cache($app = "system") {
		$cache_file_path = $this->get_cache_file_path ( $app );
		if (! file_exists ( $cache_file_path )) {
			$this->cache_data_array [$app] = array ();
			$this->write_cache (  $app );
			return;
		}
		include_once $cache_file_path;
		$this->cache_data_array [$app] = $cache_data;
	}

}