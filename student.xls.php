<?php
if (!$usersort) {Header("Location:".$web_root."/");exit();}
$stusort='';
$id=_get('id');
$classname=_get('classname');
if ($classname) {
	$g_name=substr($classname,0,5);
	$c_name=substr($classname,6);
	if ($c_name=='w' || $c_name=='l') {
		$stusort=py2hz($c_name);
		$c_name='';
	} elseif (!ctype_digit($c_name)) $c_name='';
} else {
	echo "<script>alert('dsfa');window.location='".$web_root."/?url=cj_index';</script>";
	exit();
}
$gradetop=_get('gradetop');
$line=_get('line');
$sort=_get('sort');
$subjectshow=_get('subjectshow');
$gradeshow=_get('gradeshow');
$name=_get('name');
//if ($stusort=='w' || $stusort=='l') $grasort=$stusort;
//elseif (ctype_digit($claname)) {$classname=$claname;$grasort='';}
//else $grasort='';
$result=$mysqli->query("select * from cj_data where id='$id'");
$data=$result->fetch_array();
$table=$data['数据'];
if (!$table) {
	echo "<script>window.location='".$web_root."/url=cj_index';</script>";
	exit();
}
$grade=substr($table,0,5);
if ($c_name && $data['文理']) {
	$clatmp=explode(";",$data['班级']);
	if (in_array($c_name,explode(",",$clatmp[0]))) $stusort=explode(";",$data['文理'])[0];
	elseif (in_array($c_name,explode(",",$clatmp[1]))) $stusort=explode(";",$data['文理'])[1];
	else {
		echo "<script>window.location='".$web_root."/?url=cj_index';</script>";
		exit();
	}
} elseif ($c_name && !in_array($c_name,explode(",",$data['班级']))) {
	echo "<script>window.location='".$web_root."/?url=cj_index';</script>";
	exit();
}
$wlsort=array('理'=>'0','文'=>'1');
if ($subjectshow=='1') {
	if ($stusort=='文') $subname=explode(",",explode(";",$data['科目'])[$wlsort['文']]);
	elseif ($stusort=='理') $subname=explode(",",explode(";",$data['科目'])[$wlsort['理']]);
	else $subname=explode(",",$data['科目']);
} else $subname=$cj_subject;
$subnum=count($subname);
$tmpsub[]='年名';
$tmpsub[]='班名';
for ($i=0;$i<$subnum;$i++) {
	$tmpsub[]=$subname[$i];
	if ($sort==1 || $sort==3) $tmpsub[]=substr($subname[$i],0,3)."排";
	if ($sort==2 || $sort==3) $tmpsub[]=substr($subname[$i],0,3)."序";
}
$tmpsub[]='总分';
$subname=$tmpsub;
unset($tmpsub);
$subnum=count($subname);
$classshow=$data['年级'];
if ($c_name) $classshow.=$c_name.'班';
if ($gradetop && $stusort) $classshow.=$stusort.'科';
if ($name!="all") $sheetname=$classshow.$name.'同学';
elseif ($name=="all") $sheetname=str_replace('all','',$classshow);
if ($gradetop!="") $sheetname=$classshow.'前'.$gradetop.'名';
$sheetname.='历次成绩（20'.substr($table,-4,2).'年'.str_replace('0','',substr($table,-2,1)).substr($table,-1).'月）';
$nameexe="select 姓名,班级 from $table where 班级='$c_name' order by 总分 desc";
if ($gradetop && $stusort) $nameexe="select 年名,姓名,班级,类别 from $table where 年名<>'' and 类别='$stusort' order by 总分 desc limit $gradetop";
elseif ($gradetop) $nameexe="select 年名,姓名,班级 from $table where 年名<>'' order by 总分 desc limit $gradetop";
elseif ($stusort && $name=='all') $nameexe="select 年名,姓名,班级,类别 from $table where 类别='$stusort' and 班级='$c_name' order by 总分 desc";
elseif ($name!="all") $nameexe="select 姓名,班级 from $table where 班级='$c_name' and 姓名='".$name."'";
$namers=$mysqli->query($nameexe);
while ($namedata=$namers->fetch_array()) {if ($namedata) $stuname[]=$namedata;}
$stunum=count($stuname);
if ($gradeshow=='c3') {$gratmp="and 年级='初三'";$gratmp1='初三';}
elseif ($gradeshow=='g3') {$gratmp="and 年级='高三'";$gratmp1='高三';}
elseif ($gradeshow=='g2') {$gratmp="and 年级='高二'";$gratmp1='高二';}
elseif ($gradeshow=='c2') {$gratmp="and 年级='初二'";$gratmp1='初二';}
else {$gratmp='';$gratmp1='';}
$tablers=$mysqli->query("select 年级,考试,数据 from cj_data where 数据 like '%$grade%' $gratmp order by 数据");
while ($datadata=$tablers->fetch_array()) if ($datadata) $tabdata[]=$datadata;
$tabnum=count($tabdata);
$testname=num2text(substr($table,0,5)).'20'.substr($table,-4,2).'年'.str_replace('0','',substr($table,-2,1)).substr($table,-1).'月'.$data['年级'].$data['考试'].'考试';
$colstr=array('0'=>'A','1'=>'B','2'=>'C','3'=>'D','4'=>'E','5'=>'F','6'=>'G','7'=>'H','8'=>'I','9'=>'J','10'=>'K','11'=>'L','12'=>'M','13'=>'N','14'=>'O','15'=>'P','16'=>'Q','17'=>'R','18'=>'S','19'=>'T','20'=>'U','21'=>'V','22'=>'W','23'=>'X','24'=>'Y','25'=>'Z');
$objPHPExcel = new PHPExcel();
$objPHPExcel->getProperties()->setCreator("张春靖")
	 ->setTitle("学生历次成绩")
	 ->setSubject("学生历次成绩")
	 ->setDescription($gradeshow."学生历次成绩");
