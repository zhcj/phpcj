<?php
if (!$usersort) {Header("Location:".$web_root."/");exit();}
$id=_get('id');
$fd=_get('fd');
$fdnum=_get('fdnum');
$stusort=_get('getstusort');
$fdnum=str_replace('，',';',$fdnum);
$fdnum=str_replace(',',';',$fdnum);
$fdnum=str_replace('；',';',$fdnum);
if (!$fdnum || $fdnum==1) {echo "<script>alert('分数段不能是“0”或“1”！');window.location='".$web_root."/?url=cj_sheets&id=".$id."&fd=".$fd."&stusort=".$stusort."';</script>";exit();}
$result=$mysqli->query("select * from cj_data where id=".$id);
$data=$result->fetch_array();
$table=$data['数据'];
if (!count($table)) {
	echo "<script>window.location='".$web_root."/?url=cj_index';</script>";
	exit();
}
$sheetname=num2text(substr($table,0,5));
$sheetname.=$data['年级'].$data['考试'];
if ($stusort) $sheetname.=py2hz($stusort).'科';
$sheetname.='等效人数统计表';
$sheetname.='(20'.substr($table,-4,2).'年'.str_replace('0','',substr($table,-2,1)).substr($table,-1).'月)';
$fdnum=explode(';',$fdnum);
if ($fd=='mc') sort($fdnum);
elseif ($fd=='fs') rsort($fdnum);
$times=count($fdnum);
$wlsort=array('l'=>'0','w'=>'1');
if ($stusort) {
	$subname=explode(",",explode(";",$data['科目'])[$wlsort[$stusort]]);
	$claname=explode(",",explode(";",$data['班级'])[$wlsort[$stusort]]);
	$tmpsql1="where 类别 like '%".py2hz($stusort)."%'";
	$tmpsql2="and 类别 like '%".py2hz($stusort)."%'";
} else {
	$subname=explode(",",$data['科目']);
	$claname=explode(",",$data['班级']);
}
$subnum=count($subname);
$clanum=count($claname);
if ($fd=='fs') {$fdfs=$fdnum;unset($fdnum);}
for ($i=0;$i<$times;$i++) {
	if ($fd=='mc') {
		$dxfsqlrs=$mysqli->query("select * from $table $tmpsql1 order by 总分 desc limit ".$fdnum[$i]);
		if ($dxfsqlrs->data_seek($fdnum[$i]-1)) $dxfdata=$dxfsqlrs->fetch_array();
		$dxftable[$i][$fdnum[$i]]['总分']=$dxfdata['总分'];
		unset($dxfdata);
	} elseif ($fd='fs') {
		$dxfdatars=$mysqli->query("select count(总分) from $table where 总分>='".$fdfs[$i]."' $tmpsql2");
		$dxfdata=$dxfdatars->fetch_array();
		$fdnum[]=$dxfdata[0];
		unset($dxfdata);
		$dxftable[$i][$fdnum[$i]]['总分']=$fdfs[$i];
	}
	for ($k=0;$k<$clanum;$k++) {
		$dxfdatars=$mysqli->query("select count(总分) from $table where 班级='".$claname[$k]."' and 总分>='".$dxftable[$i][$fdnum[$i]]['总分']."'");
		$dxfdata=$dxfdatars->fetch_array();
		$dxftable[$i][$claname[$k]][$fdnum[$i]]['总分']=$dxfdata[0];
		$dxftable[$i]['合计'][$fdnum[$i]]['总分']=$dxftable[$i]['合计'][$fdnum[$i]]['总分']+$dxfdata[0];
		unset($dxfdata);
	}
	for ($j=0;$j<$subnum;$j++) {
		$dxfdatars=$mysqli->query("select * from $table $tmpsql1 order by ".$subname[$j]." desc limit ".$fdnum[$i]);
		if ($dxfdatars->data_seek($fdnum[$i]-1)) $dxfdata=$dxfdatars->fetch_array();
		$dxftable[$i][$fdnum[$i]][$subname[$j]]=$dxfdata[$subname[$j]];
		unset($dxfdata);
		for ($k=0;$k<$clanum;$k++) {
			$dxfdatars=$mysqli->query("select count(".$subname[$j].") from $table where 班级='".$claname[$k]."' and 总分>='".$dxftable[$i][$fdnum[$i]]['总分']."' and ".$subname[$j].">='".$dxftable[$i][$fdnum[$i]][$subname[$j]]."'");
			$dxfdata=$dxfdatars->fetch_array();
			$dxftable[$i][$claname[$k]][$fdnum[$i]][$subname[$j]]=$dxfdata[0];
			$dxftable[$i]['合计'][$fdnum[$i]][$subname[$j]]=$dxftable[$i]['合计'][$fdnum[$i]][$subname[$j]]+$dxfdata[0];
			unset($dxfdata);
		}
	}
}
$claname[]='合计';
$clanum=count($claname);
array_unshift($subname,'总分');
$subnum=count($subname);
$colstr=array('0'=>'A','1'=>'B','2'=>'C','3'=>'D','4'=>'E','5'=>'F','6'=>'G','7'=>'H','8'=>'I','9'=>'J','10'=>'K','11'=>'L','12'=>'M','13'=>'N','14'=>'O','15'=>'P','16'=>'Q','17'=>'R','18'=>'S','19'=>'T','20'=>'U','21'=>'V','22'=>'W','23'=>'X','24'=>'Y','25'=>'Z');
$objPHPExcel = new PHPExcel();
$objPHPExcel->getProperties()->setCreator("张春靖")
	 ->setTitle($testname)
	 ->setSubject("等效分统计表")
	 ->setDescription($gradeshow."等效分统计表");
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
$objPHPExcel->getActiveSheet()->getStyle('A'.$s.':'.$colstr[$subnum].$s)->applyFromArray($styleTitle);
$objPHPExcel->getActiveSheet()->setCellValue('A'.$s,$sheetname);
$objPHPExcel->getActiveSheet()->mergeCells('A'.$s.':'.$colstr[$subnum].$s);
for ($i=0;$i<$times;$i++) {
	$s++;
	$objPHPExcel->getActiveSheet()->getStyle('A'.$s)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        if ($fd=='mc') $objPHPExcel->getActiveSheet()->setCellValue('A'.$s,'按名次统计前'.$fdnum[$i].'名的等效分和等效人数');
	elseif ($fd=='fs') $objPHPExcel->getActiveSheet()->setCellValue('A'.$s,'按分数统计'.$fdfs[$i].'分及以上的等效分和等效人数');
	$objPHPExcel->getActiveSheet()->mergeCells('A'.$s.':'.$colstr[$subnum].$s);
	$s++;
	$objPHPExcel->getActiveSheet()->getStyle('A'.$s.':'.$colstr[$subnum].$s)->applyFromArray($styleArray);
	$objPHPExcel->getActiveSheet()->setCellValue('A'.$s,'班级');
	for ($j=0;$j<$subnum;$j++) $objPHPExcel->getActiveSheet()->setCellValue($colstr[($j+1)].$s,$subname[$j].'('.$dxftable[$i][$fdnum[$i]][$subname[$j]].')');
	for ($k=0;$k<$clanum;$k++) {
		$s++;
		$objPHPExcel->getActiveSheet()->getStyle('A'.$s.':'.$colstr[$subnum].$s)->applyFromArray($styleArray);
		if ($claname[$k]=='合计') $objPHPExcel->getActiveSheet()->setCellValue('A'.$s,$claname[$k]);
		else $objPHPExcel->getActiveSheet()->setCellValue('A'.$s,$claname[$k].'班');
		for ($j=0;$j<$subnum;$j++) {
			if ($dxftable[$i][$claname[$k]][$fdnum[$i]][$subname[$j]]) $objPHPExcel->getActiveSheet()->setCellValue($colstr[($j+1)].$s,$dxftable[$i][$claname[$k]][$fdnum[$i]][$subname[$j]]);
		}
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
