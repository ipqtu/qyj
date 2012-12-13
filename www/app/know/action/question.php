<?php
defined ( 'IS_ME' ) or exit ();
isset ( $_URL [2] ) || Display::display_back ( '你访问的信息不存在' );
$question_id = abs ( intval ( $_URL [2] ) );
require_once APP_URL . APP . '/model/question_class.php';
$question_object = new Question ();
$question = $question_object->get_value_by_field ( 'id', $question_id );
empty ( $question ) && Display::display_back ( '你访问的信息不存在' );
$question = $question [0];
require_once APP_URL . APP . '/model/answer_class.php';
$answer_object = new Answer ();
//添加答案
if (! empty ( $_POST ['content'] ) && ($Object_user->is_login())) {
	//回复答案
	if (isset ( $_POST ['replay_id'] )) {
		$replay_id = abs ( intval ( $_POST ['replay_id'] ) );
		$replay_info = $answer_object->get_value_by_field ( 'id', $replay_id );
		if (isset ( $replay_info [0] )) {
			$replay_info = $replay_info [0];
			$f_id = ($replay_info->floor == 1) ? $replay_info->id : $replay_info->replay_fid;
			//获取最近楼
			$last_replay = $answer_object->get_value_by_where ( array ('replay_fid' => $f_id ), 'ctime', 0, 1 );
			$foor = (empty ( $last_replay )) ? 2 : array_pop ( $last_replay )->floor + 1;
			$answer_object->insert ( array ( 'qid' => $question->id, 'author_id' => $_USER ['user_id'], 'author_name' => $_USER ['user_name'], 'ctime' => time (), 'answer' => nl2br ( $_POST ['content'] ), 'replay_fid' => $f_id, 'replay_floor' => $replay_info->floor, 'floor' => $foor  ) );
			Display::display_dialog ( '你已经成功的回复' );
		}
	} else {
		$answer_object->insert ( array ('qid' => $question->id, 'author_id' => $_USER ['user_id'], 'author_name' => $_USER ['user_name'], 'ctime' => time (), 'answer' => nl2br ( $_POST ['content'] ), 'replay_fid' => 0, 'replay_floor' => 0, 'floor' => 1 ) );
		$question_object->auto_add_field('answer_num',array('id'=>$question->id));
		Display::display_dialog ( '你已经成功的回答这个问题，感谢你的回答' );
	}
}
//删除
if (isset ( $_GET ['del'] ) && ($_USER ['user_id'] == FOUNDER_ID)) {
	$del_id = abs ( intval ( $_GET ['del'] ) );
	$replay_info = $answer_object->get_value_by_field ( 'id', $del_id );
	if (isset ( $replay_info [0] )) {
		$replay_info = $replay_info [0];
		$answer_object->del ( array ('id' => $replay_info->id ) ); //本身
		$answer_object->del ( array ('replay_fid' => $replay_info->id ) ); //楼主
		$answer_object->del ( array ('replay_fid' => $replay_info->replay_fid, 'replay_floor' => $replay_info->floor ) ); //回复
	}
}
$question_object->auto_add_field('call_num',array('id'=>$question->id));
//获取答案
$page = (isset ( $_GET ['page'] )) ? abs ( intval ( $_GET ['page'] ) ) : 0;
$all_num = $answer_object->get_table_all_num ( array ('qid' => $question->id,'replay_fid'=>0) );
$star_num = System::get_page_star_num ( $page, 10, $all_num );
$page_html = System::get_page_html ( $page, 10, $all_num, $Object_url->get_url () );
$answers = $answer_object->get_value_by_where ( array ('qid' => $question->id,'replay_fid'=>0 ), 'ctime', $star_num, 10 );
$replay_answer = array ();
foreach ( $answers as $answer ) {
	$replay_answer [$answer->id] = $answer_object->get_value_by_field ( 'replay_fid', $answer->id, 'ctime', 0 );
}
$Object_template->assign ( array ('replay_answer'=>$replay_answer,'answers'=>$answers,'app_cache' => $app_cache, 'question' => $question, 'page_html' => $page_html ) );
$Object_template->display ( APP . '/question' );