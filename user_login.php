<?php
$username=_post('username');
$password=_post('password');
$msg1='';
$msg2='';
$msg3='';
if ($username and $password) {
	if (substr($username,0,3)=='c20' || substr($username,0,3)=='g20') $exe="select * from phpcj_student where username='".$username."'";
	else $exe="select * from phpcj_user where username='".$username."'";
	$result=$mysqli->query($exe);
	$row=$result->fetch_array();
	if($row['username']) {
		if ($row['password']==md5($password) || (!$row['usercheck'] && $row['password']==$password)) {
			$_SESSION['sjzuserid']=$row['id'];
			if ($row['realname']) $_SESSION['sjzusername']=$row['realname'];
			else $_SESSION['sjzusername']=$row['username'];
			$_SESSION['sjzusersort']=$row['usersort'];
			if ($row['usersort']) {
				$_SESSION['sjzusergrade']=$row['usergrade'];
				$_SESSION['sjzusermain']=$row['mainteacher'];
				$_SESSION['sjzuserclass']=$row['userclass'];
			} else {
				$_SESSION['sjzusergrade']=$row['usergrade'];
			}
			if (!$row['usercheck']) $_SESSION['sjzusercheck']='chpwd';
			$mysqli->query("insert into phpcj_userlog (username,realname,loginip,useragent) values ('$username','".$row['realname']."','".$_SERVER["REMOTE_ADDR"]."','".$_SERVER['HTTP_USER_AGENT']."')");
			Header("Location:".$web_root."/");
			exit();
		} elseif ($row['password']==$password && $row['usercheck']==1) $msg2='<p><font color="red">帐号未开通，请<a href="'.$web_root.'/contactus.htm">联系</a>管理员！</font></p>';
		else {$msg2='<p><font color="red">密码错误！</font></p>';}
	} else {$msg1='<p><font color="red">无此用户！</font></p>';}
}?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
<link rel="shortcut icon" href="<?php echo $web_root;?>/favicon.ico">
<link rel="bookmark" href="<?php echo $web_root;?>/favicon.ico">
<link href="<?php echo $web_root;?>/css/bootstrap.min.css" rel="stylesheet">
<link href="<?php echo $web_root;?>/css/font-awesome.min.css" rel="stylesheet">
<link href="<?php echo $web_root;?>/css/form-elements.css" rel="stylesheet">
<link href="<?php echo $web_root;?>/css/login.css" rel="stylesheet">
<title>用户登录 - <?php echo $sch_title;?></title>
</head>
<!--[if lt IE 9]>
<div style="text-align:center;height:60px;line-height:60px;color:red;font-weight:bold;font-size:18px;">本程序不支持您所使用的浏览器，请升级到 IE 9 以上版本或使用<a href="http://www.firefox.com.cn/" target="_blank">火狐</a>、<a href="http://www.baidu.com/s?wd=chrome" target="_blank">谷歌</a>浏览器！也可以手机扫下面二维码浏览！</div>
<center><img src="images/qrcode.png"></center>
<script src="<?php echo $web_root;?>/js/html5shiv.js"></script>
<script src="<?php echo $web_root;?>/js/respond.min.js"></script>
<![endif]-->
<body">
<div class="top-content">
  <div class="inner-bg">
    <div class="container">
      <div class="row">
        <div class="col-sm-8 col-sm-offset-2 text">
          <h1>石家庄某某中学网络交流平台</h1>
        </div>
      </div>
      <div class="row">
        <div class="col-sm-6 col-sm-offset-3 form-box">
          <div class="form-top">
            <div class="form-top-left">
              <h3>系统登录</h3>
              <p>无账号或忘记密码？<button type="button" style="background-color:transparent;border:0;color:#3c8dbc;" data-toggle="modal" data-target="#myModal">看这里</button></p>
            </div>
            <div class="form-top-right">
              <i class="fa fa-key"></i>
            </div>
          </div>
          <div class="form-bottom">
            <form role="form" action="<?php echo $web_root?>/?url=user_login" method="post" class="login-form" name="login">
              <div class="form-group">
                <label class="sr-only" for="form-username">账号</label>
                <input name="username" placeholder="账号" class="form-username form-control" id="form-username" type="text" value="<?=$username?>"><?php echo $msg1.$msg3;?>
              </div>
              <div class="form-group">
                <label class="sr-only" for="form-password">密码</label>
                <input name="password" placeholder="密码" class="form-password form-control" id="form-password" type="password"> <?php echo $msg2?>
              </div>
<!--            <div class="checkbox">
              <label>
                <input name="remember" type="checkbox" value="Remember Me">记住我
              </label>
            </div>-->
              <input type="hidden" name="referer" value="<?php echo $referer?>"><button type="submit" class="btn btn-primary">登录</button>
            </form>
            <div style="height:40px;padding:20px">
              相关链接：<a href="http://phpcj.net/">程序主页</a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div id="footer">
    Copyright &copy; 2016 石家庄市第某某中学 <script type="text/javascript">var cnzz_protocol = (("https:" == document.location.protocol) ? " https://" : " http://");document.write(unescape("%3Cspan id='cnzz_stat_icon_5706001'%3E%3C/span%3E%3Cscript src='" + cnzz_protocol + "s9.cnzz.com/stat.php%3Fid%3D5706001%26show%3Dpic1' type='text/javascript'%3E%3C/script%3E"));</script>
  </div>
</div>
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">学生无账号或忘记密码</h4>
      </div>
      <div class="modal-body">
        <div align="left">
        <ul>
          <li>
            <p>方式一：<font color="red"><b>联系班主任</b></font>，获取账号或密码！</p>
          </li>
          <li><p>微信公众号：手机扫码，也可以在微信中搜索“中学成绩管理系统”或”phpcj_net“，关注即可。</p></li>
        </ul>
          <p align="center"><img src="images/weixin.png" style="max-width:200px;width:100%;max-height:200px;"></p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
      </div>
    </div>
  </div>
</div>
<script src="<?php echo $web_root;?>/js/jquery.min.js"></script>
<script src="<?php echo $web_root;?>/js/bootstrap.min.js"></script>
<script src="<?php echo $web_root;?>/js/jquery.backstretch.min.js"></script>
<script>
jQuery(document).ready(function() {
    $.backstretch("<?php echo $web_root;?>/images/background.jpg");
    $('.login-form input[type="text"], .login-form input[type="password"], .login-form textarea').on('focus', function() {
    	$(this).removeClass('input-error');
    });
    $('.login-form').on('submit', function(e) {
    	$(this).find('input[type="text"], input[type="password"], textarea').each(function(){
    		if( $(this).val() == "" ) {
    			e.preventDefault();
    			$(this).addClass('input-error');
    		}
    		else {
    			$(this).removeClass('input-error');
    		}
    	});
    });
});
</script>
<!--[if lt IE 10]>
<script>
$(document).ready(function(){
	$(".form-username").val("账号");
	$(".form-password").val("密码");
});
</script>
<![endif]-->
</body>
</html>
