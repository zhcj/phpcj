<?php
if (!$userid) Header("Location:".$web_root."/?url=user_login");
$action=_post('action');
$oldpassword=_post('oldpassword');
$newpassword=_post('newpassword');
$newpassword1=_post('newpassword1');
if ($usersort) $data="phpcj_user";
else $data="phpcj_student";
$msg0='';
$msg1='';
$msg2='';
if ($action=='确定' && $userid && $oldpassword && $newpassword && $newpassword1) {
	if ($oldpassword && $newpassword) {
		$rowrs=$mysqli->query("select * from $data where id='$userid'");
		$row=$rowrs->fetch_array();
		if ($row['password']==md5($oldpassword) || ($row['password']==$oldpassword && !$row['usercheck'])) {
			if ($usersort!='9') $mysqli->query("update $data set password='".md5($newpassword)."',usercheck='1' where id=$userid");
			$_SESSION['sjzusercheck']='';
			echo "<script>window.location='".$web_root."/';</script>";
			exit();
		} else {
			$msg2='<br><font color=red>原密码错误！</font>';
		}
	} else $msg1 = '<br><font color=red>新旧密码均不能为空！</font>';
}
echo '
<script language=JavaScript>
function check(){if(document.changepass.oldpassword.value==""){alert("请输入原密码!");document.changepass.oldpassword.focus();return false;}if(document.changepass.newpassword.value==""){alert("请输入新密码!");document.changepass.newpassword.focus();return false;}if(document.changepass.newpassword.value==document.changepass.oldpassword.value){alert("原密码与新密码不能相同！");document.changepass.newpassword.focus();return false;}if(document.changepass.newpassword.value.length<4){alert("密码要求最少4个字符！");document.changepass.newpassword.focus();return false;}if(document.changepass.newpassword1.value==""){alert("请再次输入新密码!");document.changepass.newpassword1.focus();return false;}if(document.changepass.newpassword.value!=document.changepass.newpassword1.value){alert("两次输入的新密码不一致！");document.changepass.newpassword.focus();return false;}return true;}
</script>
  <div class="content-wrapper">
    <section class="content-header">
      <h1>修改密码</h1>
    </section>
    <section class="content">
      <div class="row">
        <div class="col-lg-12">
          <div class="box box-primary">
            <div class="box-header with-border">
              <h3 class="box-title">请输入原密码和新密码 <a class="btn btn-primary" href="'.$web_root.'/">返回首页</a></h3>
            </div>
            <div class="box-body">
              <div class="col-lg-4">
            ';
if ($usercheck) echo '	<font color="red">注意：本程序第一次登录，必须修改密码后才能使用！</font>
';
echo '
              <form action="'.$web_root.'/?url=user_password" role="form" method="post" name="changepass">
                <div class="form-group">
                  <label>原密码</label>
                  <input class="form-control" type="password" name="oldpassword">'.$msg2.'
                </div>
                <div class="form-group">
                  <label>新密码</label>
                  <input class="form-control" type="password" name="newpassword">
                </div>
                <div class="form-group">
                  <label>再输入</label>
                  <input class="form-control" type="password" name="newpassword1">
                </div>
                <input type="hidden" name="referer" value="'.$referer.'"><button type="submit" class="btn btn-primary" name="action" value="确定" onclick="return check()">确定</button>
              </form>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
  </div>
';
if ($msg0 || $msg1) echo $msg0.$msg1;
$pagename='修改密码';
