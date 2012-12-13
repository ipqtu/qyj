<?php
require_once LIB_URL . 'class_table.php';
require_once LIB_URL . 'class_filter.php';

class LEAVE_MODEL extends Table {
	
	static $myself_object = "";
	
	static $HTML_in_textarea = 0;
	static $HTML_in_edit_textarea = 1;
	static $HTML_in_text = 2;
	static $HTML_in_int_text = 3;
	
	static $PHP_deal_textarea = 0;
	static $PHP_deal_edit_textarea = 1;
	static $PHP_deal_text = 2;
	static $PHP_deal_int_text = 3;
	
	static $PHP_check_no = 0;
	static $PHP_check_empty = 1;
	static $PHP_check_big = 2;
	static $PHP_check_equal = 3;
	
	private $table = "";
	
	private $rn = "\r\n";
	
	private $in_html_file_dir = "";
	
	private $deal_php_file_dir = "";
	
	private $in_html_data_array = array ();
	
	private $deal_php_data_array = array ();
	
	private $check_php_data_array = array ();
	
	private $db_php_data_array = array ();
	
	private $in_html_content = "";
	
	private $deal_php_content = "";
	
	static function get_object($table_name="") {
		if (! is_object ( self::$myself_object )) {
			self::$myself_object = new self ( $table_name );
		}
		return self::$myself_object;
	}
	
	public function __construct($table_name) {
		parent::__construct ( $table_name );
		$this->table = $table_name;
	}
	
	/**
	 * check file exist
	 */
	private function check_create() {
		if (! file_exists ( $this->in_html_file_dir ))
			return true;
		if (! file_exists ( $this->deal_php_file_dir ))
			return true;
		return false;
	}
	
	/**
	 * 生成相关文件
	 */
	public function create_system($template_type="default") {
		$this->in_html_file_dir = ROOT . '/model/leave/html/leave_in_html' . $this->table . ".html";
		$this->deal_php_file_dir = ROOT . '/model/leave/php/leave_deal_php_' . $this->table . ".php";
		if ($this->check_create ()) {
			//生成in_html
			foreach ( $this->in_html_data_array as $data ) {
				$this->in_html_content .= $this->make_in_html ( $data );
			}
			$this->write_file ( $this->in_html_content, $this->in_html_file_dir, true );
			//生成deal_php
			foreach ( $this->deal_php_data_array as $data ) {
				$this->make_deal_php ( $data );
			}
			foreach ( $this->check_php_data_array as $data ) {
				foreach ( $data ['type'] as $key => $type ) {
					$this->make_check_php ( array ('html_name' => $data ['html_name'], 'type' => $type, 'error_message' => $data ['error_message'] [$key] ) );
				}
			}
			if (! empty ( $this->db_php_data_array ))
				$this->make_db_php ();
			$this->write_file ( $this->deal_php_content, $this->deal_php_file_dir );
		}
		$result = new stdClass ();
		$result->in_html = $this->in_html_file_dir;
		$result->deal_php = $this->deal_php_file_dir;
		$result->list_html = ROOT . '/model/leave/template/' . $template_type . ".html";
		$result->list_php = ROOT . '/model/leave/template/' . $template_type . ".php";
		return $result;
	}
	
	/**
	 * html 参数  type 0:textarea 1:edit_textarea 2:text 3:int_text
	 * @param array $data
	 */
	public function add_html_var($html_name, $type, $name, $append = "", $html = "") {
		$this->in_html_data_array [] = array ('type' => $type, 'name' => $name, 'html_name' => $html_name, 'append' => $append, 'html' => $html );
	}
	
	/**
	 * deal php参数 type 0:textarea 1:edit_textarea 2:text 3:int_text
	 * @param unknown_type $data
	 */
	public function add_deal_var($html_name, $type) {
		$this->deal_php_data_array [] = array ('html_name' => $html_name, 'type' => $type );
	}
	
	/**
	 * check php 参数 type 0:不验证 1:验证是否为空 2:验证是否大于XX 3:验证是否等于XX
	 * @param unknown_type $data
	 */
	public function add_check_var($html_name, $type, $error_meaasge = array(), $check_value = array()) {
		(is_array ( $type )) || $type = array ($type );
		(is_array ( $error_meaasge )) || $error_meaasge = array ($error_meaasge );
		(is_array ( $check_value )) || $check_value = array ($check_value );
		$this->check_php_data_array [] = array ('html_name' => $html_name, 'type' => $type, 'check_value' => $check_value, 'error_message' => $error_meaasge );
	}
	
