<?php
defined ( 'IS_ME' ) or exit ();
require_once APP_URL . APP . '/model/question_class.php';
$question_object = new Question ();
foreach ( $app_cache ['index_nav_type'] as $nav_id => $type_id ) {
	$index_show_question [$nav_id] = $question_object->get_value_by_where ( array ('type_id' => $type_id ), 'ctime', 0, 5 );
}
$Object_template->assign ( array ('app_cache' => $app_cache, 'index_show_question' => $index_show_question ,'index_nav_type'=>$app_cache ['index_nav_type']) );
$Object_template->display ( APP . '/index' );