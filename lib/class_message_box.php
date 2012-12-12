<?php
class MessageBox {
	
	private $db;
	private $filter;
	private $member_message_table_name = "user_message";
	
	public function __construct() {
		$this->db = Mysql::get_object ();
		$this->filter = Filter::get_object ();
		$this->member_message_table_name = $this->db->prefix . $this->member_message_table_name;
	}
	
	private function get_one_message($message_id) {
		$sql = "SELECT * FROM `" . $this->member_message_table_name . '`  WHERE `id` = %d';
		return $this->db->get_row ( $this->db->prepare ( $sql, $message_id ) );
	}
	
	public function get_message($user_id, $message_id) {
		$message_info = $this->get_one_message ( $message_id );
		if (empty ( $message_info ) || ($message_info->replayed != 0))
			return "";
		if ($message_info->accept_user_id == $user_id)
			$this->db->update ( $this->member_message_table_name, array ('is_look' => 1 ), array ('id' => $message_info->id ) );
		if (($message_info->accept_user_id == $user_id) || ($message_info->send_user_id == $user_id)) {
			$messages [] = $message_info;
			if ($message_info->replay_id != 0) {
				$this->get_replay_messages ( $message_info->replay_id, $messages );
			}
			krsort ( $messages );
			return $messages;
		}
		return "";
	}
	
	private function get_replay_messages($star_message_id, &$messages) {
		$message_info = $this->get_one_message ( $star_message_id );
		if (empty ( $message_info ))
			return "";
		$messages [] = $message_info;
		if (empty ( $message_info->replay_id )) {
			return $messages;
		}
		return $this->get_replay_messages ( $message_info->replay_id, $messages );
	}
	
	public function list_user_message($user_id, $star_num, $end_num, $type = 0) {
		switch ($type) {
			case 0 :
				$where_str = " send_user_id = 0 AND `accept_user_id`=%d ";
				break; //系统信息
			case 1 :
				$where_str = " send_user_id = %d AND `sender_is_delete` =0";
				break; //发件邮箱
			default :
				$where_str = " send_user_id >0  AND accept_user_id = %d AND `accepter_is_delete` =0";
				break; //收件邮箱
		}
		$sql = "SELECT * FROM `" . $this->member_message_table_name . '` WHERE ' . $where_str . ' AND `replayed` =0 ORDER BY `is_look`,`ctime` DESC LIMIT %d,%d';
		return $this->db->get_results ( $this->db->prepare ( $sql, array ($user_id, $star_num, $end_num ) ) );
	}
	
	public function system_send_message($accept_user_id, $accept_user_name, $content, $title) {
		$send_user_name = "趣友街系统";
		$this->send_message ( $accept_user_id, $accept_user_name, 0, $send_user_name, $content, $title );
	}
	
	public function send_message($accept_user_id, $accept_user_name, $send_user_id, $send_user_name, $content, $title, $replay_id = 0) {
		$this->db->insert ( $this->member_message_table_name, array ('accept_user_id' => $accept_user_id, 'accept_user_name' => $accept_user_name, 'send_user_id' => $send_user_id, 'send_user_name' => $send_user_name, 'content' => $content, 'is_look' => 0, 'replay_id' => $replay_id, 'ctime' => time (), 'title' => $title ) );
		return $this->db->get_insert_id ();
	}
	
	public function get_user_new_message_num($user_id) {
		$sql = 'SELECT count(*) FROM `' . $this->member_message_table_name . '` WHERE `accept_user_id`=%d AND `is_look` =0';
		return $this->db->get_var ( $this->db->prepare ( $sql, $user_id ) );
	}
	
	public function get_user_new_system_message_num($user_id) {
		$sql = 'SELECT count(*) FROM `' . $this->member_message_table_name . '` WHERE `send_user_id` =0 AND `accept_user_id`=%d AND `is_look` =0';
		return $this->db->get_var ( $this->db->prepare ( $sql, $user_id ) );
	}
	
	public function get_user_new_user_message_num($user_id) {
		$sql = 'SELECT count(*) FROM `' . $this->member_message_table_name . '` WHERE `send_user_id` =0 AND `accept_user_id`=%d AND `is_look` =0';
		return $this->db->get_var ( $this->db->prepare ( $sql, $user_id ) );
	}
	
	public function replay_message($message_id, $replay_user_id, $replay_user_name, $title, $content) {
		$message_info = $this->get_one_message ( $message_id );
		if (empty ( $message_info )) {
			return 0;
		}
		if (($message_info->send_user_id == $replay_user_id) || ($message_info->accept_user_id == $replay_user_id)) {
			$this->db->update ( $this->member_message_table_name, array ('replayed' => 1 ), array ('id' => $message_info->id ) );
			if ($message_info->send_user_id == $replay_user_id) {
				$accepty_user_id = $message_info->accept_user_id;
				$accept_user_name = $message_info->accept_user_name;
			} else {
				$accepty_user_id = $message_info->send_user_id;
				$accept_user_name = $message_info->send_user_name;
			}
			return $this->send_message ( $accepty_user_id, $accept_user_name, $replay_user_id, $replay_user_name, $content, $title, $message_info->id );
		}
		return 0;
	}
	
	private function del_messages($message_id, $user_id) {
		$message_info = $this->get_one_message ( $message_id );
		if (empty ( $message_info )) {
			return false;
		}
		if (($message_info->send_user_id == $user_id) || ($message_info->accept_user_id == $user_id)) {
			if (0 != $message_info->replay_id)
				$this->del_messages ( $message_info->replay_id, $user_id );
			$this->db->delete ( $this->member_message_table_name, array ('id' => $message_info->id ) );
		}
	}
	
	public function del_message($message_id, $user_id) {
		$message_info = $this->get_one_message ( $message_id );
		if (empty ( $message_info )) {
			return false;
		}
		//发送者删除
		if ($message_info->send_user_id == $user_id) {
			//接受者已经删除
			if ($message_info->accepter_is_delete == 1)
				$this->del_messages ( $message_id, $user_id );
			else
				$this->db->update ( $this->member_message_table_name, array ('sender_is_delete' => 1 ), array ('id' => $message_info->id ) );
		} elseif ($message_info->accept_user_id == $user_id) {
			//接受者删除
			//发送者删除+系统邮件
			if ($message_info->sender_is_delete == 1 || $message_info->send_user_id == 0)
				$this->del_messages ( $message_id, $user_id );
			else
				$this->db->update ( $this->member_message_table_name, array ('accepter_is_delete' => 1 ), array ('id' => $message_info->id ) );
		}
	}
}