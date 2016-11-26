<?php
if (!$userid) Header("Location:".$web_root."/");
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
        <div class="col-md-6">
          <div class="box box-primary">
            <div class="box-header with-border">
              <h3 class="box-title">最新公告</h3>
            </div>
            <div class="box-body">
              <ul>
                <li><p>[20160603]增加学生历次成绩和成绩分析表输出为Excel文件功能 <a class="btn btn-primary" href="'.$web_root.'/?url=cj_index">查看</a></p></li>';
if ($usersort>4) echo '
                <li><p>[20160601]班主任/年级主任增加查看学生访问记录功能！<a class="btn btn-primary" href="'.$web_root.'/?url=admin_userlog">查看</a></p></li>
                <li><p>[20160531]班主任/年级主任增加导出学生账号/成绩为Excel功能！<a class="btn btn-primary" href="'.$web_root.'/?url=tea_stulist">查看</a></p></li>';
echo '
                <li><p>[20160531]如有任何问题，可关注微信公众号，留言即可！<a class="btn btn-primary" href="'.$web_root.'/?url=message">关注方法</a></p></li>';
if ($usergrade=='g2013') echo '
                <li><p>[20160510]高三全部考试成绩已经导入！<a class="btn btn-primary" href="'.$web_root.'/?url=cj_index&classname=g2013">查看</a></p></li>';
if ($usergrade=='c2013') echo '
                <li><p>[20160509]初三质检二成绩已导入！由于文综和理综没分开，因此，成绩中的物理和历史分别代表理综和文综。<a class="btn btn-primary" href="'.$web_root.'/?url=cj_index&classname=c2013">查看</a></p></li>
                <li><p>[20160509]初三全部考试成绩已经导入，最后的质检一成绩由于文综和理综没分开，因此，成绩中的物理和历史分别代表理综和文综。<a class="btn btn-primary" href="'.$web_root.'/?url=cj_index&classname=c2013">查看</a></p></li>';
echo '
              </ul>
            </div>
          </div>
        </div>
        <div class="col-md-6">
          <div class="box box-primary">
            <div class="box-header with-border">
              <h3 class="box-title">已开发完成的功能</h3>
            </div>
            <div class="box-body" style="min-height:300px">
              <p><b>老师：</b></p>
              <ul>
                <li><p>分析和查询学生成绩（各种统计报表，如学生历次成绩、各班成绩单、分段统计、等效人数、学生进步情况、成绩条等） <a class="btn btn-primary" href="'.$web_root.'/?url=cj_index">去看看</a></p></li>
                <li><p>浏览各级教育部门的通知公告 <a class="btn btn-primary" href="'.$web_root.'/?url=news_index">去看看</a></p></li>
              </ul>
              <p><b>班主任（除以上功能外）：</b></p>
              <ul>
                <li><p>可以管理本班学生信息（删除、重置本班学生账号等）</p></li>
                <li><p>生成本班学生账号Excel表格，可以打印，也可以通过“和校园”给家长发送账号</p></li>
                <li><p>生成本班成绩短信Excel表格，方便通过“和校园”和“家校通”给家长发送成绩短信</p></li>
<!--
                <li><p>与学生进行信息交流</p></li>
-->
              </ul>
              <p><b>年级主任（除以上功能外）：</b></p>
              <ul>
                <li><p>可以管理本年级教师/学生信息（开通、重置本年级教师/学生账号等）</p></li>
                <li><p>生成本年级学生账号和成绩短信Excel表格，方便通过“和校园”给本年级家长发送</p></li>
<!--
                <li><p>上传并管理本年级学生成绩</p></li>
-->
              </ul>
              <p><b>学生：</b></p>
              <ul>
                <li><p>可以登录查询自己的历次成绩，并显示名次变化曲线图</p></li>
              </ul>
              更多功能，敬请期待 ... ...
            </div>
          </div>
        </div>
        <div class="col-md-6">
          <div class="box box-primary">
            <div class="box-header with-border">
              <h3 class="box-title">终端支持</h3>
            </div>
            <div class="box-header with-border">
              <ul>
                <li><p>为了有更好的显示效果，程序使用了最新的前端技术，因此程序<font color="red">不支持IE9以下版本浏览器</font>，建议使用火狐、谷歌及IE10及以上版本，或者直接用手机访问本站！</p></li>
                <li><p>各种移动端（如：手机、平板等），可以扫以下二维码关注微信公众号，点击菜单中的“测试地址”访问</p></li>
              </ul>
              <p align="center"><img src="images/qrcode.png" style="max-width:300px;width:100%;max-height:300px;"></p>
            </div>
          </div>
        </div>
      </div>
    </section>
  </div>
';
$pagename='控制面板';
