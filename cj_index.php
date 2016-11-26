<?php
if (!$usersort) {Header("Location:".$web_root."/");exit();}
$stusort='';
$classname=_get('classname');
if ($classname) {
	$g_name=substr($classname,0,5);
	$c_name=substr($classname,6);
	if ($c_name=='w' || $c_name=='l') {
		$stusort=$c_name;
		$c_name='';
	} elseif (!ctype_digit($c_name)) $c_name='';
} elseif ($usermain) {
	$g_name=$usergrade;
	$c_name=$usermain;
	$classname=$g_name.'_'.$c_name;
} elseif ($usergrade) {
	$g_name=$usergrade;
	$c_name='';
	$classname=$g_name;
} else {
	$g_name='c'.$cj_grade3;
	$c_name='';
	$classname=$g_name;
}
$result=$mysqli->query("select * from cj_data where 数据 like '$g_name%' and 现在='1'");
$data=$result->fetch_array();
$table=$data['数据'];
$id=$data['id'];
if (!count($table)) {
	echo "<script>window.location='".$web_root."/?url=cj_index';</script>";
	exit();
}
//检测班级信息
if ($c_name && $data['文理']) {
	$clatmp=explode(";",$data['班级']);
	if (in_array($c_name,explode(",",$clatmp[0]))) {
		$stusort=hz2py(explode(";",$data['文理'])[0]);
	} elseif (in_array($c_name,explode(",",$clatmp[1]))) {
		$stusort=hz2py(explode(";",$data['文理'])[1]);
	} else {
		echo "<script>window.location='".$web_root."/?url=cj_index';</script>";
		exit();
	}
} elseif ($c_name && !in_array($c_name,explode(",",$data['班级']))) {
	echo "<script>window.location='".$web_root."/?url=cj_index';</script>";
	exit();
}
//检测科目信息
$wlsort=array('l'=>'0','w'=>'1');
if ($stusort) $subname=explode(",",explode(";",$data['科目'])[$wlsort[$stusort]]);
elseif ($data['文理']) $subname=explode(",",explode(";",$data['科目'])[0]);
else $subname=explode(",",$data['科目']);
$subnum=count($subname);
$subjectshow='';
for ($i=0;$i<$subnum;$i++) $subjectshow.=substr($subname[$i],0,3);
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
              <li class="active"><a href="#">历次</a>
              <li><a href="'.$web_root.'/?url=cj_sheets&id='.$id.'&classname='.$classname.'">统计</a>
            </ul>
            <div class="tab-content">
              <div class="tab-pane fade in active" style="min-height:300px">
<div style="padding:5px;margin-bottom:20px;border:1px solid transparent;border-radius:4px;background-color:#d2d6de;border-color:#d2d6de">
                <form row="form" class="form-inline">
                  <div class="form-group">
                    <select name="classname" id="classname" onchange="window.location=\''.$web_root.'/?url=cj_index&classname=\'+this.value" class="form-control">';
$classrs=$mysqli->query("select * from cj_data where 现在=1 and (数据 like '%$cj_grade1%' or 数据 like '%$cj_grade2%' or 数据 like '%$cj_grade3%') order by 数据");
while ($classdata=$classrs->fetch_array()){
	$classtable=$classdata['数据'];
	$gratemp=substr($classtable,0,5);
	$gravalue=num2text($gratemp);
	$gravalue=str_replace('中'.$cj_grade3.'级','三',$gravalue);
	$gravalue=str_replace('中'.$cj_grade2.'级','二',$gravalue);
	$gravalue=str_replace('中'.$cj_grade1.'级','一',$gravalue);
	if ($classdata['文理']) {
		$grasort=explode(';',$classdata['文理']);
		$sortnum=count($grasort);
	} else {
		$sortnum=1;
		$grasort=array('');
	}
	for ($i=0;$i<$sortnum;$i++) {
		$gratmp=$gratemp;
		if ($grasort) $gratmp.='_'.hz2py($grasort[$i]);
		$sele='';
		if (strstr($gratmp,$classname) && !$stusort) {
			$sele=' selected';
			$stusort=hz2py($grasort[$i]);
		} elseif ($gratmp==$classname) {
			$sele=' selected';
		}
		echo '<option value='.$gratmp.$sele.'>'.$gravalue.$grasort[$i].'</option>';
		if ($grasort[$i]) $clatmp=explode(",",explode(";",$classdata['班级'])[$i]);
		else $clatmp=explode(",",$classdata['班级']);
		$clanum=count($clatmp);
		for ($j=0;$j<$clanum;$j++){
			$sele='';
			if ($clatmp[$j]==$c_name && $g_name==$gratemp) $sele=' selected';
			echo '<option value='.$gratemp.'_'.$clatmp[$j].$sele.'>'.$gravalue.$clatmp[$j].'班</option>';
		}
	}
}
echo '
                    </select>
                    <label class="control-label"><h4> 学生历次成绩</h4></label>
                  </div>
                </form>
