<?php
if (!$userid) Header("Location:".$web_root."/?url=user_login");
if (!$usersort) Header("Location:".$web_root."/");
$infors=$mysqli->query("select * from phpcj_user where id=$userid");
$info=$infors->fetch_array();
if (!$info['username']) {echo "<script>window.location='".$web_root."/'</script>";}
$userclass=$info['mainteacher'];
if (!$userclass) $userclass='否';
else $userclass.='班';
$usergrade=$info['usergrade'];
if ($usergrade) $usergrade=num2text($usergrade);
$logrs=$mysqli->query("select * from phpcj_userlog where username='".$info['username']."' order by id desc limit 1,2");
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
      <h1>用户信息</h1>
    </section>
    <section class="content">
      <div class="row">
        <div class="col-lg-12">
          <div class="box box-primary">
            <div class="box-header with-border">
              <h3 class="box-title"><b>'.$username.'</b> 的个人信息 <a class="btn btn-primary" href="'.$web_root.'/?url=user_password">修改密码</a></h3>
            </div>
            <div class="box-body">
              <div class="col-lg-6">
                <div class="table-responsive">
                  <table class="table table-bordered table-hover">
                    <tr>
                      <td>帐号</td>
                      <td>'.$info['username'].'</td>
                    </tr>
                    <tr>
                      <td>学科</td>
                      <td>'.$info['usersubject'].'</td>
                    </tr>
                    <tr>
                      <td>年级</td>
                      <td>'.$usergrade.'</td>
                  </tr>
                  <tr>
                    <td>班主任</td>
                    <td>'.$userclass.'</td>
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
                  </tr>
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
