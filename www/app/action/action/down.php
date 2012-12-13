<?php
defined ( 'IS_ME' ) or exit ();
isset ( $_URL [2] ) || Display::display_404_error ();
isset ( $_URL [3] ) || Display::display_404_error ();
$Object_user->is_login () || Display::display_nologin ();
$type = $_URL[2];
$action_id = $Object_filter->get_abs_int($_URL[3]);
($action_id > 0 ) || Display::display_404_error();
$action_info = $action_object->get_one_action($action_id);
empty ( $action_info ) && Display::display_back ( '该活动已经不存在了' );
require_once 'model/action_join_class.php';
$action_join_object = new Action_join();
$action_join_info = $action_join_object->get_action_join_info($action_info->id);
empty ( $action_join_info ) && Display::display_back ( '该活动还没有人报名' );
if($type == 'works'){
	echo 11;
}else{
	/** PHPExcel */
	require_once LIB_URL.'class_excel.php';
	// Create new PHPExcel object
	$objPHPExcel = new PHPExcel();
	// Set properties
	$objPHPExcel->getProperties()->setCreator("Maarten Balliauw")
								 ->setLastModifiedBy("Maarten Balliauw")
								 ->setTitle("Office 2007 XLSX Test Document")
								 ->setSubject("Office 2007 XLSX Test Document")
								 ->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
								 ->setKeywords("office 2007 openxml php")
								 ->setCategory("Test result file");
	// Add some data
	$sheet = $objPHPExcel->setActiveSheetIndex(0);
	$excel_num = $excel_num1 = array('A','B','C','D','E','F','G','H');
	$sheet->setCellValue(array_shift($excel_num1).'1', '编号');
	($action_info->action_need_name == 1) && $sheet->setCellValue(array_shift($excel_num1).'1', '姓名');
	($action_info->action_need_class == 1) && $sheet->setCellValue(array_shift($excel_num1).'1', '班级');
	($action_info->action_need_sex == 1) && $sheet->setCellValue(array_shift($excel_num1).'1', '性别');
	($action_info->action_need_tel == 1) && $sheet->setCellValue(array_shift($excel_num1).'1', '电话');
	($action_info->action_need_email == 1) && $sheet->setCellValue(array_shift($excel_num1).'1', 'E-mail');
	($action_info->action_need_works == 1) && $sheet->setCellValue(array_shift($excel_num1).'1', '附件');
	$sheet->setCellValue(array_shift($excel_num1).'1', '附加信息');
	$i = 2;
	foreach ($action_join_info as $join){
			$excel_num1 = $excel_num;
			$sheet->setCellValue(array_shift($excel_num1).$i,($i-1));
			($action_info->action_need_name == 1) && $sheet->setCellValue(array_shift($excel_num1).$i, $join->user_name);
			($action_info->action_need_class == 1) && $sheet->setCellValue(array_shift($excel_num1).$i,$join->user_class);
			($action_info->action_need_sex == 1) && $sheet->setCellValue(array_shift($excel_num1).$i,$join->user_sex);
			($action_info->action_need_tel == 1) && $sheet->setCellValue(array_shift($excel_num1).$i,$join->user_tel);
			($action_info->action_need_email == 1) && $sheet->setCellValue(array_shift($excel_num1).$i,$join->user_email);
			($action_info->action_need_works == 1) && $sheet->setCellValue(array_shift($excel_num1).$i,$join->user_works);
			$sheet->setCellValue(array_shift($excel_num1).$i,$join->user_append_info);
			$i++;
	} 
	 // Rename sheet
	$objPHPExcel->getActiveSheet()->setTitle('活动报名情况');
	// Set active sheet index to the first sheet, so Excel opens this as the first sheet
	//$objPHPExcel->setActiveSheetIndex(0);
	// Redirect output to a client’s web browser (Excel5)
	header('Content-Type: application/vnd.ms-excel');
	header('Content-Disposition: attachment;filename="活动报名情况.xls"');
	header('Cache-Control: max-age=0');
	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
	$objWriter->save('php://output');
	exit;
}