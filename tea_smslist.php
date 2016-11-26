<?php
if ($usersort<5) {Header("Location:".$web_root."/");exit();}
$infors=$mysqli->query("select * from phpcj_user where id=$userid");
$info=$infors->fetch_array();
if (!$info['username']) {echo "<script>window.location='".$web_root."/'</script>";exit();}
$sjzgrade=$info['usergrade'];
$sjzmain=$info['mainteacher'];
$sjzclass=$info['userclass'];
$mygrade=_get('mygrade');
$listsort=_get('listsort');
$gradeshow=str_replace('g','高中',$sjzgrade);
$gradeshow=str_replace('c','初中',$gradeshow);
$gradeshow.='级';
if (!$listsort) $listsort='print';
if ($sjzmain && $mygrade=='') {
	$result=$mysqli->query("select * from phpcj_student where usergrade='$sjzgrade' and userclass='$sjzmain' order by username");
	$gradeshow.=$sjzmain.'班';
} elseif (($mygrade==$sjzgrade && $usersort==6) || ($mygrade && $usersort==9)) {
	$gradeshow=str_replace('g','高中',$mygrade);
	$gradeshow=str_replace('c','初中',$gradeshow);
	$gradeshow.='级';
	$result=$mysqli->query("select * from phpcj_student where usergrade='$mygrade' order by userclass+0,username");
} else {
	echo '<script>window.location.href="'.$web_root.'/";</script>';
	exit();
}
$count=$result->num_rows;
if (!$count) {echo '<script>window.location.href="'.$web_root.'/?url=tea_smslist";</script>';exit();}
$gradeshow.='学生网络查询成绩的帐号与密码';
if ($mygrade) {$menu1='';$menu2=' class="active"';}
else {$menu1=' class="active"';$menu2='';}
$smsrs=$mysqli->query("select sms_num from phpcj_sms where sms_class='$sjzmain' and sms_grade='$usergrade'");
$smsdata=$smsrs->fetch_array();
$smsnum=$smsdata['sms_num'];
echo '
  <div class="content-wrapper">
    <section class="content-header">
      <h1>'.$gradeshow.'</h1>
    </section>
    <section class="content">
      <div class="row">
        <div class="col-lg-12">
          <div class="nav-tabs-custom">
            <ul class="nav nav-tabs">
              <li><a href="'.$web_root.'/?url=tea_stulist">学生资料</a></li>';
if ($sjzgrade && $usersort>5) echo '
              <li><a href="'.$web_root.'/?url=tea_stulist&mygrade='.$sjzgrade.'">年级学生资料</a></li>';
if ($sjzmain) echo '
              <li'.$menu1.'><a href="'.$web_root.'/?url=tea_smslist&listsort='.$listsort.'">学生账号</a></li>';
if ($sjzgrade && $usersort>5) echo '
              <li'.$menu2.'><a href="'.$web_root.'/?url=tea_smslist&mygrade='.$sjzgrade.'&listsort='.$listsort.'">年级学生账号</a></li>';
echo '
              <li><a href="'.$web_root.'/?url=tea_smstable">成绩短信</a></li>
            </ul>
            <div class="tab-content">
              <div class="tab-pane fade in active">
                <div class="table-responsive">
                  <div class="col-lg-8">
                    <h4>学生账号';
echo '（共'.$count.'人）——';
if ($mygrade) $tmp='&mygrade='.$mygrade;
else $tmp='';
if ($listsort=='sms') echo '<a href="?url=tea_smslist&listsort=print'.$tmp.'">打印版</a> 校讯通短信版';
elseif ($listsort=='print') echo '打印版 <a href="?url=tea_smslist&listsort=sms'.$tmp.'">校讯通短信版</a>';
echo '</h4>下载Excel：<a href="/?url=smslist.xls&filetype=xls">xls</a> <a href="/?url=smslist.xls">xlsx</a>';
if ($listsort=='sms') {
	echo '
                    <table class="table table-bordered table-condensed table-hover">
                      <tr>
                        <th style="text-align:center">班级代码</th>
                        <th style="text-align:center">姓名</th>
                        <th style="text-align:center">网址</th>
                        <th style="text-align:center">账号</th>
                        <th style="text-align:center">密码</th>
                      </tr>';
}
$i=1;
while ($data=$result->fetch_array()) {
	$gradename=str_replace('g','高中',$data['usergrade']);
	$gradename=str_replace('c','初中',$gradename);
	$gradename.='级';
	if ($data['usercheck']) $passlist='已改';
	else $passlist=$data['password'];
	$smsid=$data['usergrade'].$data['userclass'];
	if ($listsort=='sms') echo '
                      <tr align="center"><td>'.$smsnum.'</td><td>'.$data['realname'].'</td><td>test.phpcj.net</td><td>'.$data['username'].'</td><td>'.$passlist.'</td></tr>';
	elseif ($listsort=='print') echo '<div style="height:30px;line-height:30px;"><!-- '.$gradename.$data['userclass'].'班 --!><b>'.$data['realname'].'</b> 账号：'.$data['username'].' 密码：'.$passlist.' 成绩查询网址：http://test.phpcj.net</div><div style="height:20px;"><br></div>';
	$i=$i+1;
}
if (!$listsort || $listsort=='sms') echo '
                    </table>';
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
$pagename='学生登录帐号和密码';
