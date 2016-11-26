<?php
session_start();
error_reporting(0);
if(!$_SESSION['sjzusersort']) {Header("Location:./");exit();}
elseif($_SESSION['sjzusercheck']) {Header("Location:./");exit();}
$url='index';
include("../conn.php");
$action=_get('action');
$id=_get('id');
$sheet=_get('sheet');
$grade=_post('grade');
$class=_post('class');
$classname=_post('classname');
$getdata=_post('getdata');
$data=_post('data');
$alert=_get('alert');
$teacher=_get('teacher');
if ($alert=='1') $alert='修改成功！';
elseif ($alert=='2') $alert='学年、学期均不能为空！';
elseif ($alert=='3') $alert='任课信息添加成功！';
elseif ($alert=='4') $alert='任课信息修改成功！';
elseif ($alert=='5') $alert='上传文件名为空！';
elseif ($alert=='6') $alert='请选择xls格式文件上传！';
elseif ($alert=='7') $alert='文件不能大于8M！';
elseif ($alert=='8') $alert='文件上传出错！可能是目录"'.upfile().'"不可读写！';
elseif ($alert=='81') $alert='文件中无工作表信息，请检查文件！';
elseif ($alert=='9') $alert='科目';
elseif ($alert=='10') $alert='班级';
elseif ($alert=='11') $alert='姓名';
elseif ($alert=='12') $alert='数据上传成功！';
elseif ($alert=='13') $alert='数据处理成功！';
if ($teacher=='1') $teacher='（没有任课教师信息！请添加！）';
if ($teacher=='2') $teacher='（已有任课教师信息，未更新！）';
if ($action=="configedit") {
	$schoolname=_post('schoolname');
	$schoolweb=_post('schoolweb');
	$cjlogin=_post('cjlogin');
	$cjyear=_post('cjyear');
	$cjyear=substr($cjyear,0,4);
	$cjterm=_post('cjterm');
	$cjterm=str_replace("１","1",$cjterm);
	$cjterm=str_replace("２","2",$cjterm);
	$cjterm=str_replace("二","2",$cjterm);
	$cjterm=str_replace("一","1",$cjterm);
	$cjabout=_post('cjabout');
	$cjsub=postcheck(_post('cjsubject'));
	$subject=explode(',',$cjsub);
	$subnum=count($subject);
	$subtemp=$cj_subject;
	$cjtestname=postcheck(_post('cjtestname'));
	$cjitem=postcheck(_post('cjitem'));
	$cjlevel=postcheck(_post('cjlevel'));
	if ($cjyear && $cjterm) {
		mysql_query("update cj_config set cj_schoolname='$schoolname',cj_schoolweb='$schoolweb',cj_year='$cjyear',cj_term='$cjterm',cj_subject='$cjsub',cj_testname='$cjtestname',cj_item='$cjitem',cj_level='$cjlevel',cj_about='$cjabout'");
		for($i=0;$i<$subnum;$i++) {
			if (in_array($subject[$i],$cj_subject)) {
				$subtemp=str_replace($subject[$i],'',$subtemp);
			} else {
					mysql_query ("alter table cj_data add ".$subject[$i]." smallint(4) not null");
					mysql_query ("alter table cj_teacher add ".$subject[$i]." varchar(10) character set utf8 collate utf8_general_ci not null");
			}
		}
		$subtemp=str_replace(';;','',$subtemp);
		$subtmp=explode(";",$subtemp);
		$subnum=count($subtmp);
		for($i=0;$i<$subnum;$i++) {
			mysql_query ("alter table cj_data drop ".$subtmp[$i]);
			mysql_query ("alter table cj_teacher drop ".$subtmp[$i]);
		}
		echo "<script>window.location='admin.php?action=config&alert=1';</script>";
	}
	else 	echo "<script>window.location='admin.php?action=config&alert=2';</script>";
	exit();
} elseif ($action=="saveteacher") {
	if (_post('delteacher')=='删除') {
		mysql_query("delete from cj_teacher where id=$id");
		echo "<script>window.location='admin.php?action=editdata';</script>";
		exit();
	}
	$getdata=_post('getdata');
	$grade=_post('grade');
	if (!$grade) $grade=$getdata;
	$class=_post('classname');
	$class=str_replace('０','0',$class);
	$class=str_replace('１','1',$class);
	$class=str_replace('２','2',$class);
	$class=str_replace('３','3',$class);
	$class=str_replace('４','4',$class);
	$class=str_replace('５','5',$class);
	$class=str_replace('６','6',$class);
	$class=str_replace('７','7',$class);
	$class=str_replace('８','8',$class);
	$class=str_replace('９','9',$class);
	if ((!$class || !$grade) && !$id && !$getdata) {echo "<script>history.go(-1);</script>";exit();}
	$subject=$cj_subject;
	$num=count($subject);
	for($i=0;$i<$num;$i++){
		$adddata1.=','.$subject[$i];
		$adddata2.=",'"._post($i)."'";
		if (!$i) $adddata3.=$subject[$i]."='"._post($i)."'";
		else $adddata3.=",".$subject[$i]."='"._post($i)."'";
		$tmp.=_post($i);
	}
	$bzr=_post('bzr');
	$tmp.=_post('bzr');
	if (!$tmp) {echo "<script>alert('科目及班主任信息不能全部为空！');history.go(-1);</script>";exit();}
	if (!$id) {
		$datacount=mysql_num_rows(mysql_query("select * from cj_teacher where 数据='$grade' and 班级=$class"));
		if (!$datacount) {
			if (!$getdata) {
				mysql_query("insert into cj_teacher (数据,班级$adddata1,班主任,现任) values ('$grade',".$class.$adddata2.",'$bzr',1)");
				echo "<script>window.location='admin.php?action=teacherlist&alert=3';</script>";
			} else {
				mysql_query("insert into cj_teacher (数据,班级$adddata1,班主任) values ('$grade',".$class.$adddata2.",'$bzr')");
				echo "<script>window.location='admin.php?action=editdata&alert=3';</script>";
			}
		} else echo "<script>alert('已有此教师信息！');history.go(-1);</script>";
	} else {
		mysql_query("update cj_teacher set $adddata3,班主任='$bzr' where id=$id");
		echo "<script>window.location='admin.php?action=moditeacher&id=$id&alert=4';</script>";
	}
	exit();
} elseif ($action=="saveinfo") {
	$grade=_post('grade');
	$time=_post('time');
	$class=_post('class');
	$cjdata=substr($grade,0,5).'_'.substr($time,0,4).substr($grade,5,1);
	if (!$grade || !$class || !$time) {echo "<script>alert('请详细填写成绩相关信息！');history.back();</script>";exit();}
	$subjectall=$cj_subject;
	$numall=count($subjectall);	
	for ($i=0; $i<$numall;$i++) {
		$posti=_post($i);
		if (!$posti) $posti=0;
		$insert1.=",".$subjectall[$i];
		$insert2.=",".$posti;
		$update1.=",".$subjectall[$i]."=".$posti;
	}
	$now=_post('now');
	if (!$now) $now=0;
	if ($now==1) mysql_query("update cj_data set 现在=0 where (年级='".$grade."' or 年级='".substr($grade,0,5)."') and 现在=1");
	mysql_query("update cj_data set 年级='$grade',班级='$class',时间='$time'".$update1.",数据='$cjdata',现在=$now where id=$id");
	echo "<script>alert('成绩信息修改成功！');window.location='admin.php?action=editdata';</script>";
	exit();
} elseif ($action=="delinfo") {
	$deldata=mysql_fetch_array(mysql_query("select * from cj_data where id=$id"));
	if ($deldata['现在']==1) mysql_query("update cj_data set 现在=1 where 数据='".$deldata['上次']."'");
	mysql_query("drop table ".$deldata['数据']);
	mysql_query("delete from cj_data where id=".$id);
	mysql_query("delete from cj_teacher where 数据='".$deldata['数据']."'");
	echo "<script>history.back();</script>";
	exit();
} elseif ($action=="delteacher") {
	mysql_query("delete from cj_teacher where id=$id");
	echo "<script>history.back();</script>";
	exit();
} elseif ($action=="listtemp") {
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta http-equiv="pragma" content="no-cache">
<link rel="shortcut icon" href="favicon.ico">
<link rel="bookmark" href="favicon.ico">
<link rel="stylesheet" type="text/css" href="style.css">
<title>上传成绩数据表浏览</title>
</head>
<p align=center><a href="#" onclick="window.close();return false;">关闭本窗口</a></p>
<body class="body">
<?php
	$tempexe="select * from temptable order by 总分 desc";
	if ($sheet) $tempexe="select * from temptable order by 类别,总分 desc";
	$temprs=mysql_query($tempexe);
	if(!$temprs){echo '<p align=center>无上传成绩数据表或数据表已失效!</p></body></html>';exit();}
	$j=mysql_num_fields($temprs);
	echo '<table width="80%" border="1" align="center" cellpadding="1" cellspacing="1" style="border-collapse:collapse" bordercolor="#000000">
<tr align="center"><td>序号</td>';
	for($i=0;$i<$j;$i++){
		$meta=mysql_fetch_field($temprs);
		echo '<td>'.$meta->name.'</td>';
	}
	echo '</tr>';
	$k=1;
	while($tempdata=mysql_fetch_array($temprs)){
		echo '<tr align="center"><td>'.$k.'</td>';
		for($i=0;$i<$j;$i++) 	echo '<td>'.$tempdata[$i].'</td>';
		echo '</tr>';
		$k=$k+1;
	}
?></table>
<p align=center><a href="#" onclick="window.close();return false;">关闭本窗口</a></p>
</body>
</html>
<?php
	exit();
} elseif ($action=="delnews") {
	mysql_query("delete from cj_news where id=$id");
	echo "<script>window.location='news.php';</script>";
	exit();
}
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta http-equiv="pragma" content="no-cache">
<link rel="shortcut icon" href="/favicon.ico">
<link rel="bookmark" href="/favicon.ico">
<link rel="stylesheet" type="text/css" href="<?php echo $sjzstyle;?>.css">
<title>中学成绩查询及分析系统后台管理</title>
</head>
<body class="body">
<?php include("../head.php");?>
<table cellSpacing="0" cellPadding="0" align="center" class="bodydiv">
  <tr valign="top">
    <td class="leftwidth">
      <div class="framehead"><b>程序管理</b></div>
      <table class="framebody" width="100%" border="0" cellspacing="3" cellpadding="5">
        <tr><td align="center"><a href="?action=config">程序基本设置</a></td></tr>
        <tr><td align="center"><a href="?action=upfile">添加成绩数据</a></td></tr>
        <tr><td align="center"><a href="?action=editdata">修改成绩数据</a></td></tr>
        <tr><td align="center"><a href="?action=moditeacher">添加现任教师信息</a></td></tr>
        <tr><td align="center"><a href="?action=teacherlist">修改现任教师信息</a></td></tr>
        <tr><td align="center">程序版本：20121212</td></tr>
