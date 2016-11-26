<?php
if (!$userid) {Header("Location:".$web_root."/?url=user_login");exit();}
$id=_get('id');
$from=_get('from');
if (!$usersort) $id=$userid;
elseif ($usersort && !$id) {Header("Location:".$web_root."/");exit();}
$datars=$mysqli->query("select * from phpcj_student where id=$id");
$data=$datars->fetch_array();
if ($data['usercheck']=='1') $sjzpassword='已修改';
else $sjzpassword='未修改（还未登录过本系统）';
$stuname=$data['username'];
$stu_grade=$data['usergrade'].'级';
$stu_grade=str_replace('g','高中',$stu_grade);
$stu_grade=str_replace('c','初中',$stu_grade);
$stu_school=$data['userschool'];
if (!$stu_school) $stu_school='十九中学';
$stu_phone=$data['userphone'];
if (!$stu_phone) $stu_phone='暂无';
$stu_birth=$data['userbirth'];
if ($stu_birth=='0000-00-00') $stu_birth='暂无';
//$stu_idcard=$data['useridcard'];
$stu_photo=$web_root.'/picture/'.substr($stuname,0,5).'/'.substr($stuname,5,2).'/'.substr($stuname,7,2).'.jpg';
//if (file_exists(dirname(_FILE_).'/'.$stu_photo)) $stu_photo='<img src="'.$stu_photo.'">';
//else $stu_photo='<img src="'.$web_root.'picture/default.gif">';
$stu_photo='<img src="'.$web_root.'picture/default.gif">';
$logrs=$mysqli->query("select * from phpcj_userlog where username='$stuname' order by id desc limit 1,2");
$log=$logrs->fetch_array();
include("17monipdb/IP.class.php");
$lastip=new IP();
if ($log['username']) {
	$logintime=$log['logintime'];
	$loginip=$log['loginip'];
	if ($loginip=='127.0.0.1') $ipshow='内网';
	else {
		$ipshow=$lastip->find($loginip);
		$ipshow=$ipshow[0].$ipshow[1].$ipshow[2].$ipshow[3];
	}
} else {
	$logintime='无';
	$loginip='无';
	$ipshow='无';
}
echo '
  <div class="content-wrapper">
    <section class="content-header">
      <h1>';
if ($usersort) echo '学生';
echo '用户信息</h1>
    </section>
    <section class="content">
      <div class="row">
        <div class="col-lg-12">
          <div class="box box-primary">
            <div class="box-header with-border">
              <h3 class="box-title"><b>'.namedelnum($data['realname']).'</b> 同学的个人信息';
if ($usersort>4) echo ' <a class="btn btn-primary" href="'.$web_root.'/?url=tea_operate&action=del&id='.$data['id'].'" onclick="return confirm(\'确实删除此学生吗？\n注意：一旦删除将不可恢复！\')">删除此账号</a>';
elseif (!$usersort) echo ' <a class="btn btn-primary" href="'.$web_root.'/?url=user_password">修改密码</a>';
if ($from=='tea' && $usersort) echo ' <a class="btn btn-primary" href="'.$web_root.'/?url=tea_stulist">返回学生管理</a>';
echo '</h3>
            </div>
            <div class="box-body">
              <div class="col-lg-6">
                <div class="table-responsive">
                  <table class="table table-bordered table-hover">
                    <tr>
                      <td>帐号</td>
                      <td>'.$stuname;
echo '</td>
                    </tr>
';
if ($usersort) echo '
                    <tr>
                      <td>密码</td>
                      <td>'.$sjzpassword;
if ($usersort>4 && $data['usercheck']=='1') echo ' <a class="btn btn-primary" href="'.$web_root.'/?url=tea_operate&action=reset&id='.$id.'" onclick="return confirm(\'确实重置密码吗？\n注意：一旦重置将不可恢复！\n密码将重置为一个六位数字随机密码。\')">重置密码</a>';
echo '</td>
                    </tr>';
echo '
                    <tr>
                      <td>年级</td>
                      <td>'.$stu_grade.'</td>
                    </tr>
                    <tr>
                      <td>班级</td>
                      <td>'.$data['userclass'].'班</td>
                    </tr>
                    <tr>
                      <td>上次登录时间</td>
                      <td>'.$logintime.'</td>
                    </tr>
                    <tr>
                      <td>上次登录地点</td>
                      <td>'.$ipshow.'</td>
                    </tr>
                    <tr>
                      <td>上次登录IP</td>
                      <td>'.$loginip.'</td>
                    </tr>';
if ($usersort) echo '
                    <tr>
                      <td>学校标识码</td>
                      <td>'.$stu_school.'</td>
                    </tr>
                    <tr>
                      <td>联系电话</td>
                      <td>'.$stu_phone.'</td>
                    </tr>
                    <tr>
                      <td>生日</td>
                      <td>'.$stu_birth.'</td>
                    </tr>
                    <tr>
                      <td>照片</td>
                      <td>'.$stu_photo.'</td>
                    </tr>';
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
$pagename='用户信息';
if ($usersort) $pagename='学生'.$pagename;
