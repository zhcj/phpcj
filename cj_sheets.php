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
//} else {
//	echo "<script>alert('dsfa');window.location='".$web_root."/?url=cj_index';</script>";
//	exit();
}

$c_name=_get('classname');
$g_name=_get('gradename');
$t_name=_get('testname');
if (!$g_name && $usergrade) $g_name=$usergrade;
elseif (!$g_name) $g_name='c'.$cj_grade3;
if (ctype_digit($id)) $exe="select * from cj_data where id=".$id; 
elseif ($t_name) $exe="select * from cj_data where 数据='".$g_name.'_'.$t_name."'";
else $exe="select * from cj_data where 数据 like '$g_name%' and 现在='1'";
$result=$mysqli->query($exe);
$data=$result->fetch_array();
$id=$data['id'];
$table=$data['数据'];
if (!$table) {echo "<script>window.location='".$web_root."/?url=cj_sheets';</script>";exit();}
$testname=num2text(substr($table,0,5)).'20'.substr($table,-4,2).'年'.str_replace('0','',substr($table,-2,1)).substr($table,-1).'月'.$data['年级'].$data['考试'].'考试';
$testtmp=explode("_",$table);
$g_name=$testtmp[0];
$t_name=$testtmp[1];
$subjectsort=_get('subjectsort');
$step=_get('step');
$fd=_get('fd');
$fdsort=_get('fdsort');
$fdnum=_get('fdnum');
$getstusort=_get('getstusort');
$select1='';
$select2='';
$select3='';
$select4='';
$select5='';
$select6='';
$select7='';
$select8='';
$select9='';
$select10='';
$select11='';
if ($fd=='fs') $select1=' selected';
elseif ($fd=='mc' || !$fd) $select2=' selected';
if ($fdsort=='fd') $select3=' selected';
elseif ($fdsort=='lj') $select4=' selected';
elseif ($fdsort=='all') $select5=' selected';
if ($step=='10') $select6=' selected';
elseif ($step=='100') $select7=' selected';
elseif ($step=='zdy') $select8=' selected';
elseif ($step=='fs') $select9=' selected';
if ($getstusort=='w') $select10=' selected';
elseif ($getstusort=='l') $select11=' selected';
if (!$subjectsort) $subjectsort='zf';
$wlsort=array('理'=>'0','文'=>'1');
echo '
<script src="js/jquery.slimscroll.min.js"></script>
<script src="js/fastclick.min.js"></script>
  <div class="content-wrapper">
    <section class="content-header">
      <h1>'.$testname.'</h1>
    </section>
    <section class="content">
      <div class="row">
        <div class="col-lg-12">
          <div class="nav-tabs-custom">
            <ul class="nav nav-tabs">
              <li><a href="'.$web_root.'/?url=cj_index&classname='.$c_name.'">历次</a></li>
              <li class="active"><a href="#">统计</a></li>
            </ul>
            <div class="tab-content">
              <div class="tab-pane fade in active">
<div style="padding:5px;margin-bottom:20px;border:1px solid transparent;border-radius:4px;background-color:#d2d6de;border-color:#d2d6de">
                <form method="get" row="form" class="form-inline">
                  <div class="form-group">
                    <select name="gradename" id="gradename" onchange="window.location=\''.$web_root.'/?url=cj_sheets&gradename=\'+this.value" class="form-control">';
$graders=$mysqli->query("select * from cj_data where 现在=1 and (数据 like '%$cj_grade1%' or 数据 like '%$cj_grade2%' or 数据 like '%$cj_grade3%') order by 数据");
while ($classdata=$graders->fetch_array()) {
	$classtable=$classdata['数据'];
	$gratemp=substr($classtable,0,5);
	$gravalue=num2text($gratemp);
	$gravalue=str_replace('中'.$cj_grade3.'级','三',$gravalue);
	$gravalue=str_replace('中'.$cj_grade2.'级','二',$gravalue);
	$gravalue=str_replace('中'.$cj_grade1.'级','一',$gravalue);
	$gravalue=num2text($gratemp).'('.$gravalue.')';
	$sele='';
	if (substr($classtable,0,5)==$g_name) $sele=' selected';
	echo '<option value='.$gratemp.$sele.'>'.$gravalue.'</option>';
}
echo '
                    </select>
                  </div>
                  <div class="form-group">
                    <select name="testname" id="testname" onchange="window.location=\''.$web_root.'/?url=cj_sheets&gradename='.$g_name.'&fd='.$fd.'&subjectsort='.$subjectsort.'&fdsort='.$fdsort.'&step='.$step.'&getstusort='.$getstusort.'&testname=\'+this.value" class="form-control">';
