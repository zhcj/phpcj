<?php
if ($usersort) Header("Location:./");
$infors=$mysqli->query("select * from phpcj_student where id=$userid");
$info=$infors->fetch_array();
$usergrade=$info['usergrade'];
if (!$info['username']) {echo "<script>window.location='".$web_root."/'</script>";}
if ($usergrade=='c2013') {
	$gratmp="年级='初三' and";
	$gratmp1='初三';
} elseif ($usergrade=='g2013') {
	$gratmp="年级='高三' and";
	$gratmp1='高三';
/**
} elseif ($usergrade=='g2014') {
	$gratmp="and 年级='高二'";
	$gratmp1='高二';
} elseif ($usergrade=='c2014') {
	$gratmp="and 年级='初二'";
	$gratmp1='初二';
**/
} else {
	$gratmp='';
	$gratmp1='';
}
$tmprs=$mysqli->query("select * from cj_data where $gratmp 数据 like '".$info['usergrade']."%'");
while ($tmpdata=$tmprs->fetch_array()) {
	$table[]=$tmpdata['数据'];
	$testgrade[]=$tmpdata['年级'];
	$testnametmp[]=substr($tmpdata['数据'],6).$tmpdata['年级'].$tmpdata['考试'];
	if ($tmpdata['现在']=='1') {
		$tmptable=$tmpdata['数据'];
		$tmpsort=$tmpdata['文理'];
		$tmpsub=$tmpdata['科目'];
		$tmpcla=$tmpdata['班级'];
	}
}
$testnumtmp=count($table);
$datars=$mysqli->query("select * from $tmptable where 姓名='$username'");
$data=$datars->fetch_array();
$wlsort=array('理'=>'0','文'=>'1');
if ($tmpsort) {
	$tmpsub=explode(";",$tmpsub);
	$tmpcla=explode(";",$tmpcla);
	if (in_array($info['userclass'],explode(",",$tmpcla[0]))) $subname=explode(",",$tmpsub[0]);
	elseif (in_array($info['userclass'],explode(",",$tmpcla[1]))) $subname=explode(",",$tmpsub[1]);
	unset($tmpsub);
	unset($tmpcla);
} else $subname=explode(",",$tmpsub);
$subnum=count($subname);
unset($tmpsub);
$tmpsub[]='总分';
$tmpsub[]='年名';
$tmpsub[]='班名';
for ($i=0;$i<$subnum;$i++) {
	$tmpsub[]=$subname[$i];
//	$tmpsub[]=substr($subname[$i],0,3)."排";
//	$tmpsub[]=substr($subname[$i],0,3)."序";
}
$subname=$tmpsub;
$subnum=count($subname);
$testnum=0;
for ($i=0;$i<$testnumtmp;$i++) {
	$result=$mysqli->query("select * from ".$table[$i]." where 姓名='".$username."' and 班级='".$info['userclass']."'");
	$alldata=$result->fetch_array();
	if ($alldata['姓名']) {
		for ($j=0;$j<$subnum;$j++) $stu[$testnum][$subname[$j]]=$alldata[$subname[$j]];
		$testnum++;
		$testname[]=$testnametmp[$i];
		$nianming[]=$alldata['年名'];
		$nianji[]=$testgrade[$i];
		$kaoshi[]=substr($table[$i],-4);
	}
}
if ($testnum>1) {
	$nianming=implode(';',$nianming);
	$nianji=implode(';',$nianji);
	$kaoshi=implode(';',$kaoshi);
}
echo '
  <div class="content-wrapper">
    <section class="content-header">
      <h1>历次成绩</h1>
    </section>
    <section class="content">
      <div class="row">
        <div class="col-lg-12">
          <div class="nav-tabs-custom">
            <ul class="nav nav-tabs">
              <li><a href="'.$web_root.'/?url=stu_last">最近</a></li>
              <li class="active"><a href="#">历次</a></li>
            </ul>
            <div class="tab-content">
              <div class="tab-pane fade in active">
                <div class="table-responsive">
                  <div class="col-lg-12">
                    <h4><b>'.namedelnum($username).'</b> 同学'.$gratmp1.'历次成绩如下：</h4>
                    <table class="table table-bordered table-condensed table-hover">
                      <tr>
                        <th style="text-align:center">考试</th>';
for ($i=0;$i<$subnum;$i++) {
	if ($subname[$i]=='年名') echo '
                        <th style="text-align:center">年级名次</th>';
	elseif ($subname[$i]=='班名') echo '
                        <th style="text-align:center">班级名次</th>';
	else echo '
                        <th style="text-align:center">'.$subname[$i].'</th>';
}
echo '
                      </tr>';
for ($i=0;$i<$testnum;$i++) {
	echo '
                      <tr align="center">
                        <td>'.$testname[$i].'</td>';
	for ($j=0;$j<$subnum;$j++) echo '
                        <td>'.$stu[$i][$subname[$j]].'</td>';
	echo '
                      </tr>';
}
if (!$testnum) echo '
                      <tr align="center">
                        <td colspan="'.($subnum+1).'"><font color="red">暂无考试成绩，如有问题，请联系班主任！</font></td>
                      </tr>';
echo '
                    </table>';
if ($testnum>1)	echo '
                    <div>
                    <h4><b>年级名次</b> 变化曲线图：</h4>
                      <p align="center"><img alt="年级名次变化曲线图" src="cj_line.php?mingci='.$nianming.'&kaoshi='.$kaoshi.'&nianji='.$nianji.'" style="max-width:490px;width:100%;max-height:285px;height:100%"></p>
                    </div>';
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
';
$pagename=namedelnum($username).'同学成绩';