	public function add_db_var($db_var_array = array(), $db_append_array = array()) {
		$this->db_php_data_array ['db_var_array'] = $db_var_array;
		$this->db_php_data_array ['db_append_array'] = $db_append_array;
	}

	
	/**
	 * 数组转换为html
	 * textarea array('type'=>0,'name'=>"XXX",'html_name'=>'XX','apppend'=>'XXX','html'=>'XX')
	 * edit_textarea array('type'=>1,'name'=>"XXX",'html_name'=>'XX','apppend'=>'XXX','html'=>'XX')
	 * text array('type'=>2,'name'=>"XXX",'html_name'=>'XX','apppend'=>'XXX','html'=>'XX')
	 * text_int array('type'=>3,'name'=>"XXX",'html_name'=>'XX','apppend'=>'XXX','html'=>'XX')
	 * 
	 * @param unknown_type $date_array
	 */
	private function make_in_html($data_array) {
		$this->in_html_content .= (empty ( $data_array ['name'] )) ? "" : "<span id=\"model_leave_{$data_array['html_name']}_name\">{$data_array['name']}:</span>\r\n";
		$html_info = 'name="' . $data_array ['html_name'] . '" class = "model_leave_' . $data_array ['html_name'] . '_in"';
		switch ($data_array ['type']) {
			case 0 : //普通文本区域
			case 1 : //编辑器文本区域
				$this->in_html_content .= '<textarea ' . $html_info . "></textarea>\r\n";
				break;
			case 2 : //文本
			case 3 : //int文本
				$this->in_html_content .= '<input type="text" ' . $html_info . "/>\r\n";
				break;
			default :
				break;
		}
		$this->in_html_content .= (empty ( $data_array ['append'] )) ? "" : '<span class="model_leave_' . $data_array ['html_name'] . '_append">(' . $data_array ['append'] . ")</span>\r\n";
		$this->in_html_content .= (empty ( $data_array ['html'] )) ? "" : '<span class="model_leave_' . $data_array ['html_name'] . '_html">' . $data_array ['html'] . "</span>\r\n";
	}
	
	/**
	 * 数组转换处理数据php
	 * Enter description here ...
	 * @param unknown_type $data_array
	 */
	private function make_deal_php($data_array) {
		$html_name = $data_array ['html_name'];
		switch ($data_array ['type']) {
			case 0 : //普通文本区域
				$this->deal_php_content .= '$post_data ["' . $html_name . '"] = $_POST ["' . $html_name . "\"];\r\n";
				break;
			case 1 : //编辑器文本区域
				$this->deal_php_content .= '$post_data ["' . $html_name . '"] = $Object_filter->filter_edit_content ( $Object_filter->get_real_gpc_var ( "' . $html_name . "\" ) );\r\n";
				break;
			case 2 : //文本
				$this->deal_php_content .= '$post_data ["' . $html_name . '"] = $_POST ["' . $html_name . "\"];\r\n";
				break;
			case 3 : //int文本
				$this->deal_php_content .= '$post_data ["' . $html_name . '"] = abs ( intval ( $_POST ["' . $html_name . "\"] ) );\r\n";
				break;
			default :
				break;
		}
	}
	
	/**
	 * 数组转化成验证php
	 * @param unknown_type $data_array
	 */
	private function make_check_php($data_array) {
		$html_name = $data_array ['html_name'];
		switch ($data_array ['type']) {
			case 0 : //不验证
				break;
			case 1 : //验证是否为空
				$this->deal_php_content .= '(empty($post_data ["' . $html_name . '"])) && Display::display_dialog("' . $data_array ['error_message'] . "\");\r\n";
				break;
			case 2 : //验证是否大于XX
				$this->deal_php_content .= '($post_data ["' . $html_name . '"] > ' . $data_array ['check_value'] . ' ) || Display::display_dialog("' . $data_array ['error_message'] . "\");\r\n";
				break;
			case 3 : //验证是否等于XX
				$this->deal_php_content .= '($post_data ["' . $html_name . '"] = ' . $data_array ['check_value'] . ' ) || Display::display_dialog("' . $data_array ['error_message'] . "\");\r\n";
				break;
			default :
				break;
		}
	}
	
	private function make_db_php() {
		if (is_array ( $this->db_php_data_array ['db_var_array'] )) {
			foreach ( $this->db_php_data_array ['db_var_array'] as $db_var => $db_value_name ) {
				$this->deal_php_content .= '$sql_data_array["' . $db_var . '"] = (isset($post_data["' . $db_value_name . '"])) ? $post_data["' . $db_value_name . '"]:"";' . "\r\n";
			}
		}
		if (is_array ( $this->db_php_data_array ['db_append_array'] )) {
			foreach ( $this->db_php_data_array ['db_append_array'] as $k => $v ) {
				$this->deal_php_content .= '$sql_data_array["' . $k . '"]=' . $v . ";\r\n";
			}
		}
		$this->deal_php_content .= '$leave_model_object = LEAVE_MODEL::get_object ( "' . $this->table . '", "' . $this->tag . "\" );\r\n";
		$this->deal_php_content .= "\$leave_model_result = \$leave_model_object->insert(\$sql_data_array);\r\n";
	}
	
	private function write_file($str, $file, $html = "") {
		$content = (empty ( $html )) ? "<?php\r\n//createTime:" . date ( 'Y-m-d H:i:s' ) . "\r\n defined ( 'IS_ME' ) or exit (); \r\n" . "{$str}?>" : $str;
		$file_hand = fopen ( $file, 'w' );
		fwrite ( $file_hand, $content );
		fclose ( $file_hand );
	}

}