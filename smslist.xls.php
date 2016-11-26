<?php
if ($usersort<5) {Header("Location:".$web_root."/");exit();}
$infors=$mysqli->query("select * from phpcj_user where id=$userid");
$info=$infors->fetch_array();
if (!$info['username']) {echo "<script>window.location='".$web_root."/'</script>";exit();}
$sjzgrade=$info['usergrade'];
$sjzmain=$info['mainteacher'];
$sjzclass=$info['userclass'];
$listsort=_get('listsort');
$grade=_get('grade');
$gradeshow=str_replace('g','高中',$sjzgrade);
$gradeshow=str_replace('c','初中',$gradeshow);
$gradeshow.='级';
if ($grade) {
	$result=$mysqli->query("select * from phpcj_student where usergrade='$sjzgrade' order by usergrade,username");
} elseif ($sjzmain) {
	$result=$mysqli->query("select * from phpcj_student where usergrade='$sjzgrade' and userclass='$sjzmain' order by username");
	$gradeshow.=$sjzmain.'班';
} else {
	echo '<script>window.location.href="'.$web_root.'/";</script>';
	exit();
}
$count=$result->num_rows;
if (!$count) {echo '<script>window.location.href="'.$web_root.'/?url=tea_smslist";</script>';exit();}
if ($grade) {
	$smsrs=$mysqli->query("select * from phpcj_sms where sms_grade='$usergrade'");
} else {
	$smsrs=$mysqli->query("select * from phpcj_sms where sms_class='$sjzmain' and sms_grade='$usergrade'");
}
while ($smsdata=$smsrs->fetch_array()) $smsnum[$smsdata['sms_class']]=$smsdata['sms_num'];
$colstr=array('0'=>'A','1'=>'B','2'=>'C','3'=>'D','4'=>'E');
$objPHPExcel = new PHPExcel();
$objPHPExcel->getProperties()->setCreator("张春靖")
	 ->setTitle("学生成绩查询账号密码")
	 ->setSubject("学生成绩查询账号密码")
	 ->setDescription($gradeshow."学生成绩查询账号密码");
$objPHPExcel->setActiveSheetIndex(0);
$objPHPExcel->getActiveSheet()->setTitle('学生账号密码');
$styleArray = array(
    'alignment' => array(
        'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
        'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
    ),
);
for ($i=0;$i<5;$i++) {
	$objPHPExcel->getActiveSheet()->getColumnDimension($colstr[$i])->setAutoSize(true);
}
$colname2='';
$colname3='';
$colname4='';
$colname5='';
$objPHPExcel->getActiveSheet()->getStyle('A1:E1')->applyFromArray($styleArray);
if ($listsort=='print') {
	$s=1;
	$colname2='姓名：';
	$colname3='账号：';
	$colname4='密码：';
	$colname5='网址：';
} else {
	$objPHPExcel->getActiveSheet()->setCellValue('A1','班级代码');
	$objPHPExcel->getActiveSheet()->setCellValue('B1','姓名');
	$objPHPExcel->getActiveSheet()->setCellValue('C1','账号');
	$objPHPExcel->getActiveSheet()->setCellValue('D1','密码');
	$objPHPExcel->getActiveSheet()->setCellValue('E1','网址');
	$s=2;
}
$objPHPExcel->getActiveSheet()->getDefaultRowDimension()->setRowHeight(20);
while ($data=$result->fetch_array()) {
	if (!$data['usercheck']) {
		$objPHPExcel->getActiveSheet()->getStyle('A'.$s.':'.'E'.$s)->applyFromArray($styleArray);
		if ($listsort!='print') $objPHPExcel->getActiveSheet()->setCellValue('A'.$s,$smsnum[$data['userclass']]);
		$objPHPExcel->getActiveSheet()->setCellValue('B'.$s,$colname2.$data['realname']);
		$objPHPExcel->getActiveSheet()->setCellValue('C'.$s,$colname3.$data['username']);
		$objPHPExcel->getActiveSheet()->setCellValue('D'.$s,$colname4.$data['password']);
		$objPHPExcel->getActiveSheet()->setCellValue('E'.$s,$colname5.'test.phpcj.net');

		if ($listsort=='print') {
			$s++;
			$objPHPExcel->getActiveSheet()->getStyle('A'.$s.':'.'E'.$s)->applyFromArray($styleArray);
			$objPHPExcel->getActiveSheet()->setCellValue('A'.$s,'');
			$objPHPExcel->getActiveSheet()->setCellValue('B'.$s,'');
			$objPHPExcel->getActiveSheet()->setCellValue('C'.$s,'');
			$objPHPExcel->getActiveSheet()->setCellValue('D'.$s,'');
			$objPHPExcel->getActiveSheet()->setCellValue('E'.$s,'');
		}
		$s++;
	}
}
header('Content-Type: application/vnd.ms-excel');
//header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
$filename =$gradeshow.'学生账号与密码.xls';
header("".xlsname($filename)."");
header('Cache-Control: max-age=0');
// If you're serving to IE 9, then the following may be needed
//header('Cache-Control: max-age=1');
// If you're serving to IE over SSL, then the following may be needed
header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
header ('Cache-Control: cache, must-revalidate');
header ('Pragma: public');
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
//$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save('php://output');
