<?php
if ($usersort<6) {Header("Location:./");exit();}
$gradeshow=str_replace('g','高中',$usergrade);
$gradeshow=str_replace('c','初中',$gradeshow);
if ($usersort==9) {
	$exe="select * from phpcj_user where usersort<>9";
	$gradeshow='全部教师';
} elseif ($usersort==6) {
	$exe="select * from phpcj_user where usersort<6 and usergrade='$usergrade'";
	$gradeshow.='级教师';
} else {
	Header("Location:./");
	exit();
}
$result=$mysqli->query($exe);
$count=$result->num_rows;
echo '
<link href="css/dataTables.bootstrap.css" rel="stylesheet">
<script src="js/jquery.dataTables.min.js"></script>
<script src="js/jquery.dataTables.zh.js"></script>
<script src="js/dataTables.bootstrap.min.js"></script>
<script>
  $(function () {
    $("#dataTable").dataTable({
        "columnDefs": [
            { "orderable": false, "targets": [1,2,3,6] }
        ]
    });
  });
</script>
  <div class="content-wrapper">
    <section class="content-header">
      <h1>教师用户管理</h1>
    </section>
    <section class="content">
      <div class="row">
        <div class="col-lg-12">
          <div class="box box-primary">
            <div class="box-header with-border">
              <h3 class="box-title">'.$gradeshow.'</b>（共'.$count.'人）</h3>
            </div>
            <div class="box-body">
              <div class="table-responsive">
                <div class="col-lg-12">
                  <table class="table table-bordered table-hover" id="dataTable">
                    <thead>
                    <tr>
                      <th>序号</th>
                      <th>登录名</th>
                      <th>姓名</th>
                      <th>密码</th>
                      <th>学科</th>
                      <th>年级</th>
                      <th>班级</th>
                      <th>班主任</th>
                      <th>权限</th>
                      <th>操作</th>
                    </tr>
                    </thead>
                    <tbody>';
$i=1;
while ($data=$result->fetch_array()) {
	$loguser=$data['username'];
//	if (strlen($loguser)==11) $loguser=substr($loguser,0,7).'****';
	if ($data['username']==$data['password'] && $data['usercheck']=='1') {$userstat='<a href="?url=admin_operate&action=on&id='.$data['id'].'">开通</a>';$sjzpassword='未修改';}
	elseif ($data['username']==$data['password']) {$userstat='已开通 <a href="?url=admin_operate&action=off&id='.$data['id'].'" onclick="return confirm(\'确实关闭此帐号吗？\n注意：一旦关闭密码将重置！\')">关闭</a>';$sjzpassword='未修改';}
	elseif ($data['username']!=$data['password'] && $data['usercheck']=='1') {$sjzpassword='已修改 <a href="?url=admin_operate&action=reset&id='.$data['id'].'" onclick="return confirm(\'确实重置密码吗？\n注意：一旦重置将不可恢复！\')">重置</a>';$userstat='已开通 <a href="?url=admin_operate&action=off&id='.$data['id'].'" onclick="return confirm(\'确实关闭此帐号吗？\n注意：一旦关闭密码将重置！\')">关闭</a>';}
	else {$sjzpassword='未修改';$userstat='已开通';}
	if ($data['mainteacher']) $mainteacher=$data['mainteacher'];
	else $mainteacher='';
	if ($data['usergrade']) $tea_grade=py2hz($data['usergrade']).'级';
	else $tea_grade='';
	$tea_sort=$data['usersort'];
	if ($tea_sort==9) $tea_sort='系统管理员';
	elseif ($tea_sort==6) {
		$tea_sort='年级管理员';
		if ($usersort==9) $tea_sort.=' <a href="'.$web_root.'/?url=admin_operate&action=down&id='.$data['id'].'" title="更改为班主任" onclick="return confirm(\'确实将此用户更改为班主任吗？\')"><i class="fa fa-long-arrow-down fa-fw"></i></a>';
	} elseif ($tea_sort==5) {
		$tea_sort='班主任';
		if ($usersort==9) $tea_sort.=' <a href="'.$web_root.'/?url=admin_operate&action=down&id='.$data['id'].'" title="更改为任课教师" onclick="return confirm(\'确实将此用户更改为任课教师吗？\')"><i class="fa fa-long-arrow-down fa-fw"></i></a> <a href="'.$web_root.'/?url=admin_operate&action=up&id='.$data['id'].'" title="更改为年级管理员" onclick="return confirm(\'确实将此用户更改为年级管理员吗？\')"><i class="fa fa-long-arrow-up fa-fw"></i></a>';
	} elseif ($tea_sort==4) {
		$tea_sort='任课教师';
		if ($usersort==9) $tea_sort.=' <a href="'.$web_root.'/?url=admin_operate&action=down&id='.$data['id'].'" title="更改为普通教师" onclick="return confirm(\'确实将此用户更改为普通教师吗？\')"><i class="fa fa-long-arrow-down fa-fw"></i></a> <a href="'.$web_root.'/?url=admin_operate&action=up&id='.$data['id'].'" title="更改为班主任" onclick="return confirm(\'确实将此用户更改为班主任吗？\')"><i class="fa fa-long-arrow-up fa-fw"></i></a>';
	} elseif ($tea_sort==1) {
		$tea_sort='教师';
		if ($usersort==9) $tea_sort.=' <a href="'.$web_root.'/?url=admin_operate&action=up&id='.$data['id'].'" title="更改为任课教师" onclick="return confirm(\'确实将此用户更改为任课教师吗？\')"><i class="fa fa-long-arrow-up fa-fw"></i></a>';
	} else $tea_sort='<font color="red">错误！</font>';
	echo '<tr><td>'.$i.'</td><td>'.$loguser.'</td><td>'.$data['realname'].'</td><td>'.$sjzpassword.'</td><td>'.$data['usersubject'].'</td><td>'.$tea_grade.'</td><td>'.$data['userclass'].'</td><td>'.$mainteacher.'</td><td>'.$tea_sort.'</td><td>'.$userstat.'</td></tr>';
	$i=$i+1;
}
echo '
                    <tbody>
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
$pagename='教师用户管理';