$testrs=$mysqli->query("select * from cj_data where 数据 like '%$g_name%' order by 数据");
while ($classdata=$testrs->fetch_array()) {
	$classtable=$classdata['数据'];
	$gratemp=substr($classtable,-4);
	$gravalue='20'.substr($gratemp,0,2).'年'.substr($gratemp,2,2).'月'.$classdata['考试'];
	$sele='';
	if (substr($classtable,-4)==$t_name) $sele=' selected';
	echo '<option value='.$gratemp.$sele.'>'.$gravalue.'</option>';
}
echo '
                    </select>
                    <label class="control-label"><h4> 各种统计表</h4></label>
                  </div>
                </form>
</div>
                <div class="row">
                  <div class="col-md-6">
                    <div class="box box-default box-solid">
                      <div class="box-header with-border">
                        <h3 class="box-title">两率一分</h3>
                        <div class="box-tools pull-right">
                          <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                          </button>
                        </div>
                      </div>
                      <div class="box-body" style="min-height:200px">
                        <h5>
                          <a class="btn btn-primary" href="'.$web_root.'/?url=cj_table&id='.$id.'" title="平均分、及格率、优秀率、任课老师">简表</a> <a class="btn btn-primary" href="'.$web_root.'/?url=table.xls&id='.$id.'">下载 <img src="images/xls.gif" height="18px"></a> <a class="btn btn-primary" href="?url=cj_table&id='.$id.'&info=more" title="增加最高分、最低分、最优生、学困生">详表</a> <a class="btn btn-primary" href="'.$web_root.'/?url=table.xls&id='.$id.'&info=more">下载 <img src="images/xls.gif" height="18px"></a>
                        </h5>
                        <form method="get" row="form" name="tjb" class="form-inline">
                        <h5>
                          只统计各班前 <input type="text" value="40" name="gradetop" size="4" class="form-control"> 名
                        </h5>
                        <h5>
                          <button name="info" value="" class="btn btn-primary">简表</button> <button name="info" value="more" class="btn btn-primary">详表</button><input type="hidden" value="cj_table" name="url"> <input type="hidden" value="'.$id.'" name="id">
                        </h5>
                        </form>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="box box-default box-solid">
                      <div class="box-header with-border">
                        <h3 class="box-title">分段统计</h3>
                        <div class="box-tools pull-right">
                          <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                          </button>
                        </div>
                      </div>
                      <div class="box-body" style="min-height:200px">
                        <form method="get" row="form" name="fdtj" class="form-inline">
                        <h5>
                          <input name="id" type="hidden" value="'.$id.'"> 按 <select name="fd" id="fd" onchange="window.location=\''.$web_root.'/?url=cj_sheets&gradename='.$g_name.'&fdsort='.$fdsort.'&step='.$step.'&subjectsort='.$subjectsort.'&getstusort='.$getstusort.'&fd=\'+this.value" class="form-control"><option value="fs"'.$select1.'>分数</option><option value="mc"'.$select2.'>名次</option></select>';
