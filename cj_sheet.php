<?php
if (!$usersort) {Header("Location:".$web_root."/");exit();}
$id=_get('id');
$sheet=_get('sheet');
$class=_get('class');
$sheetsort=_get('sheetsort');
$url=$web_root.'/?url=sheet.xls&id='.$id.'&sheet='.$sheet.'&class='.$class.'&sheetsort='.$sheetsort;
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
              <li><a href="'.$web_root.'/?url=cj_index">历次</a></li>
              <li><a href="'.$web_root.'/?url=cj_sheets&id='.$id.'">统计</a></li>
              <li class="active"><a href="'.$web_root.'/?url=cj_sheets&id='.$id.'">'.$tabname.'<i class="fa fa-times"></i></a>
              </li>
            </ul>
            <div class="tab-content">
              <div class="tab-pane fade in active">
                <div class="table-responsive">
                  <div style="text-align:right;padding:5px;"><a class="btn btn-primary" href="'.$url.'">下载 <img src="images/xls.gif" height="18px"></a></div>
                  <h4 align="center">'.$sheetname.'</h4>
                  <table class="table table-bordered table-condensed table-hover">
                    <tr align="center">';
for ($i=0;$i<$subnum;$i++) {
	if ($subjectsort==$subname[$i] || ($sheet=='jinbu1' && $subname[$i]=='年变') || ($sheet=='jinbu2' && $subname[$i]=='班变')) echo '<td><b>'.$subname[$i].'</b></td>';
	else echo '<td>'.$subname[$i].'</td>';
}
echo '
                    </tr>';
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
		echo '<tr align="center">';
		for ($i=0;$i<$subnum;$i++) echo '<td>'.$subname[$i].'</td>';
		echo '</tr>';
	}
	echo '<tr align="center">';
	for($i=0;$i<$subnum;$i++){
		if ($subjectsort==$subname[$i] || ($sheet=='jinbu1' && $subname[$i]=='年变') || ($sheet=='jinbu2' && $subname[$i]=='班变')) echo '<td nowrap><b>'.$kemu[$subname[$i]].'</b></td>';
		else echo '<td nowrap>'.$kemu[$subname[$i]].'</td>';
	}
	echo '
                    </tr>';
	if ($sheet=='cjt') echo '</table><table><tr><td>&nbsp;</td></tr></table><table class="table table-bordered table-condensed table-hover">';
	if (!$tmp) $tmp='1';
}
echo '
                  </table>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
  </div>
';
$pagename=$sheetname;
