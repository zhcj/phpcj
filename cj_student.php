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
$url=$web_root.'/?url=student.xls&id='.$id.'&classname='.$classname.'&gradetop='.$gradetop.'&sort='.$sort.'&subjectshow='.$subjectshow.'&gradeshow='.$gradeshow.'&name='.$name;
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
echo '
  <div class="content-wrapper">
    <section class="content-header">
      <h1>'.$testname.'</h1>
    </section>
    <section class="content">
      <div class="row">
        <div class="col-lg-12">
          <div class="nav-tabs-custom">
            <ul class="nav nav-tabs">
              <li><a href="'.$web_root.'/?url=cj_index&classname='.$classname.'">历次</a></li>
              <li class="active"><a href="'.$web_root.'/?url=cj_index&classname='.$classname.'">详情<i class="fa fa-times"></i></a></li>
              <li><a href="'.$web_root.'/?url=cj_sheets&id='.$id.'&classname='.$classname.'">统计</a></li>
            </ul>
            <div class="tab-content">
              <div class="tab-pane fade in active">
                <div class="table-responsive">
                  <div style="text-align:right;padding:5px;"><a class="btn btn-primary" href="'.$url.'">下载 <img src="images/xls.gif" height="18px"></a></div>
                  <div class="col-lg-12">
                    <h4 align="center">'.$sheetname.'</h4>';
for($i=0;$i<$stunum;$i++) {
	$nianji='';
	$kaoshi='';
	$nianming='';
	$banming='';
	$kemuming='';
	echo '
                    <div style="padding:0px 0px 10px">'.$classshow;
	if ($gradetop!="") echo  '年级第 <b>'.$stuname[$i]['年名'].'</b> 名 '.$stuname[$i]['班级'].'班';
	echo ' <b>'.$stuname[$i]['姓名'].'</b> 同学'.$gratmp1.'历次成绩：</div>
                    <table class="table table-bordered table-condensed table-hover">
                      <tr align="center"><td>考试</td><td>年级</td><td>班级</td>';
	for($k=2;$k<$subnum;$k++) echo '<td>'.$subname[$k].'</td>';
	echo '
                      </tr>';
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
			echo '
                      <tr align="center"><td>'.substr($tabdata[$j]['数据'],-4).$tabdata[$j]['年级'].$tabdata[$j]['考试'].'</td>';
//			for($k=0;$k<$subnum;$k++) echo '<td>'.$alldata[$subname[$k]].'</td>';
			for($k=0;$k<$subnum;$k++) {
//			echo '<td>'.$alldata[$subname[$k]].'</td>';
				if (in_array($subname[$k],$cj_subject) && $alldata[substr($subname[$k],0,3)."排"]>$alldata['年名'] ) echo '<td><font color="blue"><b>'.$alldata[$subname[$k]].'</b></font></td>';
				elseif (in_array($subname[$k],$cj_subject) && $alldata[substr($subname[$k],0,3)."排"]<$alldata['年名'] ) echo '<td><font color="red"><u>'.$alldata[$subname[$k]].'</u></font></td>';
				elseif (in_array($subname[$k],$cj_subject) || $subname[$k]=='总分' || $subname[$k]=='年名') echo '<td>'.$alldata[$subname[$k]].'</td>';
				else echo '<td>'.$alldata[$subname[$k]].'</td>';
			}
			echo '</tr>';
			$nianming[]=$alldata['年名'];
			$banming[]=$alldata['班名'];
			$nianji[]=hz2py($tabdata[$j]['年级']);
			$kaoshi[]=substr($tabdata[$j]['数据'],-4);
		}
	}
	$nianming=implode(';',$nianming);
	$nianji=implode(';',$nianji);
	$kaoshi=implode(';',$kaoshi);
	echo '
                    </table>';
	if (strstr($nianming,";")) {
		if ($line=="2" && $_GET['name']=="all") {
			echo '
                    <div align="left"><a onmouseout="hiddenPic('.$i.');" onmousemove="showPic('.$i.');"><font color="blue">'.$stuname[$i]['姓名'].'同学年级名次变化曲线图</font></a></div><span style="position:relative;z-index:'.($stunum-$i).'"><div id="'.$i.'" style="position:absolute;visibility:hidden;solid black;border-top:1px solid black;border-left:1px solid black;border-bottom:4px solid gray;border-right:4px solid gray;color:black;"><img alt="年级名次变化曲线图" src="cj_line.php?mingci='.$nianming.'&kaoshi='.$kaoshi.'&nianji='.$nianji.'" border="0"></div></span><br>
';
		} elseif (($line=="3" && $_GET['name']=="all") or $_GET['name']!="all") {
			echo '
                    <div><b>'.$stuname[$i]['姓名'].'</b>同学年级名次变化曲线图：
                      <p align="center"><img alt="年级名次变化曲线图" src="cj_line.php?mingci='.$nianming.'&kaoshi='.$kaoshi.'&nianji='.$nianji.'" style="max-width:490px;width:100%;max-height:285px;height:100%"></p>
                    </div>';
		}
//	} elseif ($line!='1') echo '<font color="red"><b>'.$stuname[$i]['姓名'].'</b>同学只有一次考试成绩，无法显示年级名次变化曲线图！</font>';
	}
}
if ($line=="2" && $_GET['name']=="all") echo '<div style="height:400px"> </div>';
echo '
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
  </div>
<script language="javascript" type="text/javascript">
function showPic(pic){
	document.getElementById(pic).style.visibility="";
	document.getElementById(pic).style.top="-130";
	document.getElementById(pic).style.left="50";
}
function hiddenPic(pic){
	document.getElementById(pic).style.visibility="hidden";
}
</script>
';
$pagename=$sheetname;
