<?php
if ($usersort) Header("Location:./");
echo '
  <div class="content-wrapper">
    <section class="content-header">
      <h1>
        网络交流平台
        <small>欢迎页面</small>
      </h1>
    </section>
    <section class="content">
      <div class="row">
        <div class="col-lg-12">
          <div class="box box-primary">
            <div class="box-header with-border">
              <h3 class="box-title">'.namedelnum($username).' 同学，欢迎登录某某中学网络交流平台！</h3>
            </div>
            <div class="box-body">
              <h4>关于本系统：</h4>
              <ul>
                <li><p>石家庄市某某中学自主开发，用于家校交流的一个平台。</p></li>
                <li><p>登录后，可以查询： <a class="btn btn-primary" href="'.$web_root.'/?url=stu_last">最近一次考试成绩</a> 和 <a class="btn btn-primary" href="'.$web_root.'/?url=stu_all">历次考试成绩</a></p></li>
                <li><p>其他基本功能： <a class="btn btn-primary" href="'.$web_root.'/?url=stu_info">查看个人信息</a> 和 <a class="btn btn-primary" href="'.$web_root.'/?url=user_password">修改个人密码</a></p></li>
                <li><p>程序还在开发中，更多功能，敬请期待！</p></li>
                <li><p>使用过程中如果发现问题，请关注 <a class="btn btn-primary" href="'.$web_root.'/?url=message">微信公众号</a> ，直接留言即可！</p></li>
              </ul>
            </div>
          </div>
        </div>
      </div>
    </section>
  </div>
';
$pagename='控制面板';