$objPHPExcel->setActiveSheetIndex(0);
$objPHPExcel->getActiveSheet()->setTitle($sheetname);
$objPHPExcel->getActiveSheet()->getDefaultRowDimension()->setRowHeight(20);
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
$s=1;
for($k=0;$k<($subnum+1);$k++) {
	$objPHPExcel->getActiveSheet()->getColumnDimension($colstr[$k])->setAutoSize(true); 
}
for($i=0;$i<$stunum;$i++) {
	$stutmp='';
	$objPHPExcel->getActiveSheet()->getStyle('A'.$s)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
	if ($gradetop!="") $stutmp=$data['年级'].'年级第'.$stuname[$i]['年名'].'名'.$stuname[$i]['班级'].'班';
	$webrs=$mysqli->query("select * from phpcj_student where realname='".$stuname[$i]["姓名"]."'");
	$webdata=$webrs->fetch_array();
	if ($webdata['usercheck']!=1) $stutmp.=$stuname[$i]["姓名"].'同学'.$gratmp1.'历次成绩（网络查询地址：test.phpcj.net 用户名：'.$webdata['username'].' 密码：'.$webdata['password'].'）：';
	else $stutmp.=$stuname[$i]["姓名"].'同学'.$gratmp1.'历次成绩（网络查询地址：test.phpcj.net 用户名：'.$webdata['username'].' 密码：已修改）：';
	$objPHPExcel->getActiveSheet()->setCellValue('A'.$s,$stutmp);
	$objPHPExcel->getActiveSheet()->mergeCells('A'.$s.':'.$colstr[$subnum].$s);
	$s++;
	$objPHPExcel->getActiveSheet()->getStyle('A'.$s.':'.$colstr[$subnum].$s)->applyFromArray($styleArray);
	$objPHPExcel->getActiveSheet()->setCellValue('A'.$s,'考试');
	$objPHPExcel->getActiveSheet()->setCellValue('B'.$s,'年级');
	$objPHPExcel->getActiveSheet()->setCellValue('C'.$s,'班级');
	for($k=2;$k<$subnum;$k++) $objPHPExcel->getActiveSheet()->setCellValue($colstr[($k+1)].$s,$subname[$k]);
	$s++;
	for($j=0;$j<$tabnum;$j++) {
		if ($gradetop && $stusort) $stuexe="select * from {$tabdata[$j]['数据']} where 姓名='{$stuname[$i]['姓名']}'";
//		if ($gradetop && $stusort) $stuexe="select * from {$tabdata[$j]['数据']} where 姓名='{$stuname[$i]['姓名']}' and 类别='$stusort'";
		elseif ($stusort) $stuexe="select * from {$tabdata[$j]['数据']} where 姓名='{$stuname[$i]['姓名']}' and 班级='$c_name'";
//		elseif ($stusort) $stuexe="select * from {$tabdata[$j]['数据']} where 姓名='{$stuname[$i]['姓名']}' and 类别='$stusort' and 班级='$c_name'";
		elseif ($gradetop) $stuexe="select * from {$tabdata[$j]['数据']} where 姓名='{$stuname[$i]['姓名']}'";
		else $stuexe="select * from {$tabdata[$j]['数据']} where 姓名='{$stuname[$i]['姓名']}' and 班级='$c_name'";
		$datars=$mysqli->query($stuexe);
		$alldata=$datars->fetch_array();
		if ($alldata) {
			$objPHPExcel->getActiveSheet()->getStyle('A'.$s.':'.$colstr[$subnum].$s)->applyFromArray($styleArray);
			$objPHPExcel->getActiveSheet()->setCellValue('A'.$s,substr($tabdata[$j]['数据'],-4).$tabdata[$j]['年级'].$tabdata[$j]['考试']);
			for($k=0;$k<$subnum;$k++) $objPHPExcel->getActiveSheet()->setCellValue($colstr[$k+1].$s,$alldata[$subname[$k]]);
			$s++;
		}
	}
	$s++;
	$objPHPExcel->getActiveSheet()->setCellValue('A'.$s,'');
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
