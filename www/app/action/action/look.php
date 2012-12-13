<?php
defined ( 'IS_ME' ) or exit ();
$time_type = isset ( $_URL [2] ) ? $Object_filter->get_abs_int ( $_URL [2] ) : 0;
$type = isset ( $_URL [3] ) ? intval ( $_URL [3] ) : - 1;
$sql = "";
switch ($time_type) {
	case 1 :
		{
			//今天
			$now_time = time ();
			$tomorrow_time = strtotime ( 'tomorrow' );
			$sql = " AND `action_end_time` > {$now_time} AND `action_end_time` <" . $tomorrow_time;
			break;
		}
	case 2 :
		{
			//明天
			$tomorrow_time = strtotime ( 'tomorrow' );
			$sql = " AND `action_end_time` > {$tomorrow_time} AND `action_end_time` <" . ($tomorrow_time + 86400);
			break;
		}
	case 3 :
		{
			//本周
			$week_end_time = strtotime ( "next Monday" );
			$week_star_time = $week_end_time - 604800;
			$sql = " AND `action_end_time` > {$week_star_time} AND `action_end_time` <" . $week_end_time;
			break;
		}
}

$sql .= ($type < 0) ? '' : ' AND `action_type_id` = ' . ($type % count ( $app_cache ['action_type'] ));
$all_actions = $action_object->get_action_by_where ( $sql, 0, 4 );
$Object_template->assign ( array ('all_actions' => $all_actions, 'time_type' => $time_type, 'type' => $type, 'action_types' => $app_cache ['action_type'] ) );
$Object_template->display ( APP.'action/look' );