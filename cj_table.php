<?php
if (!$usersort) {Header("Location:".$web_root."/");exit();}
$id=_get('id');
$info=_get('info');
$gradetop=_get('gradetop');
$url=$web_root.'/?url=table.xls&id='.$id.'&gradetop='.$gradetop;
if ($gradetop && !ctype_digit($gradetop)) {
	echo "<script>window.location='".$web_root."/?url=cj_sheets';</script>";
	exit();
}
if ($info=='more') $info='more';
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
              <li class="active"><a href="'.$web_root.'/?url=cj_sheets&id='.$id.'">两率一分<i class="fa fa-times"></i></a></li>
            </ul>
            <div class="tab-content">
              <div class="tab-pane fade in active">
                <div class="table-responsive">
                  <div style="text-align:right;padding:5px;">';
if ($info=='more') echo '<a class="btn btn-info" href="'.$web_root.'/?url=cj_table&id='.$id.'">简表</a> ';
else echo '<a class="btn btn-info" href="'.$web_root.'/?url=cj_table&id='.$id.'&info=more">详表</a> ';
echo '<a class="btn btn-primary" href="'.$url.'">简表下载 <img src="images/xls.gif" height="18px"></a> <a class="btn btn-primary" href="'.$url.'&info=more">详表下载 <img src="images/xls.gif" height="18px"></a></div>
';
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
	echo '
                    <h4 align="center">'.$sheetname;
	if ($stusort[$o]) echo '—'.$stusort[$o].'科';
	echo '</h4>
                    <table class="table table-bordered table-condensed table-hover">
	              <tr align="center"><th></th><th>班级</th>';
	for ($i=0;$i<$subnum;$i++) echo '<th>'.$subname[$i].'('.$score[$subname[$i]].')</th>';
	echo '
                      </tr>';
	$allnum=count($allname);
	$clastart=0;
	if ($gradetop) $clastart=1;
	for ($i=$clastart;$i<$clanum;$i++) {
		for ($j=0;$j<$allnum;$j++) {
			if (($allname[$j]=='任课教师' && $claname[$i]=='校') || ($table2[$i]==1 && $allname[$j]=='任课教师') || ($gradetop && $allname[$j]=='最低分') || ($gradetop && $allname[$j]=='学困生')) echo '';
			else {
				if ($allname[$j]=='任课教师') echo '
                  <tr align="center"><td><b>'.$allname[$j].'</b></td><td></td>';
				elseif (!$j) echo '<tr align="center"><td><b>'.$allname[$j].'</b></td><td><b>'.$claname[$i].'</b></td>';
				else echo '<tr align="center"><td>'.$allname[$j].'</td><td></td>';
				for ($k=0;$k<$subnum;$k++) {
					if ($tableall[$o]['总分']['任课教师'][$claname[$i]] && $k==$subnum-1 && $allname=='任课教师')  echo '<td><font color="red">'.$tableall[$o]['总分']['任课教师'][$claname[$i]].'</font></td>';
					elseif ($subname[$k]=="班级" && !$j) echo '<td rowspan="'.$allnum.'"><b>'.$tableall[$o][$subname[$k]][$allname[$j]][$claname[$i]].'</b></td>';
					elseif ($allname[$j]=='平均分' && $i) echo '<td><b>'.$tableall[$o][$subname[$k]][$allname[$j]][$claname[$i]].'/'.$tableall[$o][$subname[$k]]['平序'][$claname[$i]].'</b></td>';
					elseif (($allname[$j]=='及格率' || $allname[$j]=='良好率' || $allname[$j]=='优秀率') && $k==$subnum) echo '<td></td>';
					elseif ($allname[$j]=='及格率' && $i) echo '<td>'.str_replace('.00','',$tableall[$o][$subname[$k]][$allname[$j]][$claname[$i]]).'%/'.$tableall[$o][$subname[$k]]['及序'][$claname[$i]].'</td>';
					elseif ($allname[$j]=='良好率' && $i) echo '<td>'.str_replace('.00','',$tableall[$o][$subname[$k]][$allname[$j]][$claname[$i]]).'%/'.$tableall[$o][$subname[$k]]['良序'][$claname[$i]].'</td>';
					elseif ($allname[$j]=='优秀率' && $i) echo '<td>'.str_replace('.00','',$tableall[$o][$subname[$k]][$allname[$j]][$claname[$i]]).'%/'.$tableall[$o][$subname[$k]]['优序'][$claname[$i]].'</td>';
					elseif (strpos($allname[$j],'率') && !$i) echo '<td>'.str_replace('.00','',$tableall[$o][$subname[$k]][$allname[$j]][$claname[$i]]).'%</td>';
					elseif ($tableall[$o][$subname[$k]][$allname[$j]][$claname[$i]]==$tableall[$o][$claname[$i]]['ban']) echo '<td><font color="red"><b>'.$tableall[$o][$subname[$k]][$allname[$j]][$claname[$i]].'</b></font></td>';
					elseif ($j==0 || $allname[$j]=='任课教师') echo '<td><b>'.$tableall[$o][$subname[$k]][$allname[$j]][$claname[$i]].'</b></td>';
					else echo '<td>'.$tableall[$o][$subname[$k]][$allname[$j]][$claname[$i]].'</td>';
				}
				echo '
                      </tr>';
			}
		}
	}
	echo '
                    </table>';
	unset($claname);
	unset($subname);
	unset($table1);
	unset($table2);
	unset($tmp1);
	unset($tmp2);
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
