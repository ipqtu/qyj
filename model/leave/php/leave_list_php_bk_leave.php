<?php
//createTime:2012-12-11 16:18:23
 defined ( 'IS_ME' ) or exit (); 
$page = isset($_GET["page"]) ? abs(intval($_GET["page"])) : 0;
$leave_model_object = LEAVE_MODEL::get_object ( "bk_leave", "bk_leave" );
$all_leave_num = $leave_model_object->get_table_all_num();
$leave_star_num = System::get_page_star_num($page, 10, $all_leave_num);
$leave_all_result =$leave_model_object->get_all_value("ctime", $leave_star_num, 10);
$leave_page_html =System::get_page_html($page, 10, $all_leave_num, $Object_url->get_url());
$Object_template->assgin(array("leave_page_html"=>$leave_page_html,"leave_all_result"=>$leave_all_result))
?>