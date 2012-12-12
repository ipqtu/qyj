<?php

/**
 * @author ipqtu
 * 
 */
class Mysql {
	
	/**
	 * @var self::OBJECT array(object,object) 
	 */
	static $OBJECT = 'OBJECT';
	
	/**
	 * @var self::ARRAY_A array(array(key=>value))
	 */
	static $ARRAY_A = 'ARRAY_A';
	
	/**
	 * @var self::ARRAY_N array(array(0=>value))
	 */
	static $ARRAY_N = 'ARRAY_N';
	
	/**
	 * @var self::OBJECT_K  array(row[0]=>object)
	 */
	static $OBJECT_K = 'OBJECT_K';
	
	private static $self_object;
	
	private $dbhost = "localhost";
	
	private $dbname;
	
	private $dbuser = "root";
	
	private $dbpassword = "";
	
	public $prefix = "pre_";
	
	private $setchar = 'utf8';
	
	private $collate = 'utf8_general_ci';
	
	private $link;
	
	private $query_num = 0;
	
	private $last_query = "";
	
	private $last_result;
	
	private $db_debug;
	
	private $admin_email = "";
	
	private $ready = false;
	
	private $col_info;
	
	private $last_error = "";
	
	private $insert_id;
	
	private $rows_affected;
	
	public function __construct($dbhost, $dbuser, $dbpassword, $dbname, $setchar, $prefix, $admin_email, $dbbug = FALSE) {
		$this->dbhost = $dbhost;
		$this->dbuser = $dbuser;
		$this->dbpassword = $dbpassword;
		$this->dbname = $dbname;
		$this->setchar = $setchar;
		$this->prefix = $prefix;
		$this->admin_email = $admin_email;
		$this->db_debug = $dbbug;
		$this->connect ();
		self::$self_object = &$this;
	}
	
	static function get_object() {
		is_object ( self::$self_object ) or die ( '请先初始化Mysql类' );
		return self::$self_object;
	}
	
	/**
	 * 关闭数据库，释放数据库连接
	 */
	public function __destruct() {
		if (is_resource ( $this->link )) {
			mysql_close ( $this->link );
		}
	}
	
	/**
	 * 连接数据库
	 */
	private function connect() {
		$this->link = @mysql_connect ( $this->dbhost, $this->dbuser, $this->dbpassword ) or $this->print_error ( "不能连接{$this->dbhost}数据库" );
		$this->set_charset ( $this->link );
		$this->ready = true;
		@mysql_select_db ( $this->dbname ) or $this->print_error ( "不能选择数据库: " . $this->dbname );
	}
	
	/**
	 * 过滤sql
	 * %d (integer)
	 * %f (float)
	 * %s (string)
	 * %% (literal percentage sign - no argument needed)
	 * <code>
	 * prepare( "SELECT * FROM `table` WHERE `column` = %s AND `field` = %d", 'foo', 1337 )
	 * prepare( "SELECT DATE_FORMAT(`field`, '%%c') FROM `table` WHERE `column` = %s", 'foo' );
	 * </code>
	 * @param str $query
	 */
	public function prepare($query = null) { // ( $query, *$args )
		if (is_null ( $query ))
			return;
		$args = func_get_args ();
		array_shift ( $args );
		if (isset ( $args [0] ) && is_array ( $args [0] ))
			$args = $args [0];
		$query = str_replace ( "'%s'", '%s', $query ); // in case someone mistakenly already singlequoted it
		$query = str_replace ( '"%s"', '%s', $query ); // doublequote unquoting
		$query = preg_replace ( '|(?<!%)%s|', "'%s'", $query ); // quote the strings, avoiding escaped strings like %%s
		foreach ( $args as $k => $v ) {
			$args [$k] = mysql_real_escape_string ( $v, $this->link );
		}
		return @vsprintf ( $query, $args );
	}
	
	/**
	 * 设置数据库编码和整理编码
	 * @param source $db_link
	 * @param str $charset
	 * @param str $collate
	 */
	private function set_charset($db_link, $charset = null, $collate = null) {
		if (! isset ( $charset ))
			$charset = $this->setchar;
		if (! isset ( $collate ))
			$collate = $this->collate;
		if ($this->has_cap ( 'collation', $db_link ) && ! empty ( $charset )) {
			if (function_exists ( 'mysql_set_charset' ) && $this->has_cap ( 'set_charset', $db_link )) {
				mysql_set_charset ( $charset, $db_link );
				$this->real_escape = true;
			} else {
				$query = $this->prepare ( 'SET NAMES %s', $charset );
				if (! empty ( $collate ))
					$query .= $this->prepare ( ' COLLATE %s', $collate );
				mysql_query ( $query, $db_link );
			}
		}
	}
	
	/**
	 * sql 请求执行函数
	 * @param str $sql
	 * @example 
	 * query('select * from table where `id`=%i and `name`=%s',$id,$name);
	 * query('select * from table where `id`=%i and `name`=%s',array($id,$name));
	 */
	public function query($sql) {
		if (! $this->ready) {
			$this->connect ();
		}
		$return_val = "";
		$this->flush ();
		$this->last_query = $sql;
		$this->query_num ++;
		$result = @mysql_query ( $sql, $this->link );
		$this->last_error = mysql_error ();
		empty ( $this->last_error ) || $this->print_error ();
		
		if (preg_match ( '/^\s*(create|alter|truncate|drop) /i', $sql )) {
			$return_val = $result;
		} elseif (preg_match ( '/^\s*(insert|delete|update|replace) /i', $sql )) {
			$this->rows_affected = mysql_affected_rows ( $this->link );
			if (preg_match ( '/^\s*(insert|replace) /i', $sql )) {
				$this->insert_id = mysql_insert_id ( $this->link );
			}
			$return_val = $this->rows_affected;
		} else {
			$i = 0;
			while ( $i < @mysql_num_fields ( $result ) ) {
				$this->col_info [$i] = @mysql_fetch_field ( $result );
				$i ++;
			}
			$num_rows = 0;
			while ( $row = @mysql_fetch_object ( $result ) ) {
				$this->last_result [$num_rows] = $row;
				$num_rows ++;
			}
			@mysql_free_result ( $result );
			$this->num_rows = $num_rows;
			$return_val = $this->last_result;
		}
		return $return_val;
	}
	
	/**
	 * 
	 * 插入内容函数
	 * @param str $table
	 * @param array $data
	 * @param array $format
	 * @example
	 * insert( 'table', array( 'column' => 'foo', 'field' => 'bar' ) )
	 * insert( 'table', array( 'column' => 'foo', 'field' => 1337 ), array( '%s', '%d' ) )
	 */
	public function insert($table, $data, $format = null) {
		return $this->_insert_replace_helper ( $table, $data, $format, 'INSERT' );
	}
	
	/**
	 * 
	 * 替换内容函数
	 * @param str $table
	 * @param array $data
	 * @param array $format
	 * @example
	 * replace( 'table', array( 'column' => 'foo', 'field' => 'bar' ) )
	 * replace( 'table', array( 'column' => 'foo', 'field' => 1337 ), array( '%s', '%d' ) )
	 */
	public function replace($table, $data, $format = null) {
		return $this->_insert_replace_helper ( $table, $data, $format, 'REPLACE' );
	}
	
	/**
	 * 插入替换函数执行函数
	 * @param str $table
	 * @param array $data
	 * @param array $format
	 * @param str $type INSERT|REPLACE
	 */
	private function _insert_replace_helper($table, $data, $format = null, $type = 'INSERT') {
		if (! in_array ( strtoupper ( $type ), array ('REPLACE', 'INSERT' ) ))
			return false;
		$format = ( array ) $format;
		$fields = array_keys ( $data );
		$formatted_fields = array ();
		foreach ( $fields as $field ) {
			if (! empty ( $format ))
				$form = array_shift ( $format );
			else
				$form = '%s';
			$formatted_fields [] = $form;
		}
		$sql = "{$type} INTO `$table` (`" . implode ( '`,`', $fields ) . "`) VALUES ('" . implode ( "','", $formatted_fields ) . "')";
		return $this->query ( $this->prepare ( $sql, $data ) );
	}
	
	public function get_insert_id() {
		return $this->insert_id;
	}
	
	/**
	 * 更改函数
	 * @param str $table
	 * @param array $data
	 * @param array $where
	 * @param array $format
	 * @param array $where_format
	 */
	public function update($table, $data, $where, $format = null, $where_format = null) {
		if (! is_array ( $data ) || ! is_array ( $where ))
			return false;
		
		$format = ( array ) $format;
		$bits = $wheres = array ();
		foreach ( ( array ) array_keys ( $data ) as $field ) {
			if (! empty ( $format ))
				$form = array_shift ( $format );
			else
				$form = '%s';
			$bits [] = "`$field` = {$form}";
		}
		
		$where_format = ( array ) $where_format;
		foreach ( ( array ) array_keys ( $where ) as $field ) {
			if (! empty ( $where_format ))
				$form = array_shift ( $where_format );
			else
				$form = '%s';
			$wheres [] = "`$field` = {$form}";
		}
		
		$sql = "UPDATE `$table` SET " . implode ( ', ', $bits ) . ' WHERE ' . implode ( ' AND ', $wheres );
		return $this->query ( $this->prepare ( $sql, array_merge ( array_values ( $data ), array_values ( $where ) ) ) );
	}
	
	/**
	 * 删除数据
	 * @param str $table
	 * @param array $where
	 * @param array $where_format
	 */
	public function delete($table, $where, $where_format = null) {
		if (! is_array ( $where ))
			return false;
		$wheres = array ();
		$where_format = ( array ) $where_format;
		foreach ( ( array ) array_keys ( $where ) as $field ) {
			if (! empty ( $where_format ))
				$form = array_shift ( $where_format );
			else
				$form = '%s';
			$wheres [] = "`$field` = {$form}";
		}
		
		$sql = "DELETE FROM `$table` WHERE " . implode ( ' AND ', $wheres );
		return $this->query ( $this->prepare ( $sql, $where ) );
	}
	
	/**
	 * 获取结果集的某个值
	 * @param str $query 默认为空
	 * @param int $x>=0
	 * @param int $y>=0
	 * @return str
	 */
	public function get_var($query = null, $x = 0, $y = 0) {
		if ($query)
			$this->query ( $query );
		
		// Extract var out of cached results based x,y vals
		if (! empty ( $this->last_result [$y] )) {
			$values = array_values ( get_object_vars ( $this->last_result [$y] ) );
		}
		// If there is a value return it else return null
		return (isset ( $values [$x] ) && $values [$x] !== '') ? $values [$x] : null;
	}
	
	/**
	 * 获取结果集中的一行
	 * @param str $query
	 * @param str $output (self::OBJECT) self::OBJECT|self::ARRAY_A|self::ARRAY_N
	 * @param int $y (0) $y>=0
	 * @return self::OBJECT (->var) self::ARRAY_A ('name'=>'XXX') self::ARRAY_N (0=>'XXX')
	 */
	public function get_row($query = null, $output = 'OBJECT', $y = 0) {
		if ($query)
			$this->query ( $query );
		else
			return array ();
		
		if (! isset ( $this->last_result [$y] ))
			return array ();
		
		if ($output == self::$OBJECT) {
			return $this->last_result [$y] ? $this->last_result [$y] : null;
		} elseif ($output == self::$ARRAY_A) {
			return $this->last_result [$y] ? get_object_vars ( $this->last_result [$y] ) : null;
		} elseif ($output == self::$ARRAY_N) {
			return $this->last_result [$y] ? array_values ( get_object_vars ( $this->last_result [$y] ) ) : null;
		} else {
			$this->print_error ( '$db->get_row(string query, output type, int offset) —— 输出类型需为 OBJECT、ARRAY_A，或 ARRAY_N' );
		}
	}
	
	/**
	 * 获取结果集的一列
	 * @param str $query
	 * @param int $x>=0
	 * @return array() array(0=>"XXX",1=>"XX2");
	 */
	public function get_col($query = null, $x = 0) {
		if ($query)
			$this->query ( $query );
		$new_array = array ();
		// Extract the column values
		for($i = 0, $j = count ( $this->last_result ); $i < $j; $i ++) {
			$new_array [$i] = $this->get_var ( null, $x, $i );
		}
		return $new_array;
	}
	
	/**
	 * 获取全部搜索结果
	 * @param str $query
	 * @param array $output 
	 * self::OBJECT array(object,object) 
	 * self::OBJECT_K  array(row[0]=>object)
	 * self::ARRAY_A array(array(key=>value))
	 * self::ARRAY_N array(array(0=>value))
	 */
	function get_results($query = null, $output = 'OBJECT') {
		if ($query)
			$this->query ( $query );
		$new_array = array ();
		if ($output == self::$OBJECT) {
			// Return an integer-keyed array of row objects
			return $this->last_result;
		} elseif ($output == self::$OBJECT_K) {
			// Return an array of row objects with keys from column 1
			// (Duplicates are discarded)
			foreach ( $this->last_result as $row ) {
				$var_by_ref = get_object_vars ( $row );
				$key = array_shift ( $var_by_ref );
				if (! isset ( $new_array [$key] ))
					$new_array [$key] = $row;
			}
			return $new_array;
		} elseif ($output == self::$ARRAY_A || $output == self::$ARRAY_N) {
			// Return an integer-keyed array of...
			if ($this->last_result) {
				foreach ( ( array ) $this->last_result as $row ) {
					if ($output == self::$ARRAY_N) {
						// ...integer-keyed row arrays
						$new_array [] = array_values ( get_object_vars ( $row ) );
					} else {
						// ...column name-keyed row arrays
						$new_array [] = get_object_vars ( $row );
					}
				}
			}
			return $new_array;
		}
		return null;
	}
	
