<?php
if (!$usersort) {echo "<script>window.location='".$web_root."/';</script>";exit();}
$id=_get('id');
$action=_get('action');
if (!$action) $action=_post('action');
if ($action=='qu') {
	$qu_sex=_post('qu_sex');
	if ($qu_sex!='1') $qu_sex='0';
	$qu_birth=_post('qu_birth');
	if ($qu_birth && (!ctype_digit($qu_birth) || strlen($qu_birth)<>6)) $wrong_birth='1';
	else $wrong_birth='';
	$qu_zw=_post('qu_zw');
	$qu_zw_time=_post('qu_zw_time');
	if ($qu_zw_time && (!ctype_digit($qu_zw_time) || strlen($qu_zw_time)<>6)) $wrong_zw_time='1';
	else $wrong_zw_time='';
	$qu_zc=_post('qu_zc');
	if ($qu_zc!='1' && $qu_zc!='2' && $qu_zc!='3') $qu_zc='0';
	$qu_school=_post('qu_school');
	$qu_school_time=_post('qu_school_time');
	if ($qu_school_time && (!ctype_digit($qu_school_time) || strlen($qu_school_time)<>6)) $wrong_school_time='1';
	else $wrong_school_time='';
	$qu_school_zy=_post('qu_school_zy');
	$qu_subject=_post('qu_subject');
	$qu_xd=_post('qu_xd');
	if ($qu_xd!='1') $qu_xd='0';
	$qu_jyzz=_post('qu_jyzz');
	if ($qu_jyzz!='1') $qu_jyzz='0';
	$qu_bkzz=_post('qu_bkzz');
	if ($qu_bkzz!='1') $qu_bkzz='0';
	$qu_mobile=_post('qu_mobile');
	$qu_email=_post('qu_email');
	$qu_weixin=_post('qu_weixin');
	if (!$wrong_birth && !$wrong_zw_time && !$wrong_school_time) {
		$mysqli->query("update phpcj_qu set qu_sex='$qu_sex',qu_birth='$qu_birth',qu_zw='$qu_zw',qu_zw_time='$qu_zw_time',qu_zc='$qu_zc',qu_school='$qu_school',qu_school_time='$qu_school_time',qu_school_zy='$qu_school_zy',qu_subject='$qu_subject',qu_xd='$qu_xd',qu_jyzz='$qu_jyzz',qu_bkzz='$qu_bkzz',qu_mobile='$qu_mobile',qu_email='$qu_email',qu_weixin='$qu_weixin' where qu_name='$username'");
		echo "<script>window.location='".$web_root."/?url=tea_qu&alert=ok';</script>";
	} else {
		echo "<script>window.location='".$web_root."/?url=tea_qu&error=1';</script>";
	}
} elseif ($action=='fkb') {
	$fkb_wenti=trim(htmlspecialchars(_post('fkb_wenti')));
	$fkb_pingjia=trim(htmlspecialchars(_post('fkb_pingjia')));
	if ($fkb_wenti && $fkb_pingjia) {
		$mysqli->query("update phpcj_fkb set fkb_wenti='$fkb_wenti',fkb_pingjia='$fkb_pingjia' where fkb_name='$username'");
		echo "<script>window.location='".$web_root."/?url=tea_fkb&alert=ok';</script>";
	} else {
		echo "<script>window.location='".$web_root."/?url=tea_fkb&error=1';</script>";
	}
} elseif ($action=='reset' && $id && $usersort>4) {
	$mysqli->query("update phpcj_student set password='".rand(100000,999999)."',usercheck='0' where id='$id'");
	echo "<script>window.location='".$web_root."/?url=tea_stulist';</script>";
} elseif ($action=='del' && $id && $usersort>4) {
	$mysqli->query("delete from phpcj_student where id='$id'");
	echo "<script>window.location='".$web_root."/?url=tea_stulist';</script>";
}
//if ($referer) echo "<script>window.location='".$referer."';</script>";
exit();
