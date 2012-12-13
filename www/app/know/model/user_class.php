<?php
require_once LIB_URL . 'class_table.php';

class User_info extends Table {
	
	private $use_all_attr = array ('credits' => 20, 'grade' => 0, 'question_num' => 0, 'answer_num' => 0, 'best' => 0 );
	
	private $use_info = array ();
	
	public function __construct() {
		parent::__construct ( 'know_user_info' );
	}
	
	/**
	 * 添加用户属性
	 * Enter description here ...
	 * @param unknown_type $uid
	 * @param unknown_type $attr
	 * @param unknown_type $value
	 */
	private function add_user_attr($uid, $attr, $value = "") {
		$this->insert ( array ('uid' => $uid, 'key' => $attr, 'value' => $value ) );
	}
	
	/**
	 * 添加用户全部属性
	 * Enter description here ...
	 * @param unknown_type $uid
	 */
	private function add_user_all_attr($uid) {
		foreach ( $this->use_all_attr as $attr => $value ) {
			$this->insert ( array ('uid' => $uid, 'key' => $attr, 'value' => $value ) );
		}
	}
	
	/**
	 * 获取用户全部信息
	 * Enter description here ...
	 * @param unknown_type $uid
	 */
	public function get_user_all_info($uid) {
		if (isset ( $this->use_info [$uid] )) {
			return $this->use_info [$uid];
		} else {
			$result = $this->get_value_by_field ( 'uid', $uid, 'uid' );
			if (! empty ( $result )) {
				foreach ( $result as $one_result ) {
					$this->use_info [$uid] [$one_result->key] = $one_result->value;
				}
				return $this->use_info [$uid];
			}
		}
		return array ();
	}
	
	/**
	 * 获取用户信息
	 * Enter description here ...
	 * @param unknown_type $uid
	 * @param unknown_type $attr
	 */
	public function get_user_info($uid, $attr) {
		$user_info = $this->get_user_all_info ( $uid );
		if (empty ( $user_info )) {
			$this->add_user_all_attr ( $uid );
			return $this->use_all_attr [$attr];
		} elseif (! isset ( $user_info [$attr] )) {
			$this->add_user_attr ( $uid, $attr, $this->use_all_attr [$attr] );
			return $this->use_all_attr [$attr];
		}
		return $user_info [$attr];
	}
	
	/**
	 * 修改用户的属性值
	 * Enter description here ...
	 * @param unknown_type $uid
	 * @param unknown_type $attr
	 * @param unknown_type $value
	 */
	public function change_user_attr_value($uid, $attr, $value) {
		$this->edit ( array ('value' => $value ), array ('uid' => $uid, 'key' => $attr ) );
	}

}