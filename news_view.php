<?php
if (!$usersort) Header("Location:".$web_root."/");
$id=_get('id');
$i=_get("i");
$result=$mysqli->query("select * from news_article where news_id='$id'");
$news=$result->fetch_array();
if (!$news) {echo "<script>alert('无此信息！');window.location='index.php';</script>";exit();}
$newsweb=$news['news_web'];
$newstype=$news['news_type'];
if ($newsweb=='学校内网') {
	if ($newstype=='校园公告') $urlid='xygg/'.substr($news["news_url"],51);
	if ($newstype=='校园新闻') $urlid='xyxw/'.substr($news["news_url"],46);
	$content=file_get_contents("../xxnw/".$urlid.".txt");
} elseif ($newsweb=='桥西教育网') {
	$content=substr($news["news_url"],47);
	$content=str_replace('.htm','.txt',$content);
	$content=file_get_contents("../qxjy/news/".$content);
} elseif ($newsweb=='石家庄教科所') {
	if ($newstype=='通知公告') $urlid='tzgg/'.substr($news["news_url"],33);
	$urlid=str_replace('.html','.txt',$urlid);
	$content=file_get_contents("../sjks/".$urlid);
} elseif ($newsweb=='石家庄教育局') {
	if ($newstype=='通知公告') $urlid='tzgg/'.substr($news["news_url"],34);
	$urlid=str_replace('.html','.txt',$urlid);
	$content=file_get_contents("../sjyj/".$urlid);
} elseif ($newsweb=='河北省教科所') {
	if ($newstype=='文件通知') $urlid='wjtz/'.substr($news["news_url"],61);
	$urlid.='.txt';
	$content=file_get_contents("../hbjks/".$urlid);
}
if (!$content) $content=str_replace(" ","<br>",$news["news_content"]);
$content=str_replace('<? ','< ?',$content);
$content=str_replace('?>','? >',$content);
$content=str_replace('<% ','< %',$content);
$content=str_replace('%>','% >',$content);
//if (!$i && $newsweb=='学校内网' && $newstype=='校园公告' && $ip!='127.0.0.1') $content='<p align="center"><font color="red">对不起，校园公告只能在学校局域网查看！</font><p>';
$select1='';
$select2='';
$menu0='';
$menu1='';
$menu1_1='';
$menu2='';
$menu2_1='';
$menu3='';
$menu3_1='';
$menu4='';
$menu4_1='';
$menu5='';
$menu5_1='';
if ($newsweb=='学校内网') $menu1_1=' class="active"';
elseif ($newsweb=='桥西教育网') $menu2_1=' class="active"';
elseif ($newsweb=='石家庄教育局') $menu3_1=' class="active"';
elseif ($newsweb=='石家庄教科所') $menu4_1=' class="active"';
elseif ($newsweb=='河北省教科所') $menu5_1=' class="active"';
$tabname=$newsweb.' >> '.$newstype;
echo '
  <div class="content-wrapper">
    <section class="content-header">
      <h1>'.$tabname.'</h1>
    </section>
    <section class="content">
      <div class="row">
        <div class="col-lg-12">
          <div class="nav-tabs-custom">
            <ul class="nav nav-tabs">
              <li'.$menu0.'><a href="'.$web_root.'/?url=news_index">全部</a></li>
              <li'.$menu1.'><a href="'.$web_root.'/?url=news_index&newsweb=学校内网">内网</a></li>';
if ($newsweb=='学校内网') echo '
              <li'.$menu1_1.'><a href="'.$web_root.'/?url=news_index&newsweb=学校内网&newstype='.$newstype.'">详情<i class="fa fa-times"></i></a></li>';
echo '
              <li'.$menu2.'><a href="'.$web_root.'/?url=news_index&newsweb=桥西教育网">桥西</a></li>';
if ($newsweb=='桥西教育网') echo '
              <li'.$menu2_1.'><a href="'.$web_root.'/?url=news_index&newsweb=桥西教育网&newstype='.$newstype.'">详情<i class="fa fa-times"></i></a></li>';
echo '
              <li'.$menu3.'><a href="'.$web_root.'/?url=news_index&newsweb=石家庄教育局">市局</a></li>';
if ($newsweb=='石家庄教育局') echo '
              <li'.$menu3_1.'><a href="'.$web_root.'/?url=news_index&newsweb=石家庄教育局&newstype='.$newstype.'">详情<i class="fa fa-times"></i></a></li>';
echo '
              <li'.$menu4.'><a href="'.$web_root.'/?url=news_index&newsweb=石家庄教科所">市科</a></li>';
if ($newsweb=='石家庄教科所') echo '
              <li'.$menu4_1.'><a href="'.$web_root.'/?url=news_index&newsweb=石家庄教科所&newstype='.$newstype.'">详情<i class="fa fa-times"></i></a></li>';
echo '
              <li'.$menu5.'><a href="'.$web_root.'/?url=news_index&newsweb=河北省教科所">省科</a></li>';
if ($newsweb=='河北省教科所') echo '
              <li'.$menu5_1.'><a href="'.$web_root.'/?url=news_index&newsweb=河北省教科所&newstype='.$newstype.'">详情<i class="fa fa-times"></i></a></li>';
echo '
            </ul>
            <div class="tab-content">
              <div class="tab-pane fade in active">
                <div class="table-responsive">
                  <div class="col-lg-12">
                    <form method="get" class="form-inline">
                      <div class="box-body">
                        <input type="hidden" name="url" value="news_index" class="form-control">
                      <div class="form-group">
                        <select name="searchtype" class="form-control"><option value="">标题+正文</option><option value="title"'.$select1.'>标题</option><option value="content"'.$select2.'>正文</option></select>
                      </div>
                      <div class="form-group">
                        <input name="keyword" value="" class="form-control">
                      </div>
                      <div class="form-group">
                        <button type="submit" value="搜索" class="btn btn-primary">搜索</button>
                      </div>
                    </div>
                    </form>
                    <div align="center" style="font-weight:bold;font-size: 20px;padding:10px;">'.$news["news_title"].'</div>
                    <div align="center" style="padding:10px;">信息来源：'.$news["news_post"].'&nbsp;&nbsp;发布时间：'.$news["news_posttime"].'</div>
                    <div style="padding:20px">'.$content.'</div>
                    <div style="padding:20px">原始链接：<a href="'.$news["news_url"].'" target="_blank">'.$news["news_url"].'</a></div>
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
$pagename=$news['news_title'];
