<?php
if ($usersort) Header("Location:./");
$info=mysql_fetch_array(mysql_query("select * from phpcj_student where id=$userid"));
if (!$info['username']) {header('Content-Type: text/html; charset=UTF-8');echo "<script language=JavaScript>alert(\"程序错误，查无此人！\");window.location='../?url=user_logout'</script>";}
//$usergrade=$info['usergrade'].'级';
//$usergrade=str_replace('g','高中',$usergrade);
//$usergrade=str_replace('c','初中',$usergrade);
$userclass=$info['userclass'];
$tmprs=mysql_query("select * from cj_data where 数据 like '".$info['usergrade']."%' order by 数据");
while($tmpdata=mysql_fetch_array($tmprs)) {
	if ($tmpdata) {
		$datalist[]=$tmpdata['数据'];
		$testnamelist[]=$tmpdata['考试'];
		$gradelist[]=$tmpdata['年级'];
		$stusort[]=$tmpdata['类别'];
	}
}
$datanum=count($datalist);
echo '
  <div id="page-wrapper">
    <div class="row">
      <div class="col-lg-12">
        <h2 class="page-header">历次成绩</h2>
      </div>
      <!-- /.col-lg-12 -->
    </div>
    <!-- /.row -->
    <div class="row">
      <div class="col-lg-12">
        <div class="panel panel-primary">
';
for ($j=0;$j<$datanum;$j++) {
	unset($tmpsub);
	$table=$datalist[$j];
	$subname=subname($table,$cj_subject,$stusort[$j]);
	$subnum=count($subname);
	$tmpsub[]='总分';
	$tmpsub[]='年名';
	$tmpsub[]='班名';
	for ($i=0;$i<$subnum;$i++) {
		$tmpsub[]=$subname[$i];
		$tmpsub[]=substr($subname[$i],0,3)."排";
		$tmpsub[]=substr($subname[$i],0,3)."序";
	}
	$subname=$tmpsub;
	$subnum=count($subname);
	$data=mysql_fetch_array(mysql_query("select * from $table where 姓名='$username' and 班级='".$userclass."'"));



	echo '
          <div class="panel-heading">
            20'.substr($datalist[$j],6,2).'年'.substr($datalist[$j],8,2).'月的'.$tmpdata['考试'].'考试中，<b>'.$username.'</b> 同学的成绩如下：
          </div>
          <!-- /.panel-heading -->
          <table class="table table-striped table-bordered table-hover" id="dataTables-example">
            <tbody>
              <tr align="center">
                <td>科目</td>
                <td>分数</td>
                <td>年级排名</td>
                <td>班级排名</td>
              </tr>
';
	if ($data['姓名']) {
		for ($i=0;$i<$subnum;$i++) {
			if (in_array($subname[$i],$cj_subject) || $subname[$i]=='总分') echo '              <tr align="center">
                <td>'.$subname[$i].'</td>';
			echo '                <td>'.$data[$subname[$i]].'</td>';
if (strstr($subname[$i],'序') || $subname[$i]=='班名')echo '              </tr>
';
		}
		echo '
            </tbody>
          </table>
';
	}
}
echo '        </div>
      </div>
    </div>
';
$pagename=$username.'同学成绩';
