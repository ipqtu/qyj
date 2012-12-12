<?php
//createTime:2012-12-11 16:18:23
 defined ( 'IS_ME' ) or exit (); 
$post_data ["leave_content"] = $Object_filter->filter_edit_content ( $Object_filter->get_real_gpc_var ( "leave_content" ) );
(empty($post_data ["leave_content"])) && Display::display_dialog("留言不能为空");
$sql_data_array["content"] = (isset($post_data["leave_content"])) ? $post_data["leave_content"]:"";
$sql_data_array["author_id"]=$_USER["user_id"];
$sql_data_array["author_name"]=$_USER["user_name"];
$sql_data_array["ctime"]=time();
$leave_model_object = LEAVE_MODEL::get_object ( "bk_leave", "bk_leave" );
$leave_model_result = $leave_model_object->insert($sql_data_array);
?>