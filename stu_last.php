<?php
if ($usersort) Header("Location:./");
$infors=$mysqli->query("select * from phpcj_student where id=$userid");
$info=$infors->fetch_array();
if (!$info['username']) {echo "<script>window.location='".$web_root."/'</script>";}
$tmprs=$mysqli->query("select * from cj_data where 现在='1' and 数据 like '".$info['usergrade']."%'");
$tmpdata=$tmprs->fetch_array();
$table=$tmpdata['数据'];
$datars=$mysqli->query("select * from $table where 姓名='$username'");
$data=$datars->fetch_array();
$wlsort=array('理'=>'0','文'=>'1');
if ($data['类别']) {
	$tmpsub=explode(";",$tmpdata['科目']);
	if (strstr($data['类别'],'文')) $subname=explode(",",$tmpsub[$wlsort['文']]);
	elseif (strstr($data['类别'],'理')) $subname=explode(",",$tmpsub[$wlsort['理']]);
	unset($tmpsub);
} else $subname=explode(",",$tmpdata['科目']);
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
echo '
  <div class="content-wrapper">
    <section class="content-header">
      <h1>最近一次考试成绩</h1>
    </section>
    <section class="content">
      <div class="row">
        <div class="col-lg-12">
          <div class="nav-tabs-custom">
            <ul class="nav nav-tabs">
              <li class="active"><a href="#">最近</a></li>
              <li><a href="'.$web_root.'/?url=stu_all">历次</a></li>
            </ul>
            <div class="tab-content">
              <div class="tab-pane fade in active">
                <div class="table-responsive">
                  <div class="col-lg-6">
                    <h4>20'.substr($tmpdata['数据'],6,2).'年'.str_replace('0','',substr($table,-2,1)).substr($table,-1).'月的'.$tmpdata['考试'].'考试中，<b>'.$username.'</b> 同学的成绩如下：</h4>
                    <table class="table table-bordered table-hover">
                      <tr>
                        <th style="text-align:center">科目</th>
                        <th style="text-align:center">分数</th>
                        <th style="text-align:center">年级排名</th>
                        <th style="text-align:center">班级排名</th>
                      </tr>';
if ($data['姓名']) {
	for ($i=0;$i<$subnum;$i++) {
if (in_array($subname[$i],$cj_subject) || $subname[$i]=='总分') echo '
                      <tr align="center">
                        <td>'.$subname[$i].'</td>';
echo '
                        <td>'.$data[$subname[$i]].'</td>';
if (strstr($subname[$i],'序') || $subname[$i]=='班名')echo '
                      </tr>';
	}
} else echo '
                      <tr align="center">
                        <td colspan="4"><font color="red">最近一次考试无成绩，如有问题，请联系班主任！</font></td>
                      </tr>';
echo '
                    </table>
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
$pagename=$username.'同学成绩';
