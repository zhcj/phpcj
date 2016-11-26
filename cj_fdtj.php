<?php
if (!$usersort) {Header("Location:".$web_root."/");exit();}
$id=_get('id');
$subjectsort=_get('subjectsort');
$step=_get('step');
$fd=_get('fd');
$fdsort=_get('fdsort');
$fdnum=_get('fdnum');
$getstusort=_get('getstusort');
$url=$web_root.'/?url=fdtj.xls&id='.$id.'&fd='.$fd.'&subjectsort='.$subjectsort.'&fdsort='.$fdsort.'&step='.$step.'&getstusort='.$getstusort.'&fdnum='.$fdnum;
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
              <li><a href="'.$web_root.'/?url=cj_sheets&id='.$id.'&fd='.$fd.'&subjectsort='.$subjectsort.'&fdsort='.$fdsort.'&step='.$step.'&getstusort='.$getstusort.'">统计</a></li>
              <li class="active"><a href="'.$web_root.'/?url=cj_sheets&id='.$id.'&fd='.$fd.'&subjectsort='.$subjectsort.'&fdsort='.$fdsort.'&step='.$step.'&getstusort='.$getstusort.'">分段<i class="fa fa-times"></i></a></li>
            </ul>
            <div class="tab-content">
              <div class="tab-pane fade in active">
                <div class="table-responsive">
                  <div style="text-align:right;padding:5px;"><a class="btn btn-primary" href="'.$url.'">下载 <img src="images/xls.gif" height="18px"></a></div>
                  <h4 align="center">'.$sheetname.'</h4>
                  <p>'.$sheetname0.'</p>
                  <table class="table table-bordered table-condensed table-hover">
                    <tr align="center"><td>分数线</td><td>年级</td>';
for ($j=1;$j<$clanum;$j++) echo '<td>'.$claname[$j].'班</td>';
echo '</tr>';
for ($i=1;$i<=$times;$i++){
	$temp1='';
	$temp2='';
	echo '
                    <tr align="center"><td>';
	if ($i==$times-1) echo $fdnum[$i-1].'以下';
	elseif ($i==$times) echo '合计';
	elseif ($fdsort=='lj') echo ''.$fdnum[$i].'以上';
	elseif ($fdsort=='fd' && $i==1) echo $fdnum[$i].'以上';
//	elseif ($fdsort=='fd' && $i==1) echo $fdnum[$i].'-'.$fdnum[$i-1];
	elseif ($fdsort=='fd') echo $fdnum[$i].'-'.($fdnum[$i-1]-1);
	echo '</td>';
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
		echo '<td>';
		if ($i==$times || $i==$times-1) echo $ljsql['0'];
		elseif ($fdsort=='lj') echo $ljsql['0'];
		elseif ($fdsort=='fd') echo $fdsql['0'];
		echo '</td>';
	}
	echo '
                    </tr>';
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
