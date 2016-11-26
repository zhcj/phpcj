<?php
if ($usersort<6) Header("Location:".$web_root."/");
$id=_get('id');
$action=_get('action');
$newsweb=_get('newsweb');
if ($action=='on' && $id) {
	$mysqli->query("update phpcj_user set usercheck='0' where id='$id'");
	echo "<script>window.location='".$web_root."/?url=admin_teacher';</script>";
	exit();
} elseif ($action=='off' && $id) {
	$mysqli->query("update phpcj_user set password=username,usercheck='1' where id='$id'");
	echo "<script>window.location='".$web_root."/?url=admin_teacher';</script>";
	exit();
} elseif ($action=='reset' && $id) {
	$mysqli->query("update phpcj_user set password=username,usercheck='0' where id='$id'");
	echo "<script>window.location='".$web_root."/?url=admin_teacher';</script>";
	exit();
} elseif ($action=='up' && $id && $usersort==9) {
	$result=$mysqli->query("select * from phpcj_user where id=$id");
	$data=$result->fetch_array();
	if ($data['usersort']=='1') $mysqli->query("update phpcj_user set usersort='4' where id='$id'");
	elseif ($data['usersort']=='4') $mysqli->query("update phpcj_user set usersort='5' where id='$id'");
	elseif ($data['usersort']=='5') $mysqli->query("update phpcj_user set usersort='6' where id='$id'");
	echo "<script>window.location='".$web_root."/?url=admin_teacher';</script>";
	exit();
} elseif ($action=='down' && $id && $usersort==9) {
	$result=$mysqli->query("select * from phpcj_user where id=$id");
	$data=$result->fetch_array();
	if ($data['usersort']=='4') $mysqli->query("update phpcj_user set usersort='1' where id='$id'");
	elseif ($data['usersort']=='5') $mysqli->query("update phpcj_user set usersort='4' where id='$id'");
	elseif ($data['usersort']=='6') $mysqli->query("update phpcj_user set usersort='5' where id='$id'");
	echo "<script>window.location='".$web_root."/?url=admin_teacher';</script>";
	exit();
} elseif ($action=='sys_name' && $usersort=='9') {
	$sys_name=_get('sys_name');
	$mysqli->query("update cj_config set cj_about='".$sys_name."' where web_id='1'");
	echo "<script>window.location='".$web_root."/?url=admin_config';</script>";
	exit();
} elseif ($action=='web_status' && $usersort=='9') {
	$web_status=_get('web_status');
	$mysqli->query("update cj_config set web_status='".$web_status."' where web_id='1'");
	echo "<script>window.location='".$web_root."/?url=admin_config';</script>";
	exit();
} elseif ($action=='moditeacher') {
	$subnum=count($cj_subject);
	$update="update cj_teacher set ";
	for ($i=0;$i<$subnum;$i++) $update.=$cj_subject[$i]."='"._get(hz2py($cj_subject[$i]))."',";
	$update.="班主任='"._get('mainteacher')."' where id='".$id."'";
	$mysqli->query($update);
	echo "<script>window.location='".$web_root."/?url=tea_teacher&grade="._get('grade')."';</script>";
	exit();
} elseif ($usersort==9 && $action=='creat_stuid') {
//重置全部学生账号，慎用！
echo '
  <div id="page-wrapper">
    <div class="row">
      <div class="col-lg-12">
        <h2 class="page-header">生成学生账号</h2>
      </div>
    </div>
    <div class="row">';
//	$grade[]='c'.$cj_grade1;
//	$grade[]='c'.$cj_grade2;
//	$grade[]='c'.$cj_grade3;
//	$grade[]='g'.$cj_grade1;
//	$grade[]='g'.$cj_grade2;
//	$grade[]='g'.$cj_grade3;
	$gradecount=count($grade);
	if (!$gradecount) {echo '功能已禁用！需要开启，去掉注释即可。</div></div>';exit();}
	for ($i=0;$i<$gradecount;$i++) {
		$result=$mysqli->query("select * from cj_data where 现在='1' and 数据 like '".$grade[$i]."%'");
		$data=$result->fetch_array();
		$sturs=$mysqli->query("select * from ".$data['数据']." order by 班级+1,convert(姓名 using gb2312) asc");
		$j=1;
		while ($studata=$sturs->fetch_array()) {
			if ($clatmp!=$studata['班级']) $j=1;
			$stuname=$grade[$i].sprintf("%02d",$studata['班级']).sprintf("%02d",$j);
			$stupassword=rand(100000,999999);
			$mysqli->query("insert into phpcj_student (username,realname,password,usergrade,userclass) values ('$stuname','".$studata['姓名']."','$stupassword','".$grade[$i]."','".$studata['班级']."')");
			$clatmp=$studata['班级'];
			echo $stupassword.$studata['姓名'].$stuname.'<br>';
			$j=$j+1;
		}
	}
	echo '
    </div>
  </div>
';
	exit();
} elseif ($usersort==9 && $action=='getnews' && $newsweb) {
//	$webname=array('qxjyw'=>'桥西教育网','sjzjyj'=>'石家庄教育局','sjzjks'=>'石家庄教科所','xxnw'=>'学校内网');
	$result=$mysqli->query("select * from news_config where news_web='".$newsweb."'");
	while ($data=$result->fetch_array()) {
		$getall=file_get_contents($data['news_url']);
		if ($data['news_web']=='桥西教育网') {
			preg_match('/Part_list">(.+)<\/ul>/',$getall,$gettmp);
			$gettmp=preg_split('/<\/li><li>/',$gettmp[1]);
			$getnum=count($gettmp);
			for($i=0;$i<$getnum;$i++) {
				preg_match('/href=\"(.+)\"\starget/',$gettmp[$i],$geturl);
				$geturl=trim($geturl[1]);
				$geturl=str_replace('../','',$geturl);
				if (strstr($data['news_url'],'xxzq')) $geturl='xxzq/'.$geturl;
				$geturl='http://www.qxjy.net.cn/'.$geturl;
				$content=file_get_contents($geturl);
				preg_match('/><title>(.+)<\/title>/',$content,$title);
				$title=$title[1];
				if (strstr($title,'<')) {
					preg_match('/<[^>]*>(.+)<\/[^>]*>/',$title,$tmp);
					$title=$tmp[1];
				}
				preg_match('/<date>(.+)<\/date>/',$content,$posttime);
				$posttime=str_replace('.0','',$posttime[1]);
				$posttime=$posttime;
				preg_match('/<author>(.+)<\/author>/',$content,$post);
				$post=$post[1];
				$content=explode('con"><!--enpcontent-->',$content);
				$content=explode('<!--/enpcontent--><br><br></div>',$content[1]);
				$content=addslashes($content[0]);
				$content=trim($content);
				$tmprs=$mysqli->query("select count('news_id') from news_article where news_url='$geturl'");
				$countnum=$tmprs->num_rows;
				if (!$countnum && $title) {
					echo "insert into news_article (news_id,news_web,news_type,news_title,news_post,news_content,news_posttime,news_url) values (NULL,'".$data['news_web']."','".$data['news_type']."','$title','$post','$content','$posttime','$geturl')";
echo "<br>";
//					$mysqli->query("insert into news_article (news_id,news_web,news_type,news_title,news_post,news_content,news_posttime,news_url) values (NULL,'".$data['news_web']."','".$data['news_type']."','$title','$post','$content','$posttime','$geturl')");
				}
			}
		} elseif ($data['news_web']=='石家庄教科所') {
			$getall=explode('<ul>',$getall);
			$getall=explode('</ul>',$getall[3]);
			$gettmp=preg_split('/<\/li>/',$getall[0]);
			$getnum=count($gettmp)-1;
			for($i=0;$i<$getnum;$i++) {
				preg_match('/href=\"(.+)html\">/',$gettmp[$i],$geturl);
				$geturl=checkchar($geturl[1]).'html';
				$content=file_get_contents($geturl);
				preg_match('/<title>([\s\S]+)<\/title>/',$content,$title);
				$title=checkchar($title[1]);
				$content=explode('<div class="artTop">',$content);
				$content=explode('<div class="footer clearFloat">',$content[1]);
				$content=$content[0];
				preg_match('/<div>发布时间：(.+)点击数/',$content,$posttime);
				$posttime=checktime($posttime[1]);
				preg_match('/作者：(.+)<\/div>/',$content,$post);
				$post=checkchar($post[1]);
				$content=explode('<div class="artCont">',$content);
				$content=explode('</div>',$content[1]);
				$content=addslashes($content);
				$content=trim($content);
				$countnum=mysql_fetch_array(mysql_query("select count('news_id') from news_article where news_url='$geturl'"));
				if (!$countnum[0] && $posttime) {
					$mysqli->query("insert into news_article (news_id,news_web,news_type,news_title,news_post,news_content,news_posttime,news_url) values (NULL,'".$data['news_web']."','".$data['news_type']."','$title','$post','$content','$posttime','$geturl')");
				}
			}
		} elseif ($data['news_web']=='石家庄教育局') {
			$getall=explode('class="lumList">',$getall);
			$getall=explode('</ul>',$getall[1]);
			$gettmp=preg_split('/<\/li>/',$getall[0]);
			$getnum=count($gettmp)-1;
			for($i=0;$i<$getnum;$i++) {
				preg_match('/\"\shref=\"(.+)html\">/',$gettmp[$i],$geturl);
				$geturl=checkchar($geturl[1].'html');
				$content=file_get_contents($geturl);
				preg_match('/<title>([\s\S]+)<\/title>/',$content,$title);
				$title=checkchar($title[1]);
				$content=explode('<div class="arcT">',$content);
				$content=explode('<div class="arcB pabs">',$content[1]);
				$content=$content[0];
				preg_match('/日期：(.+)来源/',$content,$posttime);
				$posttime=checktime(str_replace('</span><span>','',$posttime[1]));
				preg_match('/来源：(.+)<\/span><span>/',$content,$post);
				$post=checkchar($post[1]);
				$content=explode('<div class="arcCont">',$content);
				$content=explode('</div>',$content[1]);
				$content=addslashes($content[0]);
				$content=trim($content);
				$countnum=mysql_fetch_array(mysql_query("select count('news_id') from news_article where news_url='$geturl'"));
				if (!$countnum[0] && $posttime) {
					$mysqli->query("insert into news_article (news_id,news_web,news_type,news_title,news_post,news_content,news_posttime,news_url) values (NULL,'".$data['news_web']."','".$data['news_type']."','$title','$post','$content','$posttime','$geturl')");
				}
			}
		}
	}
}
//if ($referer) Header("Location:.$referer");
exit();
echo "<script>window.location='./?url=admin_teacher';</script>";
//else Header("Location:./");
exit();