<!--         <tr><td align="center"><a href="?action=addnotice">添加系统公告</a></td></tr>
        <tr><td align="center"><a href="?action=editnotice">修改系统公告</a></td></tr> -->
      </table>
      <div class="framefoot"></div><br>
    </td>
    <td width="2"></td>
    <td valign="top">
<?php
if ($action=="config" || !$action) {
	$configexe="select * from cj_config";
	$configrs=mysql_query($configexe);
	$cjconfig=mysql_fetch_array($configrs);
	$schoolname=$cjconfig['cj_schoolname'];
	$schoolweb=$cjconfig['cj_schoolweb'];
	$cjyear=$cjconfig['cj_year'];
	$cjyear0=$cjyear+1;
	$cjterm=$cjconfig['cj_term'];
	$cjsubject=$cjconfig['cj_subject'];
	$cjtestname=$cjconfig['cj_testname'];
	$cjitem=$cjconfig['cj_item'];
	$cjitemmore=$cjconfig['cj_itemmore'];
	$cjitem=$cjconfig['cj_item'];
	$cjitemmore=explode(',',$cjitemmore);
	$cjlevel=$cjconfig['cj_level'];
	$cjabout=$cjconfig['cj_about'];
?>
		<div class="framehead"><b>程序基本设置</b><font color="red"> <?php echo $alert;?></font></div>
		<div class="framebody">
		<table width="90%" border="0" cellpadding="2" cellspacing="6" align="center">
<form method="post" action="?action=configedit" name="config">
			<tr height="30"><td align="right" width="20%">学校名称：</td><td width="80%"><input name="schoolname" type=text value="<?php echo $schoolname;?>"></td></tr>
			<tr height="30"><td align="right">学校网站：</td><td>http://<input name="schoolweb" type="text" value="<?php echo $schoolweb;?>"></td></tr>
			<tr height="30"><td align="right">时间设置：</td><td><input type=text name="cjyear" value="<?php echo $cjyear;?>" size="4"> - <?php echo $cjyear0;?>学年 第<input type=text name="cjterm" value="<?php echo $cjterm;?>" size="1">学期</td></tr>
 			<tr><td align="right">科目设置：</td><td><input type=text name="cjsubject" value="<?php echo $cjsubject;?>" size="50"></td></tr>
 			<tr><td align="right"></td><td>*请慎重修改此项，可能会造成部分数据永久丢失</td></tr>
 			<tr><td align="right">考试名称：</td><td><input type=text name="cjtestname" value="<?php echo $cjtestname;?>" size="70"></td></tr>
 			<tr><td align="right">统计项目：</td><td><input type="text" readonly value="<?php echo implode(',',$cjitemmore);?>" size="63"> 详表</td></tr>
 			<tr><td align="right"></td><td><input type="text" name="cjitem" value="<?php echo $cjitem;?>" size="50"> 简表</td></tr>
 			<tr height="30"><td align="right">三率分值：</td><td><input type="text" name="cjlevel" value="<?php echo $cjlevel;?>" size="20"> 及格、良好、优秀占总分比重，0表示不统计此项</td></tr>
			<tr height="30"><td align="right">相关说明：</td><td><input type="text" name="cjabout" value="<?php echo $cjabout;?>" size="50"></td></tr>
			<tr height="30"><td align="center" colspan="2">* 注：以上多项的设置均使用逗号隔开，半角全角均可</td></tr>
			<tr height="30"><td align="center" colspan="2"><input type="submit" value="提交" onclick="return check()"></td></tr>
</form>
		</table></div>
<script language="JavaScript">
function check(){
	if(document.config.schoolname.value==""){
		alert("学校名称不能为空!");
		document.config.schoolname.focus();
		return false;}
	if(document.config.cjyear.value==""){
		alert("学年不能为空!");
		document.config.cjyeare.focus();
		return false;}
	if(document.config.cjsubject.value==""){
		alert("科目设置不能为空!");
		document.config.cjsubject.focus();
		return false;}
	if(document.config.cjabout.value==""){
		alert("相关说明不能为空!");
		document.config.cjabout.focus();
		return false;}
	return true;
	}
</script>
<?php
} elseif ($action=="moditeacher") {
	$getdata=_get('getdata');
	$classname=_get('classname');
	$subject=$cj_subject;
	$num=count($subject);	
	if ($id) {
		$rs=mysql_query("select * from cj_teacher where id=$id");
		$data=mysql_fetch_array($rs);
		$classname=$data['班级'];
		$getdata=$data['数据'];
	}
	$grade=substr($getdata,0,5);
	$kaoshi='20'.substr($getdata,6,2).'年'.str_replace('0','',substr($getdata,8,1)).substr($getdata,9,1).'月';
	$datashow=num2text($grade).$classname.'班';
	if (!$id && !$getdata) echo '<script language=JavaScript>
function check(){
	if(document.teacher.grade.value==""){
		alert("请选择年级!");
		document.teacher.grade.focus();
		return false;}
	if(document.teacher.classname.value==""){
		alert("请输入班级!");
		document.teacher.classname.focus();
		return false;}
	return true;
	}
</script>
       <div class="framehead" align="center"><b>添加现任教师信息</b></div>';
	elseif (_get('getdata')) echo '      <div class="framehead" align="center"><b>添加“'.$datashow.$kaoshi.'”考试的任课教师信息</b> <font color="red">'.$alert.'</font></div>';
	elseif ($kaoshi=='20年月') echo '      <div class="framehead" align="center"><b>修改“'.$datashow.'”现任教师信息</b> <font color="red">'.$alert.'</font></div>';
	else echo '      <div class="framehead" align="center"><b>修改“'.$datashow.$kaoshi.'”考试的任课教师信息</b> <font color="red">'.$alert.'</font></div>';
	echo '      <div class="framebody" align="center"><table width="60%" border="0" cellpadding="1" cellspacing="1"><form method="post" action="?action=saveteacher&id='.$id.'&classname='.$classname.'&getdata='.$getdata.'" name="teacher">';
	if (!$id && !$getdata) {
		echo '<tr height="25"><td align="right" width="20%">年级：</td><td width="80%"><select name="grade" id="grade"><option value="">请选择年级</option>';
		$gradenum=$cj_grade1.';'.$cj_grade2.';'.$cj_grade3.';';
		$gradenum=str_replace('20', 'g20',$gradenum);
		$gradenum.=$cj_grade1.';'.$cj_grade2.';'.$cj_grade3;
		$gradenum=str_replace(';20', ';c20',$gradenum);
		$gradetmp=explode(';',$gradenum);
		$gradenum=count($gradetmp);	
		for ($i=0; $i<$gradenum;$i++) echo '<option value="'.$gradetmp[$i].'">'.num2text($gradetmp[$i]).'</option>';
		echo '</select></td></tr><tr height="25"><td align="right">班级：</td><td><input type="classname" name="classname" value=""></td></tr>';
	}
	for ($i=0;$i<$num;$i++){
		echo '<tr height="25"><td align="right" width="20%">'.$subject[$i].'：</td><td><input type="text" name="'.$i.'" value="'.$data[$subject[$i]].'"></td></tr>';
	}
	echo '<tr height="25"><td align="right">班主任：</td><td><input type="text" name="bzr" value="'.$data['班主任'].'"></td></tr><tr height="25"><td colspan="2" align="center"><input type="submit" value="提交" onclick="return check()">';
	if ($id && $kaoshi!='20年月') echo ' <input type="submit" value="删除" onclick="return confirm(\'确实删除本班任课教师信息吗？\n注意：一旦删除后将不可恢复！\')" name="delteacher">';
	if ($classname) echo ' <input type="hidden" value="'.$classname.'" name="classname">';
	echo '</td></tr></form></table></div>';
} elseif ($action=="teacherlist") {
	$datacount=mysql_num_rows(mysql_query("select * from cj_teacher where 现任=1"));
	if (!$datacount) {
		echo '<br><br><div align="center">无现任教师信息！请<a href="?action=moditeacher">添加</a>！</div></td></tr></table>';
		include("foot.php");
		echo '</body></html>';
		exit();
	}
	echo '      <div class="framehead" align="center"><b>修改现任教师信息</b><font color="red">'.$alert.'</font></div>';
	if (!$datacount) {echo '</td></tr></table>';exit();}
	echo '<table width="100%" border="1" align="center" cellpadding="2" cellspacing="0" style="border-collapse:collapse" bordercolor="#458B74">
		  <tr align="center" height="25"><th width="12%" class="astyle">年级</th><th class="astyle">班级</th>';
	$subject=$cj_subject;
	$num=count($subject);	
	for ($i=0;$i<$num;$i++) echo '<th class="astyle">'.$subject[$i].'</th>';
	echo '<th class="astyle">班主任</th><th width="10%" class="astyle">操作</th></tr>';
	$teacherexe="select * from cj_teacher where (数据 like '%$cj_grade1%' or 数据 like '%$cj_grade2%' or 数据 like '%$cj_grade3%') and 现任=1 order by 数据,班级+0";
	$teacherrs=mysql_query($teacherexe);
	while($teacher=mysql_fetch_array($teacherrs)){
		$grade=$teacher['数据'].'级';
		$grade=str_replace("c","初中",$grade);
		$grade=str_replace("g","高中",$grade);
		echo '<tr height="25" align="center"><td class="astyle">'.$grade.'</td><td class="astyle">'.$teacher['班级'].'</td>';
		for ($i=0;$i<$num;$i++) echo '<td class="astyle">'.$teacher[$subject[$i]].'</td>';
		echo '<td class="astyle">'.$teacher['班主任'].'</td><td class="astyle"><a href="?action=moditeacher&id='.$teacher['id'].'">修改</a> <a href="?action=delteacher&id='.$teacher['id'].'" onclick="return confirm(\'确实删除本班任课教师信息吗？\n注意：一旦删除后将不可恢复！\')">删除</a></td></tr>';
	}
echo '</table>';
} elseif ($action=="editdata") {
	echo '      <div class="framehead" align="center"><b>修改已有成绩数据</b><font color="red">'.$alert.'</font></div>
		<table width="100%" border="1" align="center" cellpadding="2" cellspacing="0" style="border-collapse:collapse" bordercolor="#458B74"><tr align="center"><th width="30%" class="astyle">考试名称</th><th width="35%" class="astyle">成绩信息及数据操作</th><th class="astyle">添加或修改任课教师信息</th></tr>';
	$rs=mysql_query("select * from cj_data where 数据 like '%$cj_grade1%' or 数据 like '%$cj_grade2%' or 数据 like '%$cj_grade3%' order by 数据 desc");
	while($data=mysql_fetch_array($rs)){
		$gradetmp='';
		$table=$data['数据'];
		$id=$data['id'];
		$gradeshow=num2text(substr($table,0,5)).'('.substr($table,-4).$data['年级'].$data['考试'].')';
		if ($data['现在']==1) 	$gradetmp='<img src="/images/new.gif" width="24" height="11" border="0">';
		$classname=check($table,'班级');
		sort($classname);
		$cnum=count($classname);
		$teacher='';
		for ($i=0;$i<$cnum;$i++){
			$teacherexe="select * from cj_teacher where 数据='$table' and 班级=".$classname[$i];
			$teacherrs=mysql_query($teacherexe);
			$teacherdata=mysql_fetch_array($teacherrs);
			if ($teacherdata['id']) $teacher.='<a href="?action=moditeacher&id='.$teacherdata['id'].'" title="修改'.$classname[$i].'班任课教师信息">'.$classname[$i].'</a>';
			else $teacher.='<a href="?action=moditeacher&classname='.$classname[$i].'&getdata='.$table.'" title="添加'.$classname[$i].'班任课教师信息"><b>'.$classname[$i].'</b></a>';
			if ($i<$cnum) $teacher=$teacher.' ';
		}
		if ($tmpdata!=substr($table,0,5) && $tmpdata) echo '<tr><td colspan="3"></td></tr>';
		$tmpdata=substr($table,0,5);
		echo '<tr><td class="astyle"><a href="index.php?action=table&id='.$id.'" target="_blank">'.$gradeshow.$gradetmp.'</a>';
		echo '</td><td class="astyle"><a href="?action=upfile&id='.$id.'">重新上传</a> <a href="?action=modiinfo&id='.$id.'&modi=info">修改信息</a> <a href="?action=delinfo&id='.$id.'" onclick="return confirm(\'确实删除“'.$gradeshow.'”的信息及数据表吗？\n注意：一旦删除后将不可恢复！\')" title="彻底删除成绩信息和成绩数据">删除</a></td><td class="astyle">'.$teacher.'</td></tr>';
	}
	echo '</table>';
} elseif ($action=="upfile") {
	unlink(upfile());
	mysql_query ("drop table temptable");
	if ($id) {
		$cjrs=mysql_query("select * from cj_data where id=$id");
		$cjdata=mysql_fetch_array($cjrs);
		if (!$cjdata) {echo "<script>alert('无此成绩相关信息！');window.location='?action=editdata';</script>";exit();}
		echo '      <div class="framehead" align="center"><b>修改“<font color="red">'.substr($cjdata['数据'],-4).$cjdata['年级'].$cjdata['考试'].'</font>”成绩数据</b></div>';
	} else echo '      <div class="framehead" align="center"><b>添加新的成绩数据</b> <font color="red">'.$alert.$teacher.'</font></div>';
?>
      <div class="framebody">第一步：上传xls格式的成绩数据(Excel文件)。</div>
      <div class="framebody" align="center">
<form enctype="multipart/form-data" action="?action=importdata&id=<?php echo $id;?>" method="post" name=upfile>
选择文件: <input name="filename" type="file"> <input type="submit" value="上传" onclick="return check()">
</form>
      </div>
      <div class="framebody">说明：</div>
      <div class="framebody">1、Excel文件必须有<font color="red">姓名</font>、<font color="red">科目</font>、<font color="red">班级</font>的标题行，如果由于数据格式问题无法上传数据，请另存文件再试；</div>
      <div class="framebody">2、高中的年级如分文理，请将文理学生放在一张表上，并添加<font color="red">类别</font>项，注明学生的文理类别；</div>
      <div class="framebody">3、程序将自动忽略“名次”、“总分”列，重新计算总分，经计算后总分为零或无班级的数据也自动忽略；</div>
      <div class="framebody">4、暂不支持Excel2007及2010格式（xlsx），请另存为Excel2003格式。</div>
      <div class="framebody">如图：<br><img src="/images/data.gif" alt="数据示例"><br></div>
<script language=JavaScript>
function check(){
	if(document.upfile.filename.value==""){
		alert("请先选择文件!");
		document.upfile.filename.focus();
		return false;}
	return true;
}
</script>
<?php
} elseif ($action=="importdata") {
	if (!$sheet) $sheet=0;
	if (!$alert) {
		$upfilename=$_FILES['filename']['name'];
		$upfilesize=$_FILES['filename']['size'];
		$filetype=substr($upfilename,strrpos($upfilename,'.'));
		if (!$upfilename) {echo "<script>window.location='?action=upfile&id=$id&alert=5';</script>";exit();}
		elseif ($filetype!='.xls' && $filetype!='.XLS') {echo "<script>window.location='?action=upfile&id=$id&alert=6';</script>";exit();}
		elseif ($upfilesize>8000000) {echo "<script>window.location='?action=upfile&id=$id&alert=7';</script>";exit();}
		if (!move_uploaded_file($_FILES['filename']['tmp_name'],upfile())) {echo "<script>window.location='?action=upfile&id=$id&alert=8';</script>";exit();}
	}
	include("reader.php");
	$data=new Spreadsheet_Excel_Reader();
	$data->setOutputEncoding('UTF-8');
	$data->read(upfile());
	$i=count($data->sheets);
	if (!$i) {echo "<script>window.location='?action=upfile&id=$id&alert=81';</script>";exit();}
	if ($id) {
		$cjrs=mysql_query("select * from cj_data where id=$id");
		$cjdata=mysql_fetch_array($cjrs);
		echo '      <div class="framehead" align="center"><b>修改“<font color="red">'.substr($cjdata['数据'],-4).$cjdata['年级'].$cjdata['考试'].'</font>”成绩数据</b></div>';
	} else echo '      <div class="framehead" align="center"><b>添加新的成绩数据</b></div>';
	echo '      <div class="framebody">第二步：已上传的文件中有如下<b>'.$i.'</b>个工作表，请单击选择含有成绩数据的工作表：<br><br>
<table width="'.(100*$i).'" align="center"><tr>';
	for($j=0;$j<$i;$j++)	{
		if ($sheet==$j && $sheet) echo '<td style="background:#C5D0DD;font-weight:bold;	border-style:ridge;	border-width:1;"><b>'.$data->boundsheets[$j]['name'].'</b></td>';
		else echo '<td style="background:#C5D0DD;font-weight:bold;	border-style:ridge;	border-width:1;"><a href="?action=insertdata&id='.$id.'&sheet='.$j.'" onclick="document.getElementById(sheet).style.visibility=\'\'">'.$data->boundsheets[$j]['name'].'</td>';
	}
	echo '	</tr></table><br>';
	if ($alert) echo '<font color=red>错误！</font><b>'.$data->boundsheets[$sheet]['name'].'</b>工作表中没有<b>'.$alert.'</b>信息，请选择其他工作表！或者<a href="?action=upfile&id='.$id.'">重新上传</a>成绩文件！<br>';
	if (!$i) echo '<font color=red>错误！</font>工作表标题栏可能设置了格式，如“数据筛选”等，请清除格式后<a href=?action=upfile&id='.$id.'>重新上传</a>文件！<br>';
	echo '<br><br>注意：<br>单击工作表后，请耐心等程序处理数据，不要点击其他工作表链接。<br><br></div>';
} elseif ($action=="insertdata") {
	ini_set('max_execution_time','0');
	if (!$sheet) $sheet=0;
	include("reader.php");
	$data=new Spreadsheet_Excel_Reader();
	$data->setOutputEncoding('UTF-8');
	$data->read(upfile());
	$sheetnum=$data->sheets[$sheet]['numCols'];
	for ($i=1; $i<=$sheetnum;$i++) {
		$colshow=$data->sheets[$sheet]['cells']['1'][$i];
		if ($colshow=='外语') $colshow='英语';
		if (in_array($colshow,$cj_subject)) $subname[]=$colshow;
		if (!$subname) $cjsubjectnum=$i+1;
		if (strstr($colshow,'班级')  || strstr($colshow,'原班') || strstr($colshow,'现班')) $cjclass=$i;
		if (strstr($colshow,'学号')) $cjnum=$i;
		if (strstr($colshow,'姓名')) $cjname=$i;
		if (strstr($colshow,'类别')) $cjsort=$i;
	}
	if (!$subname) {echo '<script>window.location=\'?action=importdata&sheet='.$sheet.'&id='.$id.'&alert=9\';</script>';exit();	}
	elseif (!$cjclass) {echo '<script>window.location=\'?action=importdata&sheet='.$sheet.'&id='.$id.'&alert=10\';</script>';exit();}
	elseif (!$cjname) {echo '<script>window.location=\'?action=importdata&sheet='.$sheet.'&id='.$id.'&alert=11\';</script>';exit();}
//读取数据并写入数组
	$rowname=array('年名','班名','姓名','班级');
	if ($cjsort) $rowname[]='类别';
	if ($cjnum) $rowname[]='学号';
	$subnum=count($subname);	
	for ($i=0; $i<$subnum;$i++) {
		$rowname[]=$subname[$i];
		$rowname[]=substr($subname[$i],0,3).'排';
		$rowname[]=substr($subname[$i],0,3).'序';
		$rowname1[]=substr($subname[$i],0,3).'排';
		$rowname2[]=substr($subname[$i],0,3).'序';
	}
	$rowname1[]='年名';
	$rowname2[]='班名';
	$rowname[]='总分';
	$subname[]='总分';
	$totalnum=$data->sheets[$sheet]['numRows'];
	$stusort='all';
	for ($i=2; $i<=$totalnum;$i++) {
		if ($data->sheets[$sheet]['cells'][$i][$cjsort]) $stusort=hz2py($data->sheets[$sheet]['cells'][$i][$cjsort]);
		$row_total=0;
		for ($j=0; $j<$subnum;$j++) $row_total=$row_total+$data->sheets[$sheet]['cells'][$i][$cjsubjectnum+$j];
		if ($data->sheets[$sheet]['cells'][$i][$cjclass] && $data->sheets[$sheet]['cells'][$i][$cjname] && $row_total) {
			$mytable[$stusort]['姓名'][]=$data->sheets[$sheet]['cells'][$i][$cjname];
			$mytable[$stusort]['班级'][]=$data->sheets[$sheet]['cells'][$i][$cjclass];
			if ($cjsort) $mytable[$stusort]['类别'][]=$data->sheets[$sheet]['cells'][$i][$cjsort];
			if ($cjnum) $mytable[$stusort]['学号'][]=$data->sheets[$sheet]['cells'][$i][$cjnum];
			for ($j=0; $j<$subnum;$j++) $mytable[$stusort][$subname[$j]][]=$data->sheets[$sheet]['cells'][$i][$cjsubjectnum+$j];
			$mytable[$stusort]['总分'][]="$row_total";
		} else {
			$wrongnum[]=$i;
		}
	}
//建立数据表结构
	$col=count($rowname);
	for ($i=0; $i<$col;$i++) {
		if ($rowname[$i]=='姓名') $rowtemp=' varchar(8) character set utf8 collate utf8_general_ci not null';
		elseif ($rowname[$i]=='班级' || $rowname[$i]=='类别') $rowtemp=' varchar(5) character set utf8 collate utf8_general_ci not null';
		elseif ($rowname[$i]=='学号') $rowtemp=' varchar(20) character set utf8 collate utf8_general_ci not null';
		elseif (strstr($rowname[$i],'排') || strstr($rowname[$i],'序') || $rowname[$i]=='年名' || $rowname[$i]=='班名') $rowtemp=' int(3) null';
		else $rowtemp=' float null';
		$fieldadd.=','.$rowname[$i].$rowtemp;
	}
	$fieldadd=substr($fieldadd,1);
	mysql_query ("drop table temptable");
	mysql_query ("create table temptable ($fieldadd) ENGINE=MyISAM DEFAULT CHARSET=utf8;");
//数组中统计名次
	if ($mytable['w']) $sort[]='w';
	if ($mytable['l']) $sort[]='l';
	if ($mytable['all']) $sort[]='all';
	$sortnum=count($sort);
//文综理综统计未实现
	for ($s=0;$s<$sortnum;$s++) {
		$m=count($mytable[$sort[$s]]['姓名']);
		$classname=array_unique($mytable[$sort[$s]]['班级']);
		sort($classname);
		$classnum=count($classname);
		for ($i=0; $i<=$subnum;$i++) {
			$temp=$mytable[$sort[$s]][$subname[$i]];
			rsort($temp);
			$temp=array_flip(array_unique($temp));
			for ($j=0; $j<$m;$j++) {
				if (count(array_unique($mytable[$sort[$s]][$subname[$i]]))!==1) $mytable[$sort[$s]][$rowname1[$i]][$j]=$temp[$mytable[$sort[$s]][$subname[$i]][$j]]+1;
				$myclass[$mytable[$sort[$s]]['班级'][$j]][$subname[$i]][$j]=$mytable[$sort[$s]][$subname[$i]][$j];
			}
			unset($temp);
			for ($j=0; $j<$classnum;$j++) {
				$temp=$myclass[$classname[$j]][$subname[$i]];
				rsort($temp);
				$temp=array_flip(array_unique($temp));
				for ($k=0; $k<$m;$k++) {
					if ($mytable[$sort[$s]]['班级'][$k]==$classname[$j] && count(array_unique($mytable[$sort[$s]][$subname[$i]]))!==1) $mytable[$sort[$s]][$rowname2[$i]][$k]=$temp[$mytable[$sort[$s]][$subname[$i]][$k]]+1;
				}
				unset($temp);
			}
		}
		unset($myclass);
		$n=$n+$m;
	}
//导入数据库
	for ($s=0;$s<$sortnum;$s++) {
		$m=count($mytable[$sort[$s]]['姓名']);
		$insertdata='insert into temptable ('.implode(',',$rowname).') values ';
		for ($i=0; $i<$m;$i++) {
			$inserttmp='';
			for ($j=0; $j<$col;$j++) $inserttmp.=",'".$mytable[$sort[$s]][$rowname[$j]][$i]."'";
			$insertdata.="(".substr($inserttmp,1)."),";
			if ($m==1000 || $m==2000) {
				mysql_query (substr($insertdata,0,-1));
				$insertdata='insert into temptable ('.implode(',',$rowname).') values ';
			}
		}
		mysql_query (substr($insertdata,0,-1));
	}
	unlink(upfile());
	echo "<script>window.location='?action=modiinfo&id=$id&n=$n&wrongnum=".implode(',',$wrongnum)."&sortnum=".$sortnum."&alert=12';</script>";
	exit();
} elseif ($action=="modiinfo") {
	$wrongnum=_get('wrongnum');
	$modi=_get('modi');
	$sortnum=_get('sortnum');
	$n=_get('n');
	if ($sortnum==2) $sheet=$sortnum;
	$subnum=count($cj_subject);
	$subname=subname('temptable',$cj_subject);
	if ($id) {
		$rs=mysql_query("select * from cj_data where id=$id");
		$data=mysql_fetch_array($rs);
		$cjdata=$data['数据'];
		$cjgrade=substr($cjdata,0,5);
		$gradeshow=num2text($cjgrade);
		$gradevalue=$data['年级'];
		$cjtest=$data['考试'];
		for ($i=0; $i<$subnum;$i++) $cjmanfen[]=$data[$cj_subject[$i]];
		$cjtime=substr($cjdata,-4);
		$tmpshow='“'.substr($cjdata,-4).$gradevalue.$cjtest.'”';
	}
	echo '      <div class="framehead" align="center"><b>添加或修改'.$tmpshow.'相关成绩信息</b> <font color="red">'.$alert.'</font></div>';
	if (!$modi) {
		echo '      <div class="framebody" align="center"><br>（共导入<b>'.$n.'</b>条数据）<a href="?action=listtemp&sheet='.$sheet.'" target="_blank"><b>浏览成绩数据</b></a><br></div>';
		if ($wrongnum) echo '<div class="framebody" align="center"><br><font color="red"><b>上传成绩出现错误！Excel数据表中无姓名、班级或总分为零的行有：'.$wrongnum.'</b></font><br><br>请检查上传数据表是否有问题！<a href="?action=upfile&id='.$id.'">重新上传</a><br><br></div>';
	}
	if (!$id) echo '      <div class="framebody">第三步：填加或修改成绩数据信息。</div>';
	if (!$cjtime) $cjtime=date('y').date('m');
	if (!$cjmanfen) {
		for ($i=0; $i<$subnum;$i++) {
			if (in_array($cj_subject[$i],$subname)) $cjmanfen[]='100';
			else $cjmanfen[]='0';
		}
	}
	echo '      <div class="framebody">
<form action="?action=processdata&id='.$id.'" method="post" name="processdata">
      <table width="80%" align="center">
        <tr>
          <td width="20%" align="right"><b>*</b> 年级：</td>
          <td><select name="grade" id="grade">';
	if ($id) echo '<option value="'.$cjgrade.'">'.$gradeshow.'</option>';
	else echo '<option value="">请选择年级</option>';
	$gradenum=$cj_grade3.';'.$cj_grade2.';'.$cj_grade1.';';
	$gradenum=str_replace('20', 'g20',$gradenum).str_replace('20', 'c20',$cj_grade3.';'.$cj_grade2.';'.$cj_grade1);
	$gradetmp=explode(';',$gradenum);
	for ($i=0; $i<6;$i++) echo '<option value="'.$gradetmp[$i].'">'.num2text($gradetmp[$i]).'</option>';
	echo '</select></td>
        </tr>
        <tr>
          <td align="right"><b>*</b> 考试名称：</td>
          <td><select name="cjgrade" id="cjgrade">';
	if ($gradevalue) echo '<option value="'.$gradevalue.'">'.$gradevalue.'</option>';
	$gradecount=count($cj_gradename);
	for ($i=0; $i<$gradecount;$i++) echo '<option value="'.$cj_gradename[$i].'">'.$cj_gradename[$i].'</option>';
	echo '</select> <select name="cjtest" id="cjtest">';
	if ($cjtest) echo '<option value="'.$cjtest.'">'.$cjtest.'</option>';
	$testcount=count($cj_testname);
	for ($i=0; $i<$testcount;$i++) echo '<option value="'.$cj_testname[$i].'">'.$cj_testname[$i].'</option>';
	echo '</select></td>
        </tr>
        <tr>
          <td align="right"><b>*</b> 考试时间：</td>
          <td><input type="text" name="cjtime" value="'.$cjtime.'"></td>
        </tr>
        <tr>
          <td align="right"><b>*</b> 满分：</td>
          <td>';
	for ($i=0; $i<$subnum;$i++) {
		echo $cj_subject[$i].' <input type="text" name="'.$i.'" value="'.$cjmanfen[$i].'" size="3">分&nbsp;&nbsp;&nbsp; ';
		if ($i==2 || $i==5 || $i==8) echo '<br>';
	}
	echo '</td>
        </tr>
        <tr>';
	if ($id && !$n) echo '          <td colspan="2" align="center"><input type="hidden" name="modi" value="'.$modi.'"><input type="submit" value="提交" onclick="return check()"></td>';
	else echo '          <td colspan="2" align="right"><input type="hidden" name="n" value="'.$n.'"><input name=last type="submit" value="下一步" onclick="return check()"></td>';
	echo '        </tr>
     </table></form></div>
<script language=JavaScript>
function check(){
	if(document.processdata.grade.value==""){
		alert("请选择年级!");
		document.processdata.grade.focus();
		return false;}
	if(document.processdata.cjgrade.value==""){
		alert("请选择考试年级!");
		document.processdata.cjgrade.focus();
		return false;}
	return true;
}
</script>';
	if ($n && !$id) echo '      <div class="framebody">说明：请填写成绩数据相关信息并<a href="?action=listtemp" target="_blank"><b>核实成绩数据</b></a>，确认无误后请点下一步，将成绩信息写入数据库，如有<a href="?action=teacherlist" target="_blank"><b>任课教师信息</b></a>和上次成绩信息，程序会自动计算名次变化并添加。</div>';
} elseif ($action=="processdata") {
	$cjtime=_post('cjtime');
	$cjtest=_post('cjtest');
	$cjgrade=_post('cjgrade');
	$n=_post('n');
	$modi=_post('modi');
	if (!$grade || !$cjtime || !$cjtest || !$cjgrade) {echo "<script>alert('请详细填写成绩相关信息！');history.back();</script>";exit();}
	$cjdata=$grade.'_'.$cjtime;
	if (!$id) {
		$datacheck1=mysql_num_rows(mysql_query("select * from cj_data where 数据='$cjdata'"));
		$datacheck2=mysql_num_rows(mysql_query("select * from ".$cjdata));
		if ($datacheck1 || $datacheck2) {echo "<script>alert('已有此成绩数据或成绩相关信息，请检查输入是否有误！');history.back();</script>";exit();}
	}
	$subnum=count($cj_subject);	
	for ($i=0; $i<$subnum;$i++) {
		$posti=_post($i);
		if (!$posti) $posti=0;
		$insert1.=",".$cj_subject[$i];
		$insert2.=",".$posti;
		$update1.=",".$cj_subject[$i]."=".$posti;
	}
	if ($id) {
		mysql_query("update cj_data set 年级='$cjgrade',考试='$cjtest'".$update1.",数据='$cjdata' where id=$id");
		if (!$n) {echo "<script>window.location='?action=modiinfo&alert=1&id=".$id."&modi=".$modi."';</script>";exit();}
	} else {
		$predata=mysql_fetch_array(mysql_query("select * from cj_data where 数据 like '%$grade%' and 现在=1"));
		$pretest=$predata['数据'];
		mysql_query("update cj_data set 现在=0 where 数据 like '%$grade%' and 现在=1");
		mysql_query("insert into cj_data (年级,考试".$insert1.",数据,上次,导入) values ('$cjgrade','$cjtest'".$insert2.",'$cjdata','$pretest','1')");
	}
	mysql_query("drop table $cjdata");
	mysql_query("rename table temptable to ".$cjdata);
//添加名次变化情况
	if ($pretest) {
		mysql_query ("alter table $cjdata add 年变 int(3) not null after 年名,add 班变 int(3) not null after 班名");
		$rs=mysql_query("select * from $cjdata");
		while($rsdata=mysql_fetch_array($rs)){
			$sortname=$rsdata['姓名'];
			$sortgrade=$rsdata['年名'];
			$sortclass=$rsdata['班名'];
			$classname=$rsdata['班级'];
			$subrs=mysql_query("select * from $pretest where 姓名='$sortname' and 班级=$classname");
			$subdata=mysql_fetch_array($subrs);
			mysql_query("update $cjdata set 年变=".$subdata['年名']."-年名,班变=".$subdata['班名']."-班名 where 姓名='$sortname' and 班级=$classname");
		}
	}
//填加任课教师信息
	$datacheck1=mysql_num_rows(mysql_query("select * from cj_teacher where 数据='$cjdata'"));
	$datacheck2=mysql_num_rows(mysql_query("select * from cj_teacher where 数据='".substr($cjdata,0,5)."'"));
	if (!$datacheck1 && $datacheck2) {	
		$classall=check($cjdata,'班级');
		$clanum=count($classall);	
		for ($i=0;$i<$clanum;$i++){
			$rs=mysql_query("select * from cj_teacher where 数据='".substr($cjdata,0,5)."' and 班级=".$classall[$i]);
			if(!$rs){die("教师信息读取错误");}
			$teacher=mysql_fetch_array($rs);
			$classteacher1='';
			$classteacher2='';
			for ($j=0; $j<$subnum;$j++) {
				$classteacher1.=",".$cj_subject[$j];
				$classteacher2.=",'".$teacher[$cj_subject[$j]]."'";
			}
			$banzhuren=$teacher['班主任'];
			mysql_query ("insert into cj_teacher (数据,班级".$classteacher1.",班主任) values ('$cjdata',".$classall[$i].$classteacher2.",'$banzhuren')");
		}
	} elseif (!$datacheck1 && $datacheck2) $info='1';
	else $info='2';
	$cjtitle=$cjgrade.$cjtest.$cjtime.'成绩已上传！';
	$cjcontent='<p>'.$cjtitle.'请各位老师查询！</p>';
	mysql_query ("insert into cj_news (title,content) values ('$cjtitle','$cjcontent')");
	echo "<script>window.location='?action=upfile&alert=13&teacher=$info';</script>";
	exit();
} elseif ($action=="addnotice") {?>
      <div class="framehead" align="center"><b>修改系统公告</b> | <a href="?action=moditeacher&data=new">添加系统公告</a><font color="red"><?php echo $alert;?></font></div>
      <div class="framebody">
		<table width="80%" border="0" align="center" cellpadding="1" cellspacing="1">
<form method="post" action="?action=noticeedit">
		  <tr height="25"><td align="right"width="25%">公告标题：</td><td><input type="text" name="title" value="" size="50"></td></tr>
		  <tr><td align="right">公告内容：</td><td><textarea name="content" rows="6" cols="50"><?php echo $content;?></textarea></td></tr>
		  <tr height="25"><td align="center" colspan="2"><input type="submit" value="提交"></td></tr>
</form>
		</table>
      </div>
<?php
}?>
      <div class="framefoot"></div><br>
    </td>
  </tr>
</table>
<?php include("../foot.php");?>
</body>
</html>
