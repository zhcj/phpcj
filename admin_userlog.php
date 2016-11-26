<?php
if ($usersort<5) {Header("Location:./");exit();}
if ($usersort==9) $tmp='';
elseif ($usersort==6) $tmp="where username like '".$usergrade."%'";
elseif ($usersort==5) {
	if (strlen($usermain)==1) $tmpusername='0'.$usermain;
	else $tmpusername=$usermain;
	$tmp="where username like '".$usergrade.$tmpusername."%'";
}
$numrs=$mysqli->query("select * from phpcj_userlog $tmp");
$count=$numrs->num_rows;
$pagesize=10;
$allpage=ceil($count/$pagesize);
$page=_get('page');
if (!$page) $page=1;
elseif (!is_numeric($page) || $page<0 || $page>$allpage) {Header("Location:/");exit();}
$offset=$pagesize*($page-1);
$result=$mysqli->query("select * from phpcj_userlog $tmp order by id desc limit $offset,$pagesize");
$i=($page-1)*$pagesize+1;
include("17monipdb/IP.class.php");
$lastip=new IP();
echo '
  <div class="content-wrapper">
    <section class="content-header">
      <h1>学生访问记录</h1>
    </section>
    <section class="content">
      <div class="row">
        <div class="col-lg-12">
          <div class="box box-primary">
            <div class="box-header with-border">
              <h3 class="box-title">用户访问详情</h3>
            </div>
            <div class="box-body">
              <div class="table-responsive">
                <table class="table table-bordered table-hover">
                  <tr>
                    <th style="text-align:center">序号</th>
                    <th style="text-align:center">班级</th>
                    <th style="text-align:center">姓名</th>
                    <th style="text-align:center">登录时间</th>
                    <th style="text-align:center">登录地点</th>
                    <th style="text-align:center">使用终端</th>
                  </tr>';
while ($data=$result->fetch_array()) {
	$useragent=$data['useragent'];
	if (strstr($useragent,'MicroMessenger')) $useragent='手机(微信)';
	elseif (mobilecheck($useragent)=='mobile') $useragent='手机';
	else $useragent='电脑';
//	if (strlen($useragent)>=60) $useragent=substr($useragent,0,60).'...';
	$loginip=$data['loginip'];
	if ($loginip=='127.0.0.1') $ipshow='内网';
	else {
		$ipshow=$lastip->find($loginip);
		$ipshow=$ipshow[0].$ipshow[1].$ipshow[2].$ipshow[3];
	}
	if(substr($data['username'],0,1)=='c' ||substr($data['username'],0,1)=='g') {
		$tmpsort=substr($data['username'],0,7);
		$tmpsort=num2text(substr($data['username'],0,5).'_'.str_replace('0','',substr($tmpsort,-2,1)).substr($tmpsort,-1));
	}
	else $tmpsort='老师';
	echo '
                  <tr align="center"><td>'.$i.'</td><td>'.$tmpsort.'</td><td>'.$data['realname'].'</td><td>'.$data['logintime'].'</td><td>'.$ipshow.'</td><td><a title="'.$data['useragent'].'">'.$useragent.'</a></td></tr>';
	$i=$i+1;
}
if (!$count) echo '<tr align="center"><td colspan="6"><font color="red">暂无访问记录！</font></td></tr>';
echo '
                </table>
              </div>';
if ($page<5 || $allpage==5 || $allpage==6) $start=2;
elseif ($page>$allpage-5) $start=$allpage-5;
else $start=$page-2;
if ($page>$allpage-5 || $allpage==6) $stop=$allpage;
elseif ($page<5) $stop=7;
else $stop=$page+3;
$tmpurl="&url=admin_userlog";
echo '
              <center>
                <ul class="pagination">
';
if ($page==1) echo '<li class="disabled"><a>上一页</a></li> <li class="active"><a>1</a></li>';
else echo '<li><a href="./?page='.($page-1).$tmpurl.'">上一页</a></li> <li><a href="./?page=1'.$tmpurl.'">1</a></li>';
if ($page>4 && $allpage!=5 && $allpage!=6 && $allpage!=7) echo '<li class="disabled"><a>...</a></li>';
for ($i=$start;$i<$stop;$i++) {
	if ($i==$page) echo ' <li class="active"><a>'.$i.'</a></li> ';
	else echo ' <li><a href="./?page='.$i.$tmpurl.'">'.$i.'</a></li> ';
}
if ($page<$allpage-4 && $allpage!=6 && $allpage!=7) echo '<li class="disabled"><a>...</a></li>';
if ($allpage<=1) echo ' <li class="disabled"><a>下一页</a></li>';
elseif ($page==$allpage) echo ' <li class="active"><a>'.$allpage.'</a></li> <li class="disabled"><a>下一页</a></li>';
else echo '<li><a href="./?page='.$allpage.$tmpurl.'">'.$allpage.'</a></li> <li><a href="./?page='.($page+1).$tmpurl.'">下一页</a></li>';
echo '
                </ul>
              </center>
            </div>
          </div>
        </div>
      </div>
    </section>
  </div>';
$pagename='访问记录';
