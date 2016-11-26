<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
<link rel="shortcut icon" href="<?php echo $web_root;?>/favicon.ico">
<link rel="bookmark" href="<?php echo $web_root;?>/favicon.ico">
<link href="<?php echo $web_root;?>/css/bootstrap.min.css" rel="stylesheet">
<link href="<?php echo $web_root;?>/css/font-awesome.min.css" rel="stylesheet">
<link href="<?php echo $web_root;?>/css/AdminLTE.min.css" rel="stylesheet">
<link href="<?php echo $web_root;?>/css/ionicons.min.css" rel="stylesheet">
<link href="<?php echo $web_root;?>/css/skins/_all-skins.min.css" rel="stylesheet">
</head>
<!--[if lt IE 9]>
<div style="text-align:center;height:60px;line-height:60px;color:red;font-weight:bold;font-size:18px;">本程序不支持您所使用的浏览器，请升级到 IE 9 以上版本或使用<a href="http://www.firefox.com.cn/" target="_blank">火狐</a>、<a href="http://www.baidu.com/s?wd=chrome" target="_blank">谷歌</a>浏览器！</div>
<script src="<?php echo $web_root;?>/js/html5shiv.js"></script>
<script src="<?php echo $web_root;?>/js/respond.min.js"></script>
<![endif]-->
<body class="hold-transition skin-blue sidebar-mini">
<script src="<?php echo $web_root;?>/js/jquery.min.js"></script>
<script src="<?php echo $web_root;?>/js/bootstrap.min.js"></script>
<script src="<?php echo $web_root;?>/js/app.min.js"></script>
<div class="wrapper">
  <header class="main-header">
    <a href="<?php echo $web_root;?>/" class="logo">
      <span class="logo-mini"><img src="<?php echo $web_root;?>/images/titlelogo.png" style="width:45px;height:45px;"></span>
      <span class="logo-lg"><b>某中学网络交流平台</b></span>
    </a>
    <nav class="navbar navbar-static-top">
      <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
        <span class="sr-only">切换导航</span>
      </a>
      <div class="navbar-custom-menu">
        <ul class="nav navbar-nav">
<?php if (strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') === false) echo '
          <li class="dropdown messages-menu">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
              <i class="fa fa-envelope-o"></i>
              <span class="label label-success">1</span>
            </a>
            <ul class="dropdown-menu">
              <li class="header">你有 1 条新消息</li>
              <li>
                <ul class="menu">
                  <li>
                    <a href="'.$web_root.'/?url=message">
                      <p>微信公众号正式开通了！</p>
                    </a>
                  </li>
                </ul>
              </li>
            </ul>
          </li>
';?>
          <li class="dropdown user user-menu">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
              <img src="<?php echo $web_root.$user_logo;?>" class="user-image" alt="用户头像">
              <span class="hidden-xs"><?php echo namedelnum($username);?></span>
            </a>
            <ul class="dropdown-menu">
              <li class="user-header">
                <img src="<?php echo $web_root.$user_logo;?>" class="img-circle" alt="用户头像">
                <p>
                  <?php echo namedelnum($username);?>
                  <small><?php
if ($usersort=='9') echo '级别：系统管理员';
elseif ($usersort=='6') echo '级别：年级管理员';
elseif ($usersort=='5') echo '级别：班主任';
elseif ($usersort=='4') echo '级别：普通教师';
else echo '级别：学生';
?></small>
                </p>
              </li>
              <li class="user-footer">
                <div class="pull-left">
                  <a href="<?php echo $web_root;?>/?url=user_info" class="btn btn-default btn-flat">个人信息</a>
                </div>
                <div class="pull-right">
                  <a href="<?php echo $web_root;?>/?url=user_logout" class="btn btn-default btn-flat">退出系统</a>
                </div>
              </li>
            </ul>
          </li>
        </ul>
      </div>
    </nav>
  </header>
  <aside class="main-sidebar">
    <section class="sidebar">
      <ul class="sidebar-menu">
        <li class="header">主菜单</li>
        <li>
          <a href="<?php echo $web_root;?>/"><i class="fa fa-dashboard"></i> <span>控制面板</span></a>
        </li>
<?php if (!$usersort) echo '        <li class="treeview">
          <a href="'.$web_root.'/?url=stu_last">
            <i class="fa fa-table fa-fw"></i>
            <span>考试成绩</span>
          </a>
';
else echo '        <li>
          <a href="'.$web_root.'/?url=cj_index"><i class="fa fa-table fa-fw"></i> <span>成绩分析</span></a>
        </li>
        <li>
          <a href="'.$web_root.'/?url=news_index"><i class="fa fa-files-o fa-fw"></i> <span>新闻公告</span></a>
        </li>
';
if ($usersort>5) echo '        <li>
          <a href="'.$web_root.'/?url=admin_teacher"><i class="fa fa-sitemap fa-fw"></i> <span>教师管理</span></a>
        </li>
        <li>
          <a href="'.$web_root.'/?url=tea_teacher"><i class="fa fa-th fa-fw"></i> <span>任课表</span></a>
        </li>
';
if ($usersort>4) echo '        <li>
          <a href="'.$web_root.'/?url=tea_stulist"><i class="fa fa-users fa-fw"></i> <span>学生管理</span></a>
        </li>
        <li>
          <a href="'.$web_root.'/?url=admin_userlog"><i class="fa fa-list fa-fw"></i> <span>访问记录</span></a>
        </li>
';
echo '        <li class="treeview">
          <a href="#">
            <i class="fa fa-user fa-fw"></i>
            <span>用户中心</span>
            <i class="fa fa-angle-left pull-right"></i>
          </a>
          <ul class="treeview-menu">
            <li><a href="'.$web_root.'/?url=user_info"><i class="fa fa-user fa-fw"></i> 用户信息</a></li>
            <li><a href="'.$web_root.'/?url=user_password"><i class="fa fa-key fa-fw"></i> 修改密码</a></li>
            <li><a href="'.$web_root.'/?url=user_logout"><i class="fa fa-sign-out fa-fw"></i> 退出系统</a></li>
          </ul>
        </li>
      </ul>
    </section>
  </aside>';
