<?php
if ($usersort<5 || !$usermain) {Header("Location:".$web_root."/");exit();}
$infors=$mysqli->query("select * from phpcj_user where id=$userid");
$info=$infors->fetch_array();
if (!$info['username']) {echo "<script>window.location='".$web_root."/'</script>";exit();}
$sjzgrade=$info['usergrade'];
$sjzmain=$info['mainteacher'];
$sjzclass=$info['userclass'];
$gradeshow=str_replace('g','高中',$sjzgrade);
$gradeshow=str_replace('c','初中',$gradeshow);
$gradeshow.='级';
$gradeshow.=$sjzmain.'班';
$result=$mysqli->query("select * from cj_data where 现在='1' and 数据 like '".$sjzgrade."%'");
$data=$result->fetch_array();
$table=$data['数据'];
if (!$table) {echo "<script>window.location='".$web_root."/'</script>";exit();}
$wlsort=array('理'=>'0','文'=>'1');
if ($stusort) $subname=explode(",",explode(";",$data['科目'])[$wlsort[$stusort]]);
elseif ($data['文理']) $subname=explode(",",explode(";",$data['科目'])[0]);
else $subname=explode(",",$data['科目']);
$subname[]='总分';
$subnum=count($subname);
$smsrs=$mysqli->query("select sms_num from phpcj_sms where sms_class='$sjzmain' and sms_grade='$usergrade'");
$smsdata=$smsrs->fetch_array();
$smsnum=$smsdata['sms_num'];
$sturs=$mysqli->query("select * from $table where 班级='$sjzmain'");
$count=$sturs->num_rows;
echo '
  <div class="content-wrapper">
    <section class="content-header">
      <h1>'.$gradeshow.'最近一次考试学生成绩短信</h1>
    </section>
    <section class="content">
      <div class="row">
        <div class="col-lg-12">
          <div class="nav-tabs-custom">
            <ul class="nav nav-tabs">
              <li><a href="'.$web_root.'/?url=tea_stulist">学生资料</a>
              </li>';
if ($sjzgrade && $usersort>5) echo '
              <li><a href="'.$web_root.'/?url=tea_stulist&mygrade='.$sjzgrade.'">年级学生资料</a>
              </li>';
if ($sjzmain) echo '
              <li><a href="'.$web_root.'/?url=tea_smslist">学生账号</a>
              </li>';
if ($sjzgrade && $usersort>5) echo '
              <li><a href="'.$web_root.'/?url=tea_smslist&mygrade='.$sjzgrade.'">年级学生账号</a>
              </li>';
echo '
              <li class="active"><a href="#">成绩短信</a>
              </li>
            </ul>
            <div class="tab-content">
              <div class="tab-pane fade in active">
                <div class="table-responsive">
                  <div class="col-lg-12">
                    <h4>校讯通版（共'.$count.'人）：将下面成绩（含表头）复制到Excel表格中，保存后，直接上传到校讯通中即可。</h4>
                    <table class="table table-bordered table-condensed table-hover">
                      <tr>
                        <th style="text-align:center">班级代码</th>
                        <th style="text-align:center">姓名</th>';
for ($i=0;$i<$subnum;$i++) echo '
                        <th style="text-align:center">'.$subname[$i].'</th>';
echo '
                      </tr>';
$i=1;
while ($data=$sturs->fetch_array()) {
	echo '
                      <tr align="center"><td>'.$smsnum.'</td><td>'.$data['姓名'].'</td>';
	for ($i=0;$i<$subnum;$i++) echo '<td>'.$data[$subname[$i]].'</td>';
	echo '</tr>';
}
echo '
                    </table>
                    <h4>联通家校通版（共'.$count.'人）：先在家校通中添加相应科目，然后再将下面成绩（含表头）复制到Excel表格中，保存后，直接上传到家校通中。</h4>
                    <table class="table table-bordered table-condensed table-hover">
                      <tr>
                        <th style="text-align:center">学生/科目</th>';
for ($i=0;$i<$subnum;$i++) echo '
                        <th style="text-align:center">'.$subname[$i].'</th>';
echo '
                      </tr>';
$i=1;
$sturs->data_seek(0);
while ($data=$sturs->fetch_array()) {
	echo '
                      <tr align="center"><td>'.$data['姓名'].'</td>';
	for ($i=0;$i<$subnum;$i++) echo '<td>'.$data[$subname[$i]].'</td>';
	echo '</tr>';
}
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
$pagename='班级成绩短信';
