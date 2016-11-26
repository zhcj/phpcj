<?php
if ($usersort<5) {Header("Location:".$web_root."/");exit();}
$infors=$mysqli->query("select * from phpcj_user where id=$userid");
$info=$infors->fetch_array();
if (!$info['username']) {echo "<script>window.location='".$web_root."/'</script>";exit();}
$sjzgrade=$info['usergrade'];
$sjzmain=$info['mainteacher'];
$sjzclass=$info['userclass'];
$sms=_get('sms');
$grade=_get('grade');
if ($sms!='') $sheetname='家校通';
else $sheetname='校讯通';
$gradeshow=str_replace('g','高中',$sjzgrade);
$gradeshow=str_replace('c','初中',$gradeshow);
$gradeshow.='级';
$result=$mysqli->query("select * from cj_data where 现在='1' and 数据 like '".$sjzgrade."%'");
$data=$result->fetch_array();
$table=$data['数据'];
if (!$table) {echo "<script>window.location='".$web_root."/'</script>";exit();}
if ($data['文理']) {
	$clatmp=explode(";",$data['班级']);
	if (in_array($sjzmain,explode(",",$clatmp[0]))) $subname=explode(";",$data['科目'])[0];
	elseif (in_array($sjzmain,explode(",",$clatmp[1]))) $subname=explode(";",$data['科目'])[1];
	$subname=explode(",",$subname);
} else $subname=explode(",",$data['科目']);
$subname[]='总分';
$subnum=count($subname);
if ($grade) {
	$smsrs=$mysqli->query("select * from phpcj_sms where sms_grade='$usergrade'");
} else {
	$smsrs=$mysqli->query("select * from phpcj_sms where sms_class='$sjzmain' and sms_grade='$usergrade'");
}
while ($smsdata=$smsrs->fetch_array()) $smsnum[$smsdata['sms_class']]=$smsdata['sms_num'];
$sturs=$mysqli->query("select * from $table where 班级='$sjzmain'");
$colstr=array('0'=>'A','1'=>'B','2'=>'C','3'=>'D','4'=>'E','5'=>'F','6'=>'G','7'=>'H','8'=>'I','9'=>'J','10'=>'K','11'=>'L','12'=>'M','13'=>'N','14'=>'O','15'=>'P','16'=>'Q','17'=>'R','18'=>'S','19'=>'T','20'=>'U','21'=>'V','22'=>'W','23'=>'X','24'=>'Y','25'=>'Z');
$objPHPExcel = new PHPExcel();
$objPHPExcel->getProperties()->setCreator("张春靖")
	 ->setTitle("最近一次考试学生成绩")
	 ->setSubject("学生成绩")
	 ->setDescription($gradeshow."最近一次考试学生成绩");
$objPHPExcel->setActiveSheetIndex(0);
$objPHPExcel->getActiveSheet()->setTitle('学生成绩'.$sheetname.'版');
$objPHPExcel->getActiveSheet()->getDefaultRowDimension()->setRowHeight(20);
$styleArray = array(
    'alignment' => array(
        'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
        'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
    ),
);
if ($sms=='jxt') $colnum=$subnum+1;
else $colnum=$subnum+2;
for ($i=0;$i<$colnum;$i++) {
	$objPHPExcel->getActiveSheet()->getColumnDimension($colstr[$i])->setAutoSize(true);
}
$s=1;
$objPHPExcel->getActiveSheet()->getStyle('A'.$s.':'.$colstr[($colnum-1)].$s)->applyFromArray($styleArray);
if ($sms=='jxt') {
	$objPHPExcel->getActiveSheet()->setCellValue('A1','学生/科目');
	$startnum=1;
} else {
	$objPHPExcel->getActiveSheet()->setCellValue('A1','班级代码');
	$objPHPExcel->getActiveSheet()->setCellValue('B1','姓名');
	$startnum=2;
}
for ($i=$startnum;$i<$colnum;$i++) {
	$objPHPExcel->getActiveSheet()->setCellValue($colstr[$i].'1',$subname[$i-$startnum]);
}
if ($grade) $sturs=$mysqli->query("select * from $table order by 班级,总分 desc");
else $sturs=$mysqli->query("select * from $table where 班级='$sjzmain'");
while ($studata=$sturs->fetch_array()) {
	$s++;
	$objPHPExcel->getActiveSheet()->getStyle('A'.$s.':'.$colstr[($colnum-1)].$s)->applyFromArray($styleArray);
	if ($sms!='jxt') $objPHPExcel->getActiveSheet()->setCellValue($colstr[$startnum-2].$s,$smsnum[$studata['班级']]);
	$objPHPExcel->getActiveSheet()->setCellValue($colstr[$startnum-1].$s,$studata['姓名']);
	for ($j=$startnum;$j<$colnum;$j++) $objPHPExcel->getActiveSheet()->setCellValue($colstr[$j].$s,$studata[$subname[$j-$startnum]]);
}
header('Content-Type: application/vnd.ms-excel');
//header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
$filename =$gradeshow.'最近一次考试成绩短信'.$sheetname.'版.xls';
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
