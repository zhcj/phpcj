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
$result=$mysqli->query("select * from cj_teacher where 数据='$grade' and 现任='1' order by 班级+0");
$subname=$cj_subject;
$subnum=count($subname);
$g_name=array('c2015','c2014','c2013','g2015','g2014','g2013');
$g_value=array('初一','初二','初三','高一','高二','高三');
echo '
  <div class="content-wrapper">
    <section class="content-header">
      <h1>'.$gradename.'年级教师任课表</h1>
    </section>
    <section class="content">
      <div class="row">
        <div class="col-lg-12">
          <div class="nav-tabs-custom">
            <ul class="nav nav-tabs">';
for ($i=0;$i<6;$i++) {
	if ($g_name[$i]==$grade) $active=' class="active"';
	else $active='';
	echo '
              <li'.$active.'><a href="'.$web_root.'/?url=tea_teacher&grade='.$g_name[$i].'">'.$g_value[$i].'</a></li>';
}
echo '
            </ul>
            <div class="tab-content">
              <div class="tab-pane fade in active">
                <div class="table-responsive">
                  <div class="col-lg-12">
                    <h4 align="center">'.$gradename.'年级任课表</h4>
                    <table class="table table-bordered table-hover">
                      <tr align="center">
                        <th style="text-align:center">班级</th>
                        <th style="text-align:center">班主任</th>';
for ($i=0;$i<$subnum;$i++) echo '
                        <th style="text-align:center">'.$subname[$i].'</th>';
if ($usersort=='9' || ($usersort=='6' && $usergrade==$grade)) echo '<th style="text-align:center">操作</th>';
echo '
                      </tr>';
while ($data=$result->fetch_array()) {
	if ($id==$data['id'] && ($usersort=='9' || ($usersort=='6' && $usergrade==$grade))) echo '
                      <form action="'.$web_root.'/"><input type="hidden" name="url" value="admin_operate"><input type="hidden" name="action" value="moditeacher"><input type="hidden" name="id" value="'.$id.'"><input type="hidden" name="grade" value="'.$grade.'">';
	echo '
                      <tr align="center">
                        <td>'.$data['班级'].'</td>';
	if ($id==$data['id'] && ($usersort=='9' || ($usersort=='6' && $usergrade==$grade))) echo '
                        <td><input type="text" name="mainteacher" value="'.$data['班主任'].'" size="4"></td>';
	else echo '
                        <td><b>'.$data['班主任'].'</b></td>';
	for ($i=0;$i<$subnum;$i++) {
		if ($id==$data['id'] && ($usersort=='9' || ($usersort=='6' && $usergrade==$grade))) echo '
                        <td><input type="text" name="'.hz2py($subname[$i]).'" value="'.$data[$subname[$i]].'" size="4"></td>';
		else echo '
                        <td>'.$data[$subname[$i]].'</td>';
	}
	if ($id==$data['id'] && ($usersort=='9' || ($usersort=='6' && $usergrade==$grade))) echo '
                        <td><button type="submit" class="btn btn-primary">确定</button></td>';
	elseif ($usersort=='9' || ($usersort=='6' && $usergrade==$grade)) echo '<td><a href="'.$web_root.'/?url=tea_teacher&grade='.$grade.'&id='.$data['id'].'">编辑</a></td>';
        echo '
                      </tr>';
	if ($id==$data['id'] && ($usersort=='9' || ($usersort=='6' && $usergrade==$grade))) echo '
                      </form>';
}
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
$pagename='教师任课表';