$fdnum='10;20';
if ($fd=='mc') {
	$fdnum='10;20';
} elseif ($fd=='fs') {
	$submaxrs=$mysqli->query("select max(".py2hz($subjectsort).")from $table");
	$submax=$submaxrs->fetch_array();
	$submax=$submax['0'];
	if ($step=='fs') $fdnum=($submax-1).';'.($submax-20);
	else {
		$subminrs=$mysqli->query("select min(".py2hz($subjectsort).")from $table");
		$submin=$subminrs->fetch_array();
		$submin=$submin['0'];
		if ($subjectsort=='zf' || !$subjectsort) {
			$submax=floor($submax/10+1)*10;
			$submin=floor($submin/10)*10;
			if ($submin>'400') $fdnum=$submax.'-'.$submin;
			else $fdnum=$submax.'-400';
		} else {
			$submax=floor($submax/5+1)*5;
			$submin=floor($submin/5)*5;
			if ($submin>'40') $fdnum=$submax.'-'.$submin;
			else $fdnum=$submax.'-40';
		}
	}
	echo '<select name="step" id="step" onchange="window.location=\''.$web_root.'/?url=cj_sheets&gradename='.$g_name.'&fd='.$fd.'&fdsort='.$fdsort.'&subjectsort='.$subjectsort.'&getstusort='.$getstusort.'&step=\'+this.value" class="form-control"><option value="10"'.$select6.'>步长为10分</option><option value="100"'.$select7.'>步长为100分</option><option value="zdy"'.$select8.'>自定义步长</option><option value="fs"'.$select9.'>自定义分数线</option></select>';
	if ($step=='zdy') echo '为<input type="text" name="step" value="20" size="3" class="form-control">';
}
$idrs=$mysqli->query("select * from cj_data where id=$id");
$iddata=$idrs->fetch_array();
//	$stusort=check($table,'类别');
if (!$getstusort && $iddata['文理']) $getstusort=hz2py(explode(";",$iddata['文理'])[0]);
if ($getstusort) echo ' <select name="getstusort" id="getstusort" onchange="window.location=\''.$web_root.'/?url=cj_sheets&gradename='.$g_name.'&fd='.$fd.'&fdsort='.$fdsort.'&subjectsort='.$subjectsort.'&step='.$step.'&getstusort=\'+this.value" class="form-control"><option value="l"'.$select11.'>理科</option><option value="w"'.$select10.'>文科</option></select>';
if ($getstusort=='w') $subject=explode(",",explode(";",$iddata['科目'])[$wlsort['文']]);
elseif ($getstusort=='l') $subject=explode(",",explode(";",$iddata['科目'])[$wlsort['理']]);
else $subject=explode(",",$iddata['科目']);
$num=count($subject);
echo ' <select name="subjectsort" id="subjectsort" onchange="window.location=\''.$web_root.'/?url=cj_sheets&gradename='.$g_name.'&fdsort='.$fdsort.'&step='.$step.'&fd='.$fd.'&getstusort='.$getstusort.'&subjectsort=\'+this.value" class="form-control"><option value="zf">总分</option>';
for ($j=0;$j<$num;$j++) {
	if (hz2py($subject[$j])==$subjectsort) $temp=' selected';
	echo '<option value="'.hz2py($subject[$j]).'"'.$temp.'>'.$subject[$j].'</option>';
	$temp='';
}
echo '</select>';
if ($fd=='mc' || !$fd) echo '
                        </h5>
                        <h5>
                          前 ';
echo '<input type="text" name="fdnum" value="'.$fdnum.'" size="10" class="form-control">';
if ($fd=='mc' || !$fd) echo ' 名';
elseif ($fd=='fs') echo '分数段';
echo '的 <select name="fdsort" id="fdsort" onchange="window.location=\''.$web_root.'/?url=cj_sheets&gradename='.$g_name.'&fd='.$fd.'&step='.$step.'&subjectsort='.$subjectsort.'&getstusort='.$getstusort.'&fdsort=\'+this.value" class="form-control"><option value="lj"'.$select4.'>累计</option><option value="fd"'.$select3.'>分段</option></select> 人数
                        </h5>
                        <h5>
                          <button name="url" value="cj_fdtj" class="btn btn-primary">统计</button> <button class="btn btn-primary" name="url" value="fdtj.xls">下载 <img src="images/xls.gif" height="18px"></button>
                        </h5>
                        </form>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="box box-default box-solid">
                      <div class="box-header with-border">
                        <h3 class="box-title">等效人数</h3>
                        <div class="box-tools pull-right">
                          <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                          </button>
                        </div>
                      </div>
                      <div class="box-body" style="min-height:200px">
                        <form method="get" row="form" name="fdtj" class="form-inline">
                        <h5>
                            <input name="id" type="hidden" value="'.$id.'"> 按 <select name="fd" id="fd" onchange="window.location=\''.$web_root.'/?url=cj_sheets&gradename='.$g_name.'&fdsort='.$fdsort.'&getstusort='.$getstusort.'&fd=\'+this.value" class="form-control"><option value="fs"'.$select1.'>分数</option><option value="mc"'.$select2.'>名次</option></select>';
