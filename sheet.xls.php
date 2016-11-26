<?php
if (!$usersort) {Header("Location:".$web_root."/");exit();}
$id=_get('id');
$sheet=_get('sheet');
$class=_get('class');
$sheetsort=_get('sheetsort');
$url=$web_root.'/?url=sheet.xls&id='.$id.'&sheet='.$sheet.'&sheetsort='.$sheetsort;
$class=str_replace("班","",$class);
$class=str_replace("年级","",$class);
$result=$mysqli->query("select * from cj_data where id='$id'");
$data=$result->fetch_array();
$table=$data['数据'];
if (!$table) {
	echo "<script>window.location='".$web_root."/?url=cj_sheets&id=".$id."';</script>";
	exit();
}
if (ctype_digit($class))	{
	$clatmp=explode(";",$data['班级']);
	if (in_array($class,explode(",",$clatmp[0]))) $stusort=explode(";",$data['文理'])[0];
	elseif (in_array($class,explode(",",$clatmp[1]))) $stusort=explode(";",$data['文理'])[1];
	else {
		echo "<script>window.location='".$web_root."/?url=cj_index';</script>";
		exit();
	}
} else $stusort=$class;
$wlsort=array('理'=>'0','文'=>'1');
if ($stusort) $subname=explode(",",explode(";",$data['科目'])[$wlsort[$stusort]]);
elseif ($data['文理']) $subname=explode(",",explode(";",$data['科目'])[0]);
else $subname=explode(",",$data['科目']);
$subnum=count($subname);
$subjectsort='';
if (_get('subjectsortw') && $stusort=='文') $subjectsort=_get('subjectsortw');
if (_get('subjectsortl') && $stusort=='理') $subjectsort=_get('subjectsortl');
$subjectsort=py2hz($subjectsort);
$tmpsub[]='姓名';
if ($class=='文' || $class=='理' || !$class) $tmpsub[]='班级';
$tmpsub[]='年名';
if ($sheet=='jinbu1' || $sheet=='jinbu2') 	$tmpsub[]='年变';
if (is_numeric($class)) $tmpsub[]='班名';
if ($sheet=='jinbu1' || $sheet=='jinbu2') 	$tmpsub[]='班变';
for ($i=0;$i<$subnum;$i++) {
	$tmpsub[]=$subname[$i];
	if ($sheetsort==1 || $sheetsort==3) $tmpsub[]=substr($subname[$i],0,3)."排";
	if ($sheetsort==2 || $sheetsort==3) $tmpsub[]=substr($subname[$i],0,3)."序";
}
$tmpsub[]='总分';
$subname=$tmpsub;
$subnum=count($subname);
$testname=num2text(substr($table,0,5)).'20'.substr($table,-4,2).'年'.str_replace('0','',substr($table,-2,1)).substr($table,-1).'月'.$data['年级'].$data['考试'].'考试';
$sheetname=num2text(substr($table,0,5));
if (is_numeric($class)) $sheetname.=$class.'班';
else $sheetname.=$class.'科';
$sheetname.=$data['年级'].$data['考试'].'考试';
if ($sheet=='cjt') $tabname='成绩条';
elseif ($sheet=='jinbu1' || $sheet=='jinbu2') $tabname='名次变化';
elseif (!$sheet || $sheet=='cjd') $tabname='成绩单';
$sheetname.=$tabname.'(20'.substr($table,-4,2).'年'.str_replace('0','',substr($table,-2,1)).substr($table,-1).'月)';
$colstr=array('0'=>'A','1'=>'B','2'=>'C','3'=>'D','4'=>'E','5'=>'F','6'=>'G','7'=>'H','8'=>'I','9'=>'J','10'=>'K','11'=>'L','12'=>'M','13'=>'N','14'=>'O','15'=>'P','16'=>'Q','17'=>'R','18'=>'S','19'=>'T','20'=>'U','21'=>'V','22'=>'W','23'=>'X','24'=>'Y','25'=>'Z');
$objPHPExcel = new PHPExcel();
$objPHPExcel->getProperties()->setCreator("张春靖")
	 ->setTitle($testname)
	 ->setSubject("成绩单")
	 ->setDescription($gradeshow."成绩单");
$styleArray = array(
    'alignment' => array(
        'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
        'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
    ),
    'borders' => array(
        'allborders' => array(
            'style' => PHPExcel_Style_Border::BORDER_THIN,
        ),
    ),
);
$styleTitle = array(
    'font' => array(
        'bold' => true,
        'name' => '黑体',
        'size' => '15',
    ),
    'alignment' => array(
        'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
        'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
    ),
);
$objPHPExcel->setActiveSheetIndex(0);
$objPHPExcel->getActiveSheet()->setTitle($sheetname);
$objPHPExcel->getActiveSheet()->getDefaultRowDimension()->setRowHeight(20);
$s=1;
for($k=0;$k<$subnum;$k++) {
	$objPHPExcel->getActiveSheet()->getColumnDimension($colstr[$k])->setAutoSize(true); 
}
$objPHPExcel->getActiveSheet()->getRowDimension($s)->setRowHeight(30);
$objPHPExcel->getActiveSheet()->getStyle('A'.$s.':'.$colstr[$subnum].$s)->applyFromArray($styleTitle);
$objPHPExcel->getActiveSheet()->setCellValue('A'.$s,$sheetname);
$objPHPExcel->getActiveSheet()->mergeCells('A'.$s.':'.$colstr[($subnum-1)].$s);
$s++;
$objPHPExcel->getActiveSheet()->getStyle('A'.$s.':'.$colstr[($subnum-1)].$s)->applyFromArray($styleArray);
for ($i=0;$i<$subnum;$i++) $objPHPExcel->getActiveSheet()->setCellValue($colstr[$i].$s,$subname[$i]);
$tmp2='总分';
if ($subjectsort && $class) $tmp2=$subjectsort;
if ($sheet=='jinbu1') $tmp2='年变';
if ($sheet=='jinbu2') $tmp2='班变';
if (!$class) $tmp1='';
elseif (is_numeric($class)) $tmp1="where 班级='$class'";
else $tmp1="where 类别='$class'";
$kemurs=$mysqli->query("select * from $table $tmp1 order by $tmp2 desc");
$tmp='';
while($kemu=$kemurs->fetch_array()){
	if ($sheet=='cjt' && $tmp) {
		$s++;
		$objPHPExcel->getActiveSheet()->getStyle('A'.$s.':'.$colstr[($subnum-1)].$s)->applyFromArray($styleArray);
		for ($i=0;$i<$subnum;$i++) $objPHPExcel->getActiveSheet()->setCellValue($colstr[$i].$s,$subname[$i]);
	}
	$s++;
	$objPHPExcel->getActiveSheet()->getStyle('A'.$s.':'.$colstr[($subnum-1)].$s)->applyFromArray($styleArray);
	for ($i=0;$i<$subnum;$i++) $objPHPExcel->getActiveSheet()->setCellValue($colstr[$i].$s,$kemu[$subname[$i]]);
	if ($sheet=='cjt') $s++;
	if (!$tmp) $tmp='1';
}
header('Content-Type: application/vnd.ms-excel');
$filename =$testname.'.xls';
header("".xlsname($filename)."");
header('Cache-Control: max-age=0');
// If you're serving to IE 9, then the following may be needed
//header('Cache-Control: max-age=1');
// If you're serving to IE over SSL, then the following may be needed
//header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
//header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
//header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
//header ('Pragma: public'); // HTTP/1.0
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
$objWriter->save('php://output');
