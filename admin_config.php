<?php
if ($usersort<6) {Header("Location:".$web_root."/");exit();}
$grade=_get('grade');
$id=_get('id');
if (!$grade) $grade=$usergrade;
if (!$grade) $grade='c2015';
$gradename=str_replace("c","初",$grade);
$gradename=str_replace("g","高",$gradename);
$gradename=str_replace($cj_grade1,"一",$gradename);
$gradename=str_replace($cj_grade2,"二",$gradename);
$gradename=str_replace($cj_grade3,"三",$gradename);
$result=$mysqli->query("select * from cj_config");
$subname=$cj_subject;
$subnum=count($subname);
$g_name=array('c2015','c2014','c2013','g2015','g2014','g2013');
$g_value=array('初一','初二','初三','高一','高二','高三');
$configtab=_get('configtab');
$actived=' class="active"';
$sys_menu='';
$tea_menu='';
$cj_menu='';
$cjdata_menu='';
if ($configtab=='config' || !$configtab) $sys_menu=$actived;
elseif ($configtab=='teacher') $tea_menu=$actived;
elseif ($configtab=='cj') $cj_menu=$actived;
elseif ($configtab=='cjdata') $cjdata_menu=$actived;
echo '
  <div class="content-wrapper">
    <section class="content-header">
      <h1>程序设置</h1>
    </section>
    <section class="content">
      <div class="row">
        <div class="col-lg-12">
          <div class="nav-tabs-custom">
            <ul class="nav nav-tabs">
              <li'.$sys_menu.'><a href="'.$web_root.'/?url=admin_config">系统设置</a></li>
              <li'.$tea_menu.'><a href="'.$web_root.'/?url=admin_config&configtab=teacher">教师任课信息</a></li>
              <li'.$cj_menu.'><a href="'.$web_root.'/?url=admin_config&configtab=cj">成绩程序设置</a></li>
              <li'.$cjdata_menu.'><a href="'.$web_root.'/?url=admin_config&configtab=cjdata">已有成绩信息修改</a></li>
            </ul>
            <div class="tab-content">
              <div class="tab-pane fade in active">
                <div class="table-responsive">
                  <div class="col-lg-12">';
if (!$configtab || $configtab=='system') echo '
                  <h4 align="center">基本设置</h4>
                  <table class="table table-bordered table-hover">
                    <tr align="center">
                      <form action="'.$web_root.'/">
                      <td><input type="hidden" name="url" value="admin_operate"><input type="hidden" name="action" value="sys_name"><span style="height:34px;line-height:34px;">系统名称</span></td>
                      <td align="left"><input type="text" name="sys_name" value="'.$sch_title.'" class="form-control"></td>
                      <td><button type="submit" class="btn btn-primary">修改</button></td>
                      </form>
                    </tr>
                    <tr align="center">
                      <form action="'.$web_root.'/">
                      <td><input type="hidden" name="url" value="admin_operate"><input type="hidden" name="action" value="web_status"><span style="height:34px;line-height:34px;">网站维护</span></td>
                      <td align="left"><input type="text" name="web_status" value="'.$web_status.'" class="form-control"></td>
                      <td><button type="submit" class="btn btn-primary">修改</button></td>
                      </form>
                    </tr>';
elseif ($configtab=='cj') echo '
                  <h4 align="center">成绩模块设置</h4>
                  <table class="table table-bordered table-hover">
                    <tr align="center">
                      <form action="'.$web_root.'/">
                      <td><input type="hidden" name="url" value="admin_operate"><input type="hidden" name="action" value="sys_name">学期设置</td>
                      <td align="left"><input type="text" name="cjyear" value="'.$cj_year.'" size="4"> - '.($cj_year+1).'学年 第<input type=text name="cjterm" value="'.$cj_term.'" size="1">学期</td>
                      <td><button type="submit" class="btn btn-primary">修改</button></td>
                      </form>
                    </tr>
                    <tr align="center">
                      <form action="'.$web_root.'/">
                      <td><input type="hidden" name="url" value="admin_operate"><input type="hidden" name="action" value="sys_subject">科目设置</td>
                      <td align="left"><input type="text" name="cjsubject" value="'.$cj['cj_subject'].'" size="40"></td>
                      <td><button type="submit" class="btn btn-primary">修改</button></td>
                      </form>
                    </tr>
                    <tr align="center">
                      <form action="'.$web_root.'/">
                      <td><input type="hidden" name="url" value="admin_operate"><input type="hidden" name="action" value="sys_testname">考试名称</td>
                      <td align="left"><input type="text" name="cjtestname" value="'.$cj['cj_testname'].'" size="40"></td>
                      <td><button type="submit" class="btn btn-primary">修改</button></td>
                      </form>
                    </tr>
                    <tr align="center">
                      <form action="'.$web_root.'/">
                      <td><input type="hidden" name="url" value="admin_operate"><input type="hidden" name="action" value="sys_cjitemmort">统计项目</td>
                      <td align="left"><input type="text" name="cjitemmore" value="'.$cj['cj_itemmore'].'" size="40" readonly> 详表</td>
                      <td></td>
                      </form>
                    </tr>
                    <tr align="center">
                      <form action="'.$web_root.'/">
                      <td><input type="hidden" name="url" value="admin_operate"><input type="hidden" name="action" value="sys_cjietm"></td>
                      <td align="left"><input type="text" name="cjitem" value="'.$cj['cj_item'].'" size="40"> 简表</td>
                      <td><button type="submit" class="btn btn-primary">修改</button></td>
                      </form>
                    </tr>
                    <tr align="center">
                      <form action="'.$web_root.'/">
                      <td><input type="hidden" name="url" value="admin_operate"><input type="hidden" name="action" value="sys_cjlevel">三率分值</td>
                      <td align="left"><input type="text" name="cjlevel" value="'.$cj['cj_level'].'" size="40"> 及格、良好、优秀占总分比重，0表示不统计此项</td>
                      <td><button type="submit" class="btn btn-primary">修改</button></td>
                      </form>
                    </tr>';
elseif ($configtab=='cjdata') echo '
                  <h4 align="center">已有成绩信息修改</h4>
                  <table class="table table-bordered table-hover">
                    <tr>
                      <th style="text-align:center">考试名称</th>
                      <th style="text-align:center">修改考试信息</th>
                      <th style="text-align:center">添加/修改任课教师信息</th>
                    </tr>
                    <tr align="center">
                      <form action="'.$web_root.'/">
                      <td><input type="hidden" name="url" value="admin_operate"><input type="hidden" name="action" value="sys_name">**考试</td>
                      <td align="left">数据</td>
                      <td><button type="submit" class="btn btn-primary">修改</button></td>
                      </form>
                    </tr>';
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
$pagename='系统设置';