$fdnum='50;100';
if ($fd=='fs') {
	$tmp='';
	if ($getstusort) $tmp="where 类别 like '%".py2hz($getstusort)."%'";
	$submaxrs=$mysqli->query("select max(总分)from $table $tmp");
	$submax=$submaxrs->fetch_array();
	$submax=$submax['0'];
	$submax=floor($submax/10);
	$fdnum=(($submax-1)*10);
	$submax=floor($submax/10);
	$fdnum=$fdnum.';'.($submax*100).';'.(($submax-1)*100);
}
if ($getstusort) echo ' <select name="getstusort" id="getstusort" onchange="window.location=\''.$web_root.'/?url=cj_sheets&gradename='.$g_name.'&fd='.$fd.'&fdsort='.$fdsort.'&getstusort=\'+this.value" class="form-control"><option value="l"'.$select11.'>理科</option><option value="w"'.$select10.'>文科</option></select> ';
if ($fd=='mc' || !$fd) echo '
                        </h5>
                        <h5>
                          前 ';
echo '<input type="text" name="fdnum" value="'.$fdnum.'" size="10" class="form-control">';
if ($fd=='mc' || !$fd) echo ' 名
                        </h5>
                        <h5>';
echo '
                          <button name="url" value="cj_dxf" class="btn btn-primary">统计</button> <button class="btn btn-primary" name="url" value="dxf.xls">下载 <img src="images/xls.gif" height="18px"></button>
                        </h5>
                        </form>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="box box-default box-solid">
                      <div class="box-header with-border">
                        <h3 class="box-title">班成绩单</h3>
                        <div class="box-tools pull-right">
                          <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-plus"></i>
                          </button>
                        </div>
                      </div>
                      <div class="box-body" style="min-height:200px">
                        <form method="get" row="form" name="cjd" class="form-inline">
                        <h5>
                            <input type="hidden" value="cj_sheet" name="url"><input name="id" type="hidden" value="'.$id.'"><input name="gradename" type="hidden" value="'.$g_name.'"><input name="sheet" type="hidden" value="cjd"><select name="sheetsort" id="sheetsort" class="form-control"><option value="">不显各科排名</option><option value="1">各科年级排名</option><option value="2">各科班级排名</option><option value="3">年级班级排名</option></select>';
//$grasort=check($table,'类别');
if ($iddata['文理']) {
	$grasort=explode(";",$iddata['文理']);
	$sortnum=count($grasort);
} else {
	$sortnum=1;
	$grasort=array('');
}
if ($sortnum=='2') echo '
                        </h5>
                        <h5>
';
for ($i=0;$i<$sortnum;$i++) {
//	$subject=subname($table,$cj_subject,$grasort[$i]);
	if ($grasort[$i]=='文') $subject=explode(",",explode(";",$iddata['科目'])[$i]);
	elseif ($grasort[$i]=='理') $subject=explode(",",explode(";",$iddata['科目'])[$i]);
	else $subject=explode(",",$iddata['科目']);
	$subnum=count($subject);
	echo '<select name="subjectsort'.hz2py($grasort[$i]).'" id="subjectsort'.hz2py($grasort[$i]).'" class="form-control"><option value="">总分降序</option>';
	for ($j=0;$j<$subnum;$j++) echo '<option value="'.hz2py($subject[$j]).'">'.$subject[$j].'降序</option>';
	echo '</select>
 <input type="submit" name="class" value="'.$grasort[$i].'年级" class="btn btn-primary"> ';
	if ($grasort[$i]=='文') $clatmp=explode(",",explode(";",$iddata['班级'])[$i]);
	elseif ($grasort[$i]=='理') $clatmp=explode(",",explode(";",$iddata['班级'])[$i]);
	else $clatmp=explode(",",$iddata['班级']);
	echo '
                        </h5>
                        <h5>
';
	$clanum=count($clatmp);
	for ($j=0;$j<$clanum;$j++) {
		if ($clatmp[$j]==$usermain && $usergrade==$g_name) echo '<input type="submit" name="class" value="'.$clatmp[$j].'班" class="btn btn-info"> ';
		else echo '<input type="submit" name="class" value="'.$clatmp[$j].'班" class="btn btn-primary"> ';
	}
	if ($sortnum=='2' && !$i) echo '
                        </h5>
                        <h5>
';
}
echo '
                        </h5>
                        </form>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="box box-default box-solid">
                      <div class="box-header with-border">
                        <h3 class="box-title">名次变化</h3>
                        <div class="box-tools pull-right">
                          <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-plus"></i>
                          </button>
                        </div>
                      </div>
                      <div class="box-body" style="min-height:200px">
                        <form method="get" row="form" name="jinbu" class="form-inline">
                        <h5>';