	/**
	 * 获取指定字段的一列或者偏移量的值
	 * @param str $info_type 字段名字
	 * @param int $col_offset 偏移量
	 */
	public function get_col_info($info_type = 'name', $col_offset = -1) {
		if ($this->col_info) {
			if ($col_offset == - 1) {
				$i = 0;
				$new_array = array ();
				foreach ( ( array ) $this->col_info as $col ) {
					$new_array [$i] = $col->{$info_type};
					$i ++;
				}
				return $new_array;
			} else {
				return $this->col_info [$col_offset]->{$info_type};
			}
		}
	}
	
	/**
	 * 获取指定条件的数据条数
	 * @param str $table
	 * @param array $where
	 * @param array $where_format
	 */
	public function get_table_num($table, $where = "", $where_format = NULL) {
		if (is_array ( $where )) {
			$where_format = ( array ) $where_format;
			$wheres = array ();
			foreach ( ( array ) array_keys ( $where ) as $field ) {
				if (! empty ( $where_format ))
					$form = array_shift ( $where_format );
				else
					$form = '%s';
				$wheres [] = "`$field` = {$form}";
			}
			
			$sql = "SELECT count(*) as num FROM `" . $table . '` WHERE ' . implode ( ' AND ', $wheres );
		}else{
			$sql = "SELECT count(*) as num FROM `" . $table;
		}
		$result = $this->query ( $this->prepare ( $sql, $where ) );
		return $result [0]->num;
	}
	
	/**
	 * 获取数据库的大小
	 */
	public function get_db_size() {
		$sql = "SHOW TABLE STATUS FROM " . $this->dbname . ' LIKE ' . $this->prefix . '%';
		$results = $this->get_results ( $sql, self::$OBJECT );
		foreach ( $results as $result ) {
			$size += $result->Data_length + $result->Index_length;
		}
		$units = array ('B', 'K', 'M', 'G', 'T' );
		$i = 0;
		while ( $size > 1024 && $i < 5 ) {
			$size = intval ( $size / 1024 );
			$i ++;
		}
		return $size . $units [$i];
	}
	
	/**
	 * 获取数据版本
	 * @return mixed
	 */
	private function db_version() {
		return preg_replace ( '/[^0-9.].*/', '', mysql_get_server_info ( $this->link ) );
	}
	
	/**
	 * 判断数据库功能支持情况
	 * @param str $db_cap 要求支持的操作 collation,group_concat,subqueries,set_charset
	 */
	private function has_cap($db_cap) {
		$version = $this->db_version ();
		switch (strtolower ( $db_cap )) {
			case 'collation' :
			case 'group_concat' :
			case 'subqueries' :
				return version_compare ( $version, '4.1', '>=' );
			case 'set_charset' :
				return version_compare ( $version, '5.0.7', '>=' );
		}
		;
		
		return false;
	}
	
	/**
	 * 获取数据库错误发生所在函数
	 */
	private function get_caller() {
		$trace = array_reverse ( debug_backtrace () );
		$caller = array ();
		
		foreach ( $trace as $call ) {
			if (isset ( $call ['class'] ) && __CLASS__ == $call ['class'])
				continue;
			$caller [] = isset ( $call ['class'] ) ? "{$call['class']}->{$call['function']}" : $call ['function'];
		}
		
		return join ( ', ', $caller );
	}
	
	/**
	 * 
	 * 展示数据库错误
	 * @param str $str error_string
	 */
	private function print_error($str = '') {
		($str == "") ? $str = mysql_error ( $this->link ) : "";
		if ($this->db_debug) {
			$caller = $this->get_caller ();
			$str = htmlspecialchars ( $str, ENT_QUOTES );
			$query = htmlspecialchars ( $this->last_query, ENT_QUOTES );
			if (empty ( $caller ))
				$error_str = sprintf ( '数据库错误: %1$s <br/>查询语句:<code> %2$s </code><br/>错误点： %3$s ', $str, $query, $caller );
			else
				$error_str = sprintf ( '数据库错误： %1$s <br/>查询语句:<code> %2$s </code><br/>', $str, $query );
			exit ( $error_str );
		}
		exit ( "网站数据库错误:请联系网站负责人E_mail:" . $this->admin_email );
	}
	/**
	 * 清空缓存的数据库查询的信息
	 */
	private function flush() {
		$this->last_result = array ();
		$this->col_info = null;
		$this->last_query = null;
	}
}
?>