</div>
                <form method="get" name="search" row="form" class="form-inline">
                  <div class="form-group">
                  <input type="hidden" name="classname" value="'.$classname.'">
                    <select name="line" id="line" class="form-control"><option value="1">不显曲线图</option><option value="2" selected>显曲线链接</option><option value="3">显示曲线图</option></select>
                  </div>
                  <div class="form-group">
                    <select name="sort" id="sort" class="form-control"><option value="1">显示各科年级排名</option><option value="2">显示各科班级排名</option><option value="3">各科年级班级排名</option><option value="0">不显各科排名</option></select>
                  </div>
                  <div class="form-group">
                    <select name="subjectshow" id="subjectshow" class="form-control"><option value="1" selected>'.$subjectshow.'</option><option value="0">所有科目</option></select>
                  </div>';
if ($g_name=="g".$cj_grade3) echo '
                  <div class="form-group">
                    <select name="gradeshow" id="gradeshow" class="form-control"><option value="g3" selected>只高三成绩</option><option value="all">全部成绩</option></select>
                  </div>';
elseif ($g_name=="c".$cj_grade3) echo '
                  <div class="form-group">
                    <select name="gradeshow" id="gradeshow" class="form-control"><option value="c3" selected>只初三成绩</option><option value="all">全部成绩</option></select>
                  </div>';
elseif ($g_name=="c".$cj_grade2) echo '
                  <div class="form-group">
                    <select name="gradeshow" id="gradeshow" class="form-control"><option value="c2" selected>只初二成绩</option><option value="all" selected>全部成绩</option></select>
                  </div>';
elseif ($g_name=="g".$cj_grade2) echo '
                  <div class="form-group">
                    <select name="gradeshow" id="gradeshow" class="form-control"><option value="g2" selected>只高二成绩</option><option value="all" selected>全部成绩</option></select>
                  </div>';
if (!$c_name) {
	if ($data['文理']) $tmp=" where 类别='".py2hz($stusort)."'";
	else $tmp='';
	$toprs=$mysqli->query("select * from $table$tmp");
	$gradetop=$toprs->num_rows;
	if ($gradetop>20) $gradetop=20;
	echo '
                  <div class="form-group">
                    <input type="hidden" name="name" value="all"> 年级前 <input type="text" name="gradetop" value="'.$gradetop.'" class="form-control" size="6"> 名
                  </div>';
} else {
	$studentrs=$mysqli->query("select * from $table where 班级='$c_name'");
	echo '
                  <div class="form-group">
                    <select name="name" id="name" class="form-control"><option value="all" selected>全部学生</option>';
	while ($student=$studentrs->fetch_array()) echo '<option value='.$student['姓名'].'>'.$student['姓名'].'</option>';
	echo '</select>
                  </div>';
}
echo '
                  <div class="form-group">
                    <input name="id" type="hidden" value="'.$id.'">
                    <button type="submit" class="btn btn-primary" value="cj_student" name="url">查询</button> <button type="submit" class="btn btn-primary" value="student.xls" name="url">下载 <img src="images/xls.gif" height="18px"></button>
                  </div>
                </form>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
  </div>
';
$pagename='成绩分析';