$graname=substr($iddata['数据'],0,5);
$countnumrs=$mysqli->query("select id from cj_data where 现在=0 and 数据 like '%$graname%'");
$countnum=$countnumrs->num_rows;
if ($countnum) {
	echo '
                            <input type="hidden" value="cj_sheet" name="url"><input name="gradename" type="hidden" value="'.$g_name.'"><input name="id" type="hidden" value="'.$id.'"><select name="sheet" id="sheet" class="form-control"><option value="jinbu1">年级名次变化</option><option value="jinbu2">班级名次变化</option></select> ';
	if ($sortnum=='2') echo '
                        </h5>
                        <h5>
';
	for ($i=0;$i<$sortnum;$i++) {
		echo ' <input type="submit" name="class" value="'.$grasort[$i].'年级" class="btn btn-primary"> ';
		if ($grasort[$i]=='文') $clatmp=explode(",",explode(";",$iddata['班级'])[$i]);
		elseif ($grasort[$i]=='理') $clatmp=explode(",",explode(";",$iddata['班级'])[$i]);
		else $clatmp=explode(",",$iddata['班级']);
	if ($sortnum=='1') echo '
                        </h5>
                        <h5>
';
		$clanum=count($clatmp);
		for ($j=0;$j<$clanum;$j++) {
			if ($clatmp[$j]==$usermain && $usergrade==$g_name) echo '<input type="submit" name="class" value="'.$clatmp[$j].'班" class="btn btn-info"> ';
			else echo '<input type="submit" name="class" value="'.$clatmp[$j].'班" class="btn btn-primary"> ';
		}
		if ($sortnum=='2' && !$i) echo '
                        </h5>
                        <h5>
';
	}
}
	echo '
                        </h5>
                        </form>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="box box-default box-solid">
                      <div class="box-header with-border">
                        <h3 class="box-title">成绩条</h3>
                        <div class="box-tools pull-right">
                          <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-plus"></i>
                          </button>
                        </div>
                      </div>
                      <div class="box-body" style="min-height:200px">
                        <form method="get" row="form" name="cjt" class="form-inline">
                        <h5>
                            <input name="sheet" type="hidden" value="cjt"><input type="hidden" value="cj_sheet" name="url"><input name="gradename" type="hidden" value="'.$g_name.'"><input name="id" type="hidden" value="'.$id.'"><select name="sheetsort" id="sheetsort" class="form-control"><option value="">不显各科排名</option><option value="1">各科年级排名</option><option value="2">各科班级排名</option><option value="3">年级班级排名</option></select>
                        </h5>
                        <h5>
';
//$clatmp=check($table,'班级');
if ($iddata['文理']) {
	$clatmp=str_replace(";",",",$iddata['班级']);
	$clatmp=explode(",",$clatmp);
} else $clatmp=explode(",",$iddata['班级']);
$clanum=count($clatmp);
for ($j=0;$j<$clanum;$j++) {
	if ($clatmp[$j]==$usermain && $usergrade==$g_name) echo '<input type="submit" name="class" value="'.$clatmp[$j].'班" class="btn btn-info"> ';
	else echo '<input type="submit" name="class" value="'.$clatmp[$j].'班" class="btn btn-primary"> ';
}
echo '
                        </h5>
                        </form>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
  </div>
';
$pagename='成绩分析';
