<?php
defined ( 'IS_ME' ) or exit ();
($Object_user->is_login ()) || Display::display_nologin ();
require_once APP_URL . APP . '/model/user_class.php';
$know_user_info_object = new User_info ();
$user_credits = $know_user_info_object->get_user_info ( $_USER ['user_id'], 'credits' );
if (! empty ( $_POST )) {
	(! empty ( $_POST ['title'] )) || Display::display_back ( '问题的标题不能为空' );
	$type_id = abs ( intval ( $_POST ['type_id'] ) );
	(key_exists ( $type_id, $app_cache ['type'] )) || Display::display_back ( '问题的类型不存在' );
	$content = nl2br ( $_POST ['content'] );
	$credits = abs ( intval ( $_POST ['credits'] ) );
	($credits > 0) || Display::display_back ( '悬赏积分必须大于0' );
	($credits <= $user_credits) || Display::display_back ( '你的积分不足，请补充积分' );
	require_once APP_URL . APP . '/model/question_class.php';
	$question_object = new Question ();
	$result = $question_object->insert ( array ('author_id' => $_USER ['user_id'], 'author_name' => $_USER ['user_name'], 'title' => $_POST ['title'], 'type_id' => $type_id, 'quesstion' => $content, 'ctime' => time (), 'spend_credits' => $credits ) );
	$question_id = $Object_mysql->get_insert_id ();
	if ($result > 0) {
		//先扣除积分
		$know_user_info_object->change_user_attr_value ( $_USER ['user_id'], 'credits', $user_credits - $credits );
		//记录积分
		require_once APP_URL . APP . '/model/user_credits.php';
		$user_credits_object = new User_credits ();
		$user_credits_object->insert ( array ('uid' => $_USER ['user_id'], 'user_name' => $_USER ['user_name'], 'qid' => $question_id, 'q_title' => $_POST ['title'], 'credits' => $credits, 'to_uid' => 0, 'to_user_name' => '', 'ctime' => time (), 'application' => 0 ) );
		Display::display_dialog ( '你已经成功发布问题' );
	} else {
		Display::display_dialog ( '发布问题失败' );
	}

}
$Object_template->assign ( array ('app_cache' => $app_cache, 'user_credits' => $user_credits ) );
$Object_template->display ( APP . '/public' );