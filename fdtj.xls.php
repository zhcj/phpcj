<?php
if (!$usersort) {Header("Location:".$web_root."/");exit();}
$id=_get('id');
$subjectsort=_get('subjectsort');
$step=_get('step');
$fd=_get('fd');
$fdsort=_get('fdsort');
$fdnum=_get('fdnum');
$getstusort=_get('getstusort');
if (!$fdnum || $fdnum==1) {echo "<script>alert('分数段不能是“0”或“1”！');window.location='".$web_root."/?url=cj_sheets&id=".$id."&fd=".$fd."&subjectsort=".$subjectsort."&fdsort=".$fdsort."&step=".$step."&getstusort=".$getstusort."';</script>";exit();}
$fdnum=str_replace('，',';',$fdnum);
$fdnum=str_replace(',',';',$fdnum);
$fdnum=str_replace('；',';',$fdnum);
$fdnum=str_replace('－','-',$fdnum);
$fdnum=str_replace('_','-',$fdnum);
$fdnum=str_replace('—','-',$fdnum);
$result=$mysqli->query("select * from cj_data where id=$id");
$data=$result->fetch_array();
$table=$data['数据'];
if (!$table) {
	echo "<script>window.location='".$web_root."/?url=cj_sheets&id=".$id."';</script>";
	exit();
}
$subname=py2hz($subjectsort);
$wlsort=array('理'=>'0','文'=>'1');
if ($getstusort) {
	$temp3="and 类别 like '%".py2hz($getstusort)."%'";
	$temp4="where 类别 like '%".py2hz($getstusort)."%'";
	$claname=explode(",",explode(";",$data['班级'])[$wlsort[py2hz($getstusort)]]);
} else {
	$claname=explode(",",$data['班级']);
}
array_unshift($claname,'年级');
$clanum=count($claname);
$testname=num2text(substr($table,0,5)).'20'.substr($table,-4,2).'年'.str_replace('0','',substr($table,-2,1)).substr($table,-1).'月'.$data['考试'].'考试';
$sheetname=num2text(substr($table,0,5)).$data['年级'].$data['考试'].'考试分段统计表'.'(20'.substr($table,-4,2).'年'.str_replace('0','',substr($table,-2,1)).substr($table,-1).'月)';
$sheetname0='按';
if ($fd=='mc') $sheetname0.='名次';
elseif ($fd=='fs') $sheetname0.='分数';
$sheetname0.='统计';
if ($getstusort) $sheetname0.=py2hz($getstusort).'科';
$sheetname0.=py2hz($subjectsort);
if ($fd=='mc' || !$fd) $sheetname0.='前';
$sheetname0.=$fdnum;
if ($fd=='mc' || !$fd) $sheetname0.='名';
elseif ($fd=='fs') $sheetname0.='分数段';
$sheetname0.='的';
if ($fdsort=='fd') $sheetname0.='分段';
elseif ($fdsort=='lj') $sheetname0.='累计';
$sheetname0.='人数';
$fdmax=0;
if (strstr($fdnum,'-') && $fd!='fs') {
	echo "<script>alert('请输入正确的名次！');window.location='".$web_root."/?url=cj_sheets&id=".$id."&fd=".$fd."&subjectsort=".$subjectsort."&fdsort=".$fdsort."&step=".$step."&getstusort=".$getstusort."';</script>";
	exit();
} elseif (strstr($fdnum,'-')) {
	if (!$step) $step='10';
	$fdtmp=explode('-',$fdnum);
	$fdmax=$fdtmp['0'];
	sort($fdtmp);
	unset($fdnum);
	for ($i=$fdtmp['0'];$i<$fdtmp['1'];$i=$i+$step) $fdnum[]=$i;
	unset($fdtmp);
	rsort($fdnum);
} else $fdnum=explode(';',$fdnum);
$times=count($fdnum);
if ($fd=='mc') {
	for ($i=0;$i<$times;$i++) {
		$fdsqlrs=$mysqli->query("select * from $table $temp4 order by ".$subname." desc limit ".$fdnum[$i]);
		if ($fdsqlrs->data_seek($fdnum[$i]-1)) {
			$fddata=$fdsqlrs->fetch_array();
			$fdtmp[]=$fddata[py2hz($subjectsort)];
		}
	}
	unset($fdnum);
	$fdnum=$fdtmp;
	unset($fdtmp);
	$times=count($fdnum);
}
rsort($fdnum);
array_unshift($fdnum,$fdmax);
$times=$times+2;
$colstr=array('0'=>'A','1'=>'B','2'=>'C','3'=>'D','4'=>'E','5'=>'F','6'=>'G','7'=>'H','8'=>'I','9'=>'J','10'=>'K','11'=>'L','12'=>'M','13'=>'N','14'=>'O','15'=>'P','16'=>'Q','17'=>'R','18'=>'S','19'=>'T','20'=>'U','21'=>'V','22'=>'W','23'=>'X','24'=>'Y','25'=>'Z');
$objPHPExcel = new PHPExcel();
$objPHPExcel->getProperties()->setCreator("张春靖")
	 ->setTitle($testname)
	 ->setSubject("分段统计表")
	 ->setDescription($gradeshow."分段统计表");
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
$objPHPExcel->getActiveSheet()->getRowDimension($s)->setRowHeight(30);
$objPHPExcel->getActiveSheet()->getStyle('A'.$s.':'.$colstr[$clanum].$s)->applyFromArray($styleTitle);
$objPHPExcel->getActiveSheet()->setCellValue('A'.$s,$sheetname);
$objPHPExcel->getActiveSheet()->mergeCells('A'.$s.':'.$colstr[$clanum].$s);
$s++;
$objPHPExcel->getActiveSheet()->getStyle('A'.$s)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
$objPHPExcel->getActiveSheet()->setCellValue('A'.$s,$sheetname0);
$objPHPExcel->getActiveSheet()->mergeCells('A'.$s.':'.$colstr[$clanum].$s);
$s++;
$objPHPExcel->getActiveSheet()->getStyle('A'.$s.':'.$colstr[$clanum].$s)->applyFromArray($styleArray);
$objPHPExcel->getActiveSheet()->setCellValue('A'.$s,'分数线');
$objPHPExcel->getActiveSheet()->setCellValue('B'.$s,'年级');
for ($j=1;$j<$clanum;$j++) $objPHPExcel->getActiveSheet()->setCellValue($colstr[($j+1)].$s,$claname[$j].'班');
for ($i=1;$i<=$times;$i++){
	$temp1='';
	$temp2='';
	$s++;
	if ($i==$times-1) $fdname=$fdnum[$i-1].'以下';
	elseif ($i==$times) $fdname='合计';
	elseif ($fdsort=='lj') $fdname=$fdnum[$i].'以上';
	elseif ($fdsort=='fd' && $i==1) $fdname=$fdnum[$i].'以上';
	elseif ($fdsort=='fd') $fdname=$fdnum[$i].'-'.($fdnum[$i-1]-1);
	$objPHPExcel->getActiveSheet()->getStyle('A'.$s.':'.$colstr[$clanum].$s)->applyFromArray($styleArray);
	$objPHPExcel->getActiveSheet()->setCellValue('A'.$s,$fdname);
	for ($j=0;$j<$clanum;$j++){
		$temp5=$temp3;
		if ($j) $temp1="and 班级='{$claname[$j]}'";
		if ($j && $i==$times) $temp1="where 班级='{$claname[$j]}'";
		if ($i==$times-1) $temp2="where $subname<".$fdnum[$i-1];
		elseif ($i<$times-1) $temp2="where $subname>=".$fdnum[$i];
		if (!$temp2 && !$temp1) $temp5=$temp4;
		$ljsqlrs=$mysqli->query("select count(".$subname.") from $table $temp2 $temp1 $temp5");
		$ljsql=$ljsqlrs->fetch_array();
		if ($i==1) {
			$fdsqlrs=$mysqli->query("select count(".$subname.") from $table $temp2 $temp1 $temp3");
			$fdsql=$fdsqlrs->fetch_array();
		} elseif ($i==$times) {
			if (!$j) $fdsqlrs=$mysqli->query("select count(".$subname.") from $table where $subname<>0 $temp1 $temp3");
			else $fdsqlrs=$mysqli->query("select count(".$subname.") from $table where $subname<>0 and 班级='{$claname[$j]}' $temp3");
			$fdsql=$fdsqlrs->fetch_array();
		} else {
			$fdsqlrs=$mysqli->query("select count(".$subname.") from $table $temp2 and $subname<".$fdnum[$i-1]." $temp1 $temp3");
			$fdsql=$fdsqlrs->fetch_array();
		}
		if (!$ljsql['0'] && $i!=$times-1) $ljsql['0']='';
		if (!$fdsql['0'] && !$ljsql['0']) $fdsql['0']='';
		if ($i==$times || $i==$times-1) $fdvalue=$ljsql['0'];
		elseif ($fdsort=='lj') $fdvalue=$ljsql['0'];
		elseif ($fdsort=='fd') $fdvalue=$fdsql['0'];
		$objPHPExcel->getActiveSheet()->setCellValue($colstr[($j+1)].$s,$fdvalue);
	}
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
