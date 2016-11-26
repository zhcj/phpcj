<?php
$result=$mysqli->query("select * from cj_config");
$cj=$result->fetch_array();
$cj_schoolname=$cj['cj_schoolname'];
$cj_schoolweb=$cj['cj_schoolweb'];
$cj_year=$cj['cj_year'];
$cj_grade1=$cj_year;
$cj_grade2=$cj_year-1;
$cj_grade3=$cj_year-2;
$cj_level=$cj['cj_level'];
$cj_term=$cj['cj_term'];
$cj_subject=explode(',',$cj['cj_subject']);
$cj_testname=explode(',',$cj['cj_testname']);
$cj_itemtmp=$cj['cj_item'];
$cj_itemmoretmp=$cj['cj_itemmore'];
$sch_title=$cj['cj_about'];
$cj_gradename=explode(',',$cj['cj_gradename']);
$web_status=$cj['web_status'];
$web_root=$cj['web_root'];
//unset($cj);
function hz2py($cj){
	$cj=str_replace('语文','yw',$cj);
	$cj=str_replace('数学','sx',$cj);
	$cj=str_replace('英语','yy',$cj);
	$cj=str_replace('物理','wl',$cj);
	$cj=str_replace('化学','hx',$cj);
	$cj=str_replace('生物','sw',$cj);
	$cj=str_replace('历史','ls',$cj);
	$cj=str_replace('地理','dl',$cj);
	$cj=str_replace('政治','zz',$cj);
	$cj=str_replace('总分','zf',$cj);
	$cj=str_replace('文史','w',$cj);
	$cj=str_replace('理工','l',$cj);
	$cj=str_replace('文','w',$cj);
	$cj=str_replace('理','l',$cj);
	$cj=str_replace('高','g',$cj);
	$cj=str_replace('初','c',$cj);
	$cj=str_replace('一','1',$cj);
	$cj=str_replace('二','2',$cj);
	$cj=str_replace('三','3',$cj);
	return $cj;
}
function namedelnum($cj){
	$cj=str_replace('1','',$cj);
	$cj=str_replace('2','',$cj);
	$cj=str_replace('3','',$cj);
	$cj=str_replace('4','',$cj);
	$cj=str_replace('5','',$cj);
	$cj=str_replace('6','',$cj);
	$cj=str_replace('7','',$cj);
	$cj=str_replace('8','',$cj);
	$cj=str_replace('9','',$cj);
	$cj=str_replace('0','',$cj);
	return $cj;
}
function py2hz($cj){
	$cj=str_replace('yw','语文',$cj);
	$cj=str_replace('sx','数学',$cj);
	$cj=str_replace('yy','英语',$cj);
	$cj=str_replace('wl','物理',$cj);
	$cj=str_replace('hx','化学',$cj);
	$cj=str_replace('sw','生物',$cj);
	$cj=str_replace('ls','历史',$cj);
	$cj=str_replace('dl','地理',$cj);
	$cj=str_replace('zz','政治',$cj);
	$cj=str_replace('zf','总分',$cj);
	$cj=str_replace('w','文',$cj);
	$cj=str_replace('l','理',$cj);
	$cj=str_replace("g","高中",$cj);
	$cj=str_replace("c","初中",$cj);
	return $cj;
}
function postcheck($cj){
	$cj=str_replace('；',',',$cj);
	$cj=str_replace('\'',',',$cj);
	$cj=str_replace('"',',',$cj);
	$cj=str_replace('“',',',$cj);
	$cj=str_replace('“',',',$cj);
	$cj=str_replace('\.',',',$cj);
	$cj=str_replace('，',',',$cj);
	$cj=str_replace(';',',',$cj);
	$cj=str_replace('、',',',$cj);
	$cj=str_replace('。',',',$cj);
	$cj=str_replace('/',',',$cj);
	$cj=str_replace('-',',',$cj);
	$cj=str_replace('——',',',$cj);
	return $cj;
}
function num2text($cj){
	$cj=explode('_',$cj);
	$cjtext=$cj['0'].'级';
	if (count($cj)>1) {
		if ($cj['1']=='w') $cjtext.='文';
		if ($cj['1']=='l') $cjtext.='理';
		if (is_numeric($cj['1'])) $cjtext.=$cj['1'].'班';
	}
	$cjtext=str_replace("g","高中",$cjtext);
	$cjtext=str_replace("c","初中",$cjtext);
	return $cjtext;
}
function _get($cj){
     $val = !empty($_GET[$cj]) ? trim(htmlspecialchars($_GET[$cj])) : null;
     return $val;
}
function _post($cj){
     $val = !empty($_POST[$cj]) ? trim(htmlspecialchars($_POST[$cj])) : null;
     return $val;
}
function _session($cj){
     $val = !empty($_SESSION[$cj]) ? trim(htmlspecialchars($_SESSION[$cj])) : null;
     return $val;
}
function filename($cj){
	if (!strstr($_SERVER['HTTP_USER_AGENT'],'Linux')) $cj=iconv("UTF-8","GBK",$cj);
	return $cj;
}
function zifu($cj){
	if (strstr($_SERVER['HTTP_USER_AGENT'],'win')) $cj=iconv("UTF-8","GBK",$cj);
	return $cj;
}
function upfile(){
	$os=explode(' ',php_uname());
	if ($os['0']=='Linux') $cj="/tmp/phpcjtmp";
	else $cj='phpcjtmp';
	return $cj;
}	
function mobilecheck($cj) {
	$mobile_browser='0';
	if (preg_match('/(up.browser|up.link|mmp|symbian|smartphone|midp|wap|phone|iphone|ipad|ipod|android|xoom)/i', strtolower($cj))) $mobile_browser++;
	$mobile_ua=strtolower(substr($cj,0,4));
	$mobile_agents=array(
		'w3c ','acs-','alav','alca','amoi','audi','avan','benq','bird','blac',
		'blaz','brew','cell','cldc','cmd-','dang','doco','eric','hipt','inno',
		'ipaq','java','jigs','kddi','keji','leno','lg-c','lg-d','lg-g','lge-',
		'maui','maxo','midp','mits','mmef','mobi','mot-','moto','mwbp','nec-',
		'newt','noki','oper','palm','pana','pant','phil','play','port','prox',
		'qwap','sage','sams','sany','sch-','sec-','send','seri','sgh-','shar',
		'sie-','siem','smal','smar','sony','sph-','symb','t-mo','teli','tim-',
		'tosh','tsm-','upg1','upsi','vk-v','voda','wap-','wapa','wapi','wapp',
		'wapr','webc','winw','winw','xda','xda-'
	);   
	if (in_array($mobile_ua,$mobile_agents)) $mobile_browser++;
	if (strpos(strtolower($cj),'windows') !== false) $mobile_browser=0;
	if (strpos(strtolower($cj),'windows phone') !== false) $mobile_browser++;
	if ($mobile_browser>0) $cj='mobile';
	return $cj;
}
function xlsname($cj) {
	$ua = $_SERVER["HTTP_USER_AGENT"];
	$encoded_filename = urlencode($cj);
	$encoded_filename = str_replace("+", "%20", $encoded_filename);
	header('Content-Type: application/octet-stream');
	if (preg_match("/Firefox/", $ua)) {  
		$cj='Content-Disposition: attachment; filename*="utf8\'\''.$cj.'"';
	} else if (preg_match("/Windows NT 10/", $ua) || preg_match("/MSIE/", $ua)) {  
		$cj='Content-Disposition: attachment; filename="'.$encoded_filename.'"';
	} else {  
		$cj='Content-Disposition: attachment; filename="'.$cj.'"';
	}
	return $cj;
}
