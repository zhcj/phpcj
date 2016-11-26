<?php
if (!$usersort) {Header("Location:".$web_root."/");exit();}
$id=_get('id');
$info=_get('info');
$gradetop=_get('gradetop');
if ($gradetop && !ctype_digit($gradetop)) {
	echo "<script>window.location='".$web_root."/?url=cj_sheets';</script>";
	exit();
}
if ($info=='详表' || $info=='more') $info='more';
else $info='';
$result=$mysqli->query("select * from cj_data where id='$id'");
$data=$result->fetch_array();
$table=$data['数据'];
$sheetname=num2text(substr($table,0,5)).$data['年级'].$data['考试'].'成绩分析表'.'(20'.substr($table,-4,2).'年'.str_replace('0','',substr($table,-2,1)).substr($table,-1).'月)';
$testname=num2text(substr($table,0,5)).'20'.substr($table,-4,2).'年'.str_replace('0','',substr($table,-2,1)).substr($table,-1).'月'.$data['年级'].$data['考试'].'考试';
if (!$data) {echo "<script>window.location='".$web_root."/?url=cj_sheets&id=".$id."';</script>";exit();}
if ($data['文理']) {
	$stusort=explode(";",$data['文理']);
	$sub_name=explode(";",$data['科目']);
	$cla_name=explode(";",$data['班级']);
	$sortnum=count($stusort);
} else {
	$stusort=array('');
	$sub_name[]=$data['科目'];
	$cla_name[]=$data['班级'];
	$sortnum=1;
}
$cj_level=explode(',',$cj_level);
if (!$cj_level[0]) {$cj_itemmoretmp=str_replace('及格率,','',$cj_itemmoretmp);$cj_itemtmp=str_replace('及格率,','',$cj_itemtmp);}
if (!$cj_level[1]) {$cj_itemmoretmp=str_replace('良好率,','',$cj_itemmoretmp);$cj_itemtmp=str_replace('良好率,','',$cj_itemtmp);}
if (!$cj_level[2]) {$cj_itemmoretmp=str_replace('优秀率,','',$cj_itemmoretmp);$cj_itemtmp=str_replace('优秀率,','',$cj_itemtmp);}
if ($info) $allname=explode(',',$cj_itemmoretmp);
else $allname=explode(',',$cj_itemtmp);
$allnum=count($allname);
$colstr=array('0'=>'A','1'=>'B','2'=>'C','3'=>'D','4'=>'E','5'=>'F','6'=>'G','7'=>'H','8'=>'I','9'=>'J','10'=>'K','11'=>'L','12'=>'M','13'=>'N','14'=>'O','15'=>'P','16'=>'Q','17'=>'R','18'=>'S','19'=>'T','20'=>'U','21'=>'V','22'=>'W','23'=>'X','24'=>'Y','25'=>'Z');
$objPHPExcel = new PHPExcel();
$objPHPExcel->getProperties()->setCreator("张春靖")
	 ->setTitle($testname)
	 ->setSubject("两率一分分析表")
	 ->setDescription($gradeshow."两率一分分析表");
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
for ($o=0;$o<$sortnum;$o++) {
	$claname=explode(",",$cla_name[$o]);
	array_unshift($claname,'校');
	$clanum=count($claname);
	if ($gradetop && !$o) $sheetname.=' [各班前'.$gradetop.'名] ';
	$subname=explode(",",$sub_name[$o]);
	$subname[]='总分';
	$subnum=count($subname);
	$scoretmp=0;
	for ($i=0;$i<$subnum-1;$i++) {
		$score[$subname[$i]]=$data[$subname[$i]];
		$scoretmp=$scoretmp+$data[$subname[$i]];
	}
	$score['总分']=$scoretmp;
	unset($scoretmp);
	for ($i=0;$i<$clanum;$i++) {
		$tmp1='';
		$tmp2='';
		$clars=$mysqli->query("select * from cj_teacher where 数据='$table' and 班级='{$claname[$i]}'");
		$cladata=$clars->fetch_array();
		if ($claname[$i]!='校') {
			$tmp1=" where 班级='{$claname[$i]}'";
			$tmp2=" and 班级='{$claname[$i]}'";
			if ($stusort[$o]) {$tmp1.=" and 类别='{$stusort[$o]}'";$tmp2.=" and 类别='{$stusort[$o]}'";}
		} else {
			if ($stusort[$o]) {$tmp1=" where 类别='{$stusort[$o]}'";
			$tmp2=" and 类别='{$stusort[$o]}'";}
		}
		if ($gradetop) $topselect="select * from $table $tmp1 order by 总分 desc limit $gradetop";
		for ($k=0;$k<$subnum;$k++) {
			$countrs0=$mysqli->query("select count(".$subname[$k].") from $table $tmp1");
			$count0=$countrs0->fetch_array();
			if ($gradetop && $gradetop<$count0['0']) $count0['0']=$gradetop;
			if (in_array('参考人数',$allname)) {
				$tableall[$o][$subname[$k]]['参考人数'][$claname[$i]]=$count0['0'];
			}
			if (in_array('平均分',$allname)) {
				if ($gradetop) {
					$avgrs=$mysqli->query("select avg(".$subname[$k].") from ($topselect) as a $tmp1");
					$avg=$avgrs->fetch_array();
				} else {
					$avgrs=$mysqli->query("select avg(".$subname[$k].") from $table $tmp1");
					$avg=$avgrs->fetch_array();
				}
				$tableall[$o][$subname[$k]]['平均分'][$claname[$i]]=number_format($avg['0'],2);
			}
			if (in_array('及格率',$allname)  || in_array('及格人数',$allname)) {
				if ($gradetop) {
					$countrs1=$mysqli->query("select count(".$subname[$k].") from ($topselect) as a where ".$subname[$k].">=".($score[$subname[$k]]*$cj_level[0]).$tmp2);
					$count1=$countrs1->fetch_array();
				} else {
					$countrs1=$mysqli->query("select count(".$subname[$k].") from $table where ".$subname[$k].">=".($score[$subname[$k]]*$cj_level[0]).$tmp2);
					$count1=$countrs1->fetch_array();
				}
				$tableall[$o][$subname[$k]]['及格人数'][$claname[$i]]=$count1['0'];
				if ($cj_level[0]) $tableall[$o][$subname[$k]]['及格率'][$claname[$i]]=number_format(round($count1['0']/$count0['0']*100,2),2);
			}
			if (in_array('良好率',$allname) || in_array('良好人数',$allname)) {
				if ($gradetop) {
					$countrs2=$mysqli->query("select count(".$subname[$k].") from ($topselect) as a where ".$subname[$k].">=".($score[$subname[$k]]*$cj_level[1]).$tmp2);
					$count2=$countrs2->fetch_array();
				} else {
					$countrs2=$mysqli->query("select count(".$subname[$k].") from $table where ".$subname[$k].">=".($score[$subname[$k]]*$cj_level[1]).$tmp2);
					$count2=$countrs2->fetch_array();
				}
				$tableall[$o][$subname[$k]]['良好人数'][$claname[$i]]=$count2['0'];
				if ($cj_level[1]) $tableall[$o][$subname[$k]]['良好率'][$claname[$i]]=number_format(round($count2['0']/$count0['0']*100,2),2);
			}
			if (in_array('优秀率',$allname) || in_array('优秀人数',$allname)) {
				if ($gradetop) {
					$countrs3=$mysqli->query("select count(".$subname[$k].") from ($topselect) as a where ".$subname[$k].">=".($score[$subname[$k]]*$cj_level[2]).$tmp2);
					$count3=$countrs3->fetch_array();
				} else {
					$countrs3=$mysqli->query("select count(".$subname[$k].") from $table where ".$subname[$k].">=".($score[$subname[$k]]*$cj_level[2]).$tmp2);
					$count3=$countrs3->fetch_array();
				}
				$tableall[$o][$subname[$k]]['优秀人数'][$claname[$i]]=$count3['0'];
				if ($cj_level[2]) $tableall[$o][$subname[$k]]['优秀率'][$claname[$i]]=number_format(round($count3['0']/$count0['0']*100,2),2);
			}
//标准差
//			$stdev=mysql_fetch_array(mysql_query("select stdev(".$subname[$k].") from $table $tmp1"));
//			$tableall[$claname[$i]][$subname[$k]]['标准差']=$stdev['0'];
			if (in_array('最高分',$allname) || in_array('最优生',$allname)) {
				$maxrs=$mysqli->query("select max(".$subname[$k].") from $table $tmp1");
				$max=$maxrs->fetch_array();
				$tableall[$o][$subname[$k]]['最高分'][$claname[$i]]=$max['0'];
				$sturs=$mysqli->query("select 姓名 from $table where ".$subname[$k]."=".$max['0'].$tmp2);
				$stunum=$sturs->num_rows;
				$stuname='';
				$m=1;
				while ($namedata=$sturs->fetch_array()) {
					$stuname.=$namedata['姓名'];
					if ($stunum>1 && $m<$stunum) $stuname.='/';
					$m=$m+1;
				}
				if ($max['0']) $tableall[$o][$subname[$k]]['最优生'][$claname[$i]]=$stuname;
				unset($sturs);
			}
			if (in_array('最低分',$allname) || in_array('学困生',$allname)) {
				$minrs=$mysqli->query("select min(".$subname[$k].") from $table where ".$subname[$k]."<>0".$tmp2);
				$min=$minrs->fetch_array();
				$tableall[$o][$subname[$k]]['最低分'][$claname[$i]]=$min['0'];
				$sturs=$mysqli->query("select 姓名 from $table where ".$subname[$k]."=".$min['0']." and ".$subname[$k]."<>0".$tmp2);
				$stunum=$sturs->num_rows;
				$stuname='';
				$m=1;
				while ($namedata=$sturs->fetch_array()) {
					$stuname.=$namedata['姓名'];
					if ($stunum>1 && $m<$stunum) $stuname.='/';
					$m=$m+1;
				}
				$tableall[$o][$subname[$k]]['学困生'][$claname[$i]]=$stuname;
			}
//任课教师
			$tableall[$o][$subname[$k]]['任课教师'][$claname[$i]]=$cladata[$subname[$k]];
			$tableall[$o][$claname[$i]]['ban']=$cladata['班主任'];
			if ($cladata['班主任']==$cladata[$subname[$k]]) $tempcla='1';
		}
		if (!$cladata['语文']) $table2[$i]=1;
		if (!$tempcla) {$tableall[$o]['总分']['任课教师'][$claname[$i]]=$cladata['班主任'];$table1[$i]=1;}
		unset($tempcla);
	}
	if (in_array('平均分',$allname)) $slyp[]='平均分';
	if (in_array('及格率',$allname)) $slyp[]='及格率';
	if (in_array('良好率',$allname)) $slyp[]='良好率';
	if (in_array('优秀率',$allname)) $slyp[]='优秀率';
	$slypnum=count($slyp);
	for ($k=0;$k<$subnum;$k++) {
		for ($q=0;$q<$slypnum;$q++) {
			$tmpsort=$tableall[$o][$subname[$k]][$slyp[$q]];
			array_splice($tmpsort,0,1);
			rsort($tmpsort);
			$tmpsort=array_flip(array_unique($tmpsort));
			for ($p=1;$p<$clanum;$p++) {
				$tableall[$o][$subname[$k]][substr($slyp[$q],0,3).'序'][$claname[$p]]=$tmpsort[$tableall[$o][$subname[$k]][$slyp[$q]][$claname[$p]]]+1;
			}
			unset($tmpsort);
		}
	}
	$tmpname=$sheetname;
	if ($stusort[$o]) $tmpname.='—'.$stusort[$o].'科';
	$objPHPExcel->getActiveSheet()->getRowDimension($s)->setRowHeight(30);
	$objPHPExcel->getActiveSheet()->getStyle('A'.$s.':'.$colstr[($subnum+1)].$s)->applyFromArray($styleTitle);
	$objPHPExcel->getActiveSheet()->setCellValue('A'.$s,$tmpname);
	$objPHPExcel->getActiveSheet()->mergeCells('A'.$s.':'.$colstr[($subnum+1)].$s);
	$s++;
	$objPHPExcel->getActiveSheet()->getStyle('A'.$s.':'.$colstr[($subnum+1)].$s)->applyFromArray($styleArray);
	$objPHPExcel->getActiveSheet()->setCellValue('A'.$s,'');
	$objPHPExcel->getActiveSheet()->setCellValue('B'.$s,'班级');
	for ($i=0;$i<$subnum;$i++) $objPHPExcel->getActiveSheet()->setCellValue($colstr[($i+2)].$s,$subname[$i].'('.$score[$subname[$i]].')');
	$allnum=count($allname);
	$clastart=0;
	if ($gradetop) $clastart=1;
	for ($i=$clastart;$i<$clanum;$i++) {
		for ($j=0;$j<$allnum;$j++) {
			if (($allname[$j]=='任课教师' && $claname[$i]=='校') || ($table2[$i]==1 && $allname[$j]=='任课教师') || ($gradetop && $allname[$j]=='最低分') || ($gradetop && $allname[$j]=='学困生')) {
			} else {
				$s++;
				$objPHPExcel->getActiveSheet()->getStyle('A'.$s.':'.$colstr[($subnum+1)].$s)->applyFromArray($styleArray);
				if ($allname[$j]=='任课教师') {
					$objPHPExcel->getActiveSheet()->setCellValue('A'.$s,$allname[$j]);
					$objPHPExcel->getActiveSheet()->setCellValue('B'.$s,'');
				} elseif (!$j) {
					$objPHPExcel->getActiveSheet()->setCellValue('A'.$s,$allname[$j]);
					$objPHPExcel->getActiveSheet()->setCellValue('B'.$s,$claname[$i]);
				} else {
					$objPHPExcel->getActiveSheet()->setCellValue('A'.$s,$allname[$j]);
					$objPHPExcel->getActiveSheet()->setCellValue('B'.$s,'');
				}
				for ($k=0;$k<$subnum;$k++) {
					if ($tableall[$o]['总分']['任课教师'][$claname[$i]] && $k==$subnum-1 && $allname=='任课教师') $objPHPExcel->getActiveSheet()->setCellValue($colstr[($k+2)].$s,$tableall[$o]['总分']['任课教师'][$claname[$i]]);
					elseif ($subname[$k]=="班级" && !$j) {
						$objPHPExcel->getActiveSheet()->setCellValue($colstr[($k+2)].$s,$tableall[$o][$subname[$k]][$allname[$j]][$claname[$i]]);
						$objPHPExcel->getActiveSheet()->mergeCells($colstr[($k+1)].$s.':'.$colstr[($subnum+1)].$s);
					} elseif ($allname[$j]=='平均分' && $i) $objPHPExcel->getActiveSheet()->setCellValue($colstr[($k+2)].$s,$tableall[$o][$subname[$k]][$allname[$j]][$claname[$i]].'/'.$tableall[$o][$subname[$k]]['平序'][$claname[$i]]);
					elseif (($allname[$j]=='及格率' || $allname[$j]=='良好率' || $allname[$j]=='优秀率') && $k==$subnum) $objPHPExcel->getActiveSheet()->setCellValue($colstr[($k+2)].$s,'');
					elseif ($allname[$j]=='及格率' && $i) $objPHPExcel->getActiveSheet()->setCellValue($colstr[($k+2)].$s,str_replace('.00','',$tableall[$o][$subname[$k]][$allname[$j]][$claname[$i]]).'%/'.$tableall[$o][$subname[$k]]['及序'][$claname[$i]]);
					elseif ($allname[$j]=='良好率' && $i) $objPHPExcel->getActiveSheet()->setCellValue($colstr[($k+2)].$s,str_replace('.00','',$tableall[$o][$subname[$k]][$allname[$j]][$claname[$i]]).'%/'.$tableall[$o][$subname[$k]]['良序'][$claname[$i]]);
					elseif ($allname[$j]=='优秀率' && $i) $objPHPExcel->getActiveSheet()->setCellValue($colstr[($k+2)].$s,str_replace('.00','',$tableall[$o][$subname[$k]][$allname[$j]][$claname[$i]]).'%/'.$tableall[$o][$subname[$k]]['优序'][$claname[$i]]);
					elseif (strpos($allname[$j],'率') && !$i) $objPHPExcel->getActiveSheet()->setCellValue($colstr[($k+2)].$s,str_replace('.00','',$tableall[$o][$subname[$k]][$allname[$j]][$claname[$i]]));
					elseif ($tableall[$o][$subname[$k]][$allname[$j]][$claname[$i]]==$tableall[$o][$claname[$i]]['ban']) $objPHPExcel->getActiveSheet()->setCellValue($colstr[($k+2)].$s,$tableall[$o][$subname[$k]][$allname[$j]][$claname[$i]]);
					elseif ($j==0 || $allname[$j]=='任课教师') $objPHPExcel->getActiveSheet()->setCellValue($colstr[($k+2)].$s,$tableall[$o][$subname[$k]][$allname[$j]][$claname[$i]]);
					else $objPHPExcel->getActiveSheet()->setCellValue($colstr[($k+2)].$s,$tableall[$o][$subname[$k]][$allname[$j]][$claname[$i]]);
				}
			}
		}
	}
	unset($claname);
	unset($subname);
	unset($table1);
	unset($table2);
	unset($tmp1);
	unset($tmp2);
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
