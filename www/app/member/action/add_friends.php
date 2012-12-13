<?php
defined ( 'IS_ME' ) or exit ();
$friend_id = $Object_filter->get_abs_int ( $_GET ['friend_id'] );
(empty ( $friend_id )) && exit ( "添加好友失败" );
$friend_info = $Object_user->get_user_by ( 'id', $friend_id );
(empty ( $friend_info )) && exit ( '你要添加的好友不存在' );
require_once 'model/friends_class.php';
$friend_object = new Friends ();
$friend_object->is_friend_relation ( $_USER ['user_id'], $friend_id ) && exit ( "你们已经是好友了" );
$friend_object->add_friend ( $_USER ['user_id'], $friend_id, $friend_info ['user_name'] );
$title = "用户" . $_USER ['user_name'] . "将你加为TA的好友了...";
$content = "用户" . $_USER ['user_name'] . "将你加为TA的好友了<br/><a href='{$Object_url->mk_url(array('member','user',$_USER['user_id']))}'>查看他的消息</a>";
$Object_message_box->system_send_message ( $friend_id, $friend_info ['user_name'], $content, $title );
exit ( "添加好友成功" );