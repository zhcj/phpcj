<?php
if ($usersort<5) {Header("Location:".$web_root."/");exit();}
$infors=$mysqli->query("select * from phpcj_user where id=$userid");
$info=$infors->fetch_array();
if (!$info['username']) {echo "<script>window.location='".$web_root."/'</script>";exit();}
$sjzgrade=$info['usergrade'];
$sjzmain=$info['mainteacher'];
$sjzclass=$info['userclass'];
$mygrade=_get('mygrade');
$gradeshow=str_replace('g','高中',$sjzgrade);
$gradeshow=str_replace('c','初中',$gradeshow);
$gradeshow.='级';
if ($sjzmain && $mygrade=='') {
	$result=$mysqli->query("select * from phpcj_student where usergrade='$sjzgrade' and userclass='$sjzmain' order by username");
	$gradeshow.=$sjzmain.'班';
} elseif (($sjzgrade && $usersort==6) || ($mygrade && $usersort==9)) {
	if ($mygrade!=$sjzgrade && $usersort!=9) $mygrade=$sjzgrade;
	$gradeshow=str_replace('g','高中',$mygrade);
	$gradeshow=str_replace('c','初中',$gradeshow);
	$gradeshow.='级';
	$result=$mysqli->query("select * from phpcj_student where usergrade='$mygrade' order by userclass+0,username");
} else {
	echo '<script>window.location.href="'.$web_root.'/";</script>';
	exit();
}
$count=$result->num_rows;
//if (!$count) {Header("Location:".$web_root."/");exit();} 
$gradeshow.='学生资料';
if ($mygrade) {$menu1='';$menu2=' class="active"';}
else {$menu1=' class="active"';$menu2='';}
echo '
<link href="'.$web_root.'/css/dataTables.bootstrap.css" rel="stylesheet">
<script src="'.$web_root.'/js/jquery.dataTables.min.js"></script>
<script src="'.$web_root.'/js/jquery.dataTables.zh.js"></script>
<script src="'.$web_root.'/js/dataTables.bootstrap.min.js"></script>
<script>
$(function(){
    $("#dataTable").dataTable({
        "columnDefs": [
            { "orderable": false, "targets": [2,5] }
        ]
    });
});
</script>
  <div class="content-wrapper">
    <section class="content-header">
      <h1>'.$gradeshow.'</h1>
    </section>
    <section class="content">
      <div class="row">
        <div class="col-lg-12">';
if (!$count) echo '<h4>无学生资料！</h4>';
else {
	echo '
          <div class="nav-tabs-custom">
            <ul class="nav nav-tabs">';
if ($sjzmain) echo '
              <li'.$menu1.'><a href="'.$web_root.'/?url=tea_stulist">学生资料</a></li>';
if ($sjzgrade && $usersort>5) echo '
              <li'.$menu2.'><a href="'.$web_root.'/?url=tea_stulist&mygrade='.$sjzgrade.'">年级学生资料</a></li>';
echo '
            </ul>
            <div class="tab-content">
              <div class="tab-pane fade in active">
                <div class="table-responsive">
                  <div class="col-lg-6">
                    <div style="padding:5px;margin-bottom:20px;border:1px solid transparent;border-radius:4px;background-color:#d2d6de;border-color:#d2d6de">
                      <b>学生账号：</b>';
if ($sjzmain) echo '<a class="btn btn-primary" href="'.$web_root.'/?url=smslist.xls&listsort=print" title="班级学生账号打印版Excel文件下载">打印版 <img src="images/xls.gif" height="18px"></a>';
if ($sjzgrade && $usersort>5) echo ' <a class="btn btn-primary" href="'.$web_root.'/?url=smslist.xls&listsort=print&grade=all" title="年级学生账号打印版Excel文件下载">年级 <img src="images/xls.gif" height="18px"></a>';
if ($sjzmain) echo ' <a class="btn btn-primary" href="'.$web_root.'/?url=smslist.xls" title="班级学生账号校讯通短信版Excel文件下载">校讯通短信版 <img src="images/xls.gif" height="18px"></a>';
if ($sjzgrade && $usersort>5) echo ' <a class="btn btn-primary" href="'.$web_root.'/?url=smslist.xls&grade=all" title="年级学生账号校讯通版Excel文件下载">年级 <img src="images/xls.gif" height="18px"></a>';
echo '
                    </div>
                  </div>
                  <div class="col-lg-6">
                    <div style="padding:5px;margin-bottom:20px;border:1px solid transparent;border-radius:4px;background-color:#d2d6de;border-color:#d2d6de">
                      <b>最近一次成绩短信：</b>';
if ($sjzmain) echo '<a class="btn btn-primary" href="'.$web_root.'/?url=smstable.xls" title="班级学生成绩校讯通版Excel文件下载">校讯通短信版 <img src="images/xls.gif" height="18px"></a>';
if ($sjzgrade && $usersort>5) echo ' <a class="btn btn-primary" href="'.$web_root.'/?url=smstable.xls&grade=all" title="年级学生成绩校讯通版Excel文件下载">年级 <img src="images/xls.gif" height="18px"></a>';
if ($sjzmain) echo ' <a class="btn btn-primary" href="'.$web_root.'/?url=smstable.xls&sms=jxt" title="班级学生成绩家校通版Excel文件下载">家校通版 <img src="images/xls.gif" height="18px"></a>';
echo '
                    </div>
                  </div>
                  <h4>说明：</h4>
                  <p>1、学生账号中的“打印版”，下载并打印出来，可以将<font color="red">学生账号</font>（不含已登录过的账号）在开家长会时发给家长；</p>
                  <p>2、学生账号中的“校讯通短信版”，下载后（由于生成的格式校讯通不支持，需要打开下载的Excel文件，点击一下保存即可），上传到校讯通中，可以将<font color="red">学生账号</font>（不含已登录过的账号）以短信形式发给家长；</p>
                  <p>3、最近一次成绩短信中的“校讯通短信版”，下载后（由于生成的格式校讯通不支持，需要打开下载的Excel文件，点击一下保存即可），上传到校讯通中，可以将<font color="red">学生成绩</font>以短信形式发给家长；</p>
                  <p>4、最近一次成绩短信中的“家校通版”，下载后，上传到家校通中，可以将<font color="red">学生成绩</font>以短信形式发给家长；</p>
                  <div class="col-lg-12">
                    <table class="table table-bordered table-condensed table-hover" id="dataTable">
                      <thead>
                      <tr>
                        <th style="text-align:center">序号</th>
                        <th style="text-align:center">班级</th>
                        <th style="text-align:center">姓名</th>
                        <th style="text-align:center">账号</th>
                        <th style="text-align:center">密码</th>
                        <th style="text-align:center">更多</th>
                      </tr>
                      </thead>
                      <tbody>';
$i=1;
while ($data=$result->fetch_array()) {
	if ($data['usercheck']=='1') $sjzpassword='已改密码 <a href="?url=tea_operate&action=reset&id='.$data['id'].'" onclick="return confirm(\'确实重置密码吗？\n注意：一旦重置将不可恢复！\n密码将重置为一个六位数字随机密码。\')">重置</a>';
	else $sjzpassword=$data['password'];
	$stu_grade=$data['usergrade'].'级';
	$stu_grade=str_replace('g','高中',$stu_grade);
	$stu_grade=str_replace('c','初中',$stu_grade);
	echo '
                      <tr align="center"><td>'.$i.'</td><td>'.$data['userclass'].'</td><td>'.$data['realname'].'</td><td>'.$data['username'].' <a href="'.$web_root.'/?url=tea_operate&action=del&id='.$data['id'].'" onclick="return confirm(\'确实删除此学生吗？\n注意：一旦删除将不可恢复！\')">删除</a></td><td>'.$sjzpassword.'</td><td><a href="'.$web_root.'/?url=stu_info&id='.$data['id'].'&from=tea">详细</a></td></tr>';
	$i=$i+1;
}
echo '
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
            </div>
          </div>';
}
echo '        </div>
      </div>
    </section>
  </div>
';
$pagename='学生信息管理';
