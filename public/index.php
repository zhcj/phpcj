<?php
session_start();
error_reporting(0);
include("../conni.php");
$url=_get('url');
if (!$url) $url=_post('url');
if ($url=='user_logout') {include('../user_logout.php');exit();}
$ip=$_SERVER["REMOTE_ADDR"];
$userid=_session('sjzuserid');
$username=_session('sjzusername');
$usercheck=_session('sjzusercheck');
$usersort=_session('sjzusersort');
$usergrade=_session('sjzusergrade');
$usermain=_session('sjzusermain');
$web_style=_get('style');
$referer=_post('referer');
//if (!$referer) $referer=$_SERVER["HTTP_REFERER"];
$referer=strstr($referer,'/?');
if (strstr($url,'xls')) {
	require_once('../PHPExcel.php');
	include("../".$url.".php");
	exit();
}
if ($usersort) $user_logo='/images/tea.jpg';
elseif (substr($usergrade,0,1)=='c') $user_logo='/images/stu_c.jpg';
elseif (substr($usergrade,0,1)=='g') $user_logo='/images/stu_g.jpg';
if (!$username) $url='user_login';
elseif ($usercheck) $url='user_password';
elseif (!$usersort && !$url) $url='stu_index';
elseif ($usersort && !$url) $url='user_index';
elseif ($url && !file_exists('../'.$url.'.php')) $url='wrong';
if ($url!='user_login') include("../header.php");
include("../".$url.".php");
if ($url!='user_login') include("../foot.php");
