<?php
if (!$usersort) {Header("Location:".$web_root."/");exit();}
$id=_get('id');
$fd=_get('fd');
$fdnum=_get('fdnum');
$stusort=_get('getstusort');
$fdnum=str_replace('，',';',$fdnum);
$fdnum=str_replace(',',';',$fdnum);
$fdnum=str_replace('；',';',$fdnum);
$url=$web_root.'/?url=dxf.xls&id='.$id.'&fd='.$fd.'&getstusort='.$stusort.'&fdnum='.$fdnum;
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
echo '
  <div class="content-wrapper">
    <section class="content-header">
      <h1>'.$sheetname.'</h1>
    </section>
    <section class="content">
      <div class="row">
        <div class="col-lg-12">
          <div class="nav-tabs-custom">
            <ul class="nav nav-tabs">
              <li><a href="'.$web_root.'/?url=cj_index">历次</a></li>
              <li><a href="'.$web_root.'/?url=cj_sheets&id='.$id.'&fd='.$fd.'&getstusort='.$stusort.'">统计</a></li>
              <li class="active"><a href="'.$web_root.'/?url=cj_sheets&id='.$id.'&fd='.$fd.'&getstusort='.$stusort.'">等效人数<i class="fa fa-times"></i></a>
              </li>
            </ul>
            <div class="tab-content">
              <div class="tab-pane fade in active">
                <div class="table-responsive">
                  <div style="text-align:right;padding:5px;"><a class="btn btn-primary" href="'.$url.'">下载 <img src="images/xls.gif" height="18px"></a></div>
                  <h4 align="center">'.$sheetname.'</h4>';
for ($i=0;$i<$times;$i++) {
        if ($fd=='mc') echo '
           <p>按名次统计前'.$fdnum[$i].'名的等效分和等效人数</p>';
	elseif ($fd=='fs') echo '
           <p>按分数统计'.$fdfs[$i].'分及以上的等效分和等效人数</p>';
	echo '
                  <table class="table table-bordered table-condensed table-hover">
                    <tr align="center"><td>班级</td>';
	for ($j=0;$j<$subnum;$j++) echo '<td>'.$subname[$j].'('.$dxftable[$i][$fdnum[$i]][$subname[$j]].')</td>';
	echo '</tr>';
	for ($k=0;$k<$clanum;$k++) {
		if ($claname[$k]=='合计') echo '<tr align="center"><td>'.$claname[$k].'</td>';
		else echo '<tr align="center"><td>'.$claname[$k].'班</td>';
		for ($j=0;$j<$subnum;$j++) {
			echo '<td>';
			if ($dxftable[$i][$claname[$k]][$fdnum[$i]][$subname[$j]]) echo $dxftable[$i][$claname[$k]][$fdnum[$i]][$subname[$j]];
			echo '</td>';
		}
		echo '
                    </tr>';
	}
echo '
                  </table>';
}
echo '
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
