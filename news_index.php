<?php
if (!$usersort) {Header("Location:".$web_root."/");exit();}
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
$action=_get('action');
$pagesize=_get('ps');
$newsweb=_get('newsweb');
$newstype=_get('newstype');
$keyword=_get('keyword');
$searchtype=_get('searchtype');
$searchname=str_replace('title','标题',$searchtype);
$searchname=str_replace('content','正文',$searchname);
if ($keyword && !$searchname) $searchname='标题+正文';
if ($keyword && $searchtype=='title') {$tmp="where news_title like '%$keyword%'";$select1=' selected';}
elseif ($keyword && $searchtype=='content') {$tmp="where news_content like '%$keyword%'";$select2=' selected';}
elseif ($keyword) $tmp="where news_title like '%$keyword%' or news_content like '%$keyword%'"; 
elseif ($newsweb && $newstype) $tmp="where news_web='".$newsweb."' and news_type='".$newstype."'";
elseif ($newsweb && !$newstype) $tmp="where news_web='".$newsweb."'";
elseif (!$newsweb && $newstype) $tmp="where news_type='".$newstype."'";
else $tmp='';
$numrs=$mysqli->query("select * from news_article $tmp");
$count=$numrs->num_rows;
if (!$pagesize) $pagesize=10;
$allpage=ceil($count/$pagesize);
$page=_get('page');
if (!$page) $page=1;
elseif (!is_numeric($page) || $page<0 || $page>$allpage) {Header("Location:".$web_root."/");exit();}
$offset=$pagesize*($page-1);
$result=$mysqli->query("select * from news_article $tmp order by news_posttime desc,news_id desc limit $offset,$pagesize");
$i=($page-1)*$pagesize+1;
$tabname=$newsweb;
if ($keyword) $tabname='搜索结果';
elseif (!$newsweb) {$menu0=' class="active"';$tabname='全部公告';}
elseif ($newsweb=='学校内网' && $newstype) $menu1_1=' class="active"';
elseif ($newsweb=='学校内网') $menu1=' class="active"';
elseif ($newsweb=='桥西教育网' && $newstype) $menu2_1=' class="active"';
elseif ($newsweb=='桥西教育网') $menu2=' class="active"';
elseif ($newsweb=='石家庄教育局' && $newstype) $menu3_1=' class="active"';
elseif ($newsweb=='石家庄教育局') $menu3=' class="active"';
elseif ($newsweb=='石家庄教科所' && $newstype) $menu4_1=' class="active"';
elseif ($newsweb=='石家庄教科所') $menu4=' class="active"';
elseif ($newsweb=='河北省教科所' && $newstype) $menu5_1=' class="active"';
elseif ($newsweb=='河北省教科所') $menu5=' class="active"';
if ($newstype) $tabname.=' >> '.$newstype;
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
if ($newsweb=='学校内网' && $newstype) echo '
              <li'.$menu1_1.'><a href="'.$web_root.'/?url=news_index&newsweb=学校内网">'.$newstype.'<i class="fa fa-times"></i></a></li>';
echo '
              <li'.$menu2.'><a href="'.$web_root.'/?url=news_index&newsweb=桥西教育网">桥西</a></li>';
if ($newsweb=='桥西教育网' && $newstype) echo '
              <li'.$menu2_1.'><a href="'.$web_root.'/?url=news_index&newsweb=桥西教育网">'.$newstype.'<i class="fa fa-times"></i></a></li>';
echo '
              <li'.$menu3.'><a href="'.$web_root.'/?url=news_index&newsweb=石家庄教育局">市局</a></li>';
if ($newsweb=='石家庄教育局' && $newstype) echo '
              <li'.$menu3_1.'><a href="'.$web_root.'/?url=news_index&newsweb=石家庄教育局">'.$newstype.'<i class="fa fa-times"></i></a></li>';
echo '
              <li'.$menu4.'><a href="'.$web_root.'/?url=news_index&newsweb=石家庄教科所">市科</a></li>';
if ($newsweb=='石家庄教科所' && $newstype) echo '
              <li'.$menu4_1.'><a href="'.$web_root.'/?url=news_index&newsweb=石家庄教科所">'.$newstype.'<i class="fa fa-times"></i></a></li>';
echo '
              <li'.$menu5.'><a href="'.$web_root.'/?url=news_index&newsweb=河北省教科所">省科</a></li>';
if ($newsweb=='河北省教科所' && $newstype) echo '
              <li'.$menu5_1.'><a href="'.$web_root.'/?url=news_index&newsweb=河北省教科所">'.$newstype.'<i class="fa fa-times"></i></a></li>';
if ($keyword) echo '
              <li class="active"><a href="#">搜索</a></li>';
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
                          <input name="keyword" value="'.$keyword.'" class="form-control">
                        </div>
                        <div class="form-group">
                          <button type="submit" value="搜索" class="btn btn-primary">搜索</button>
                        </div>
                        <div class="form-group">
                          共'.$count.'条记录
                        </div>
                      </div>
                    </form>';
if ($page) {
	echo '
                    <table class="table table-bordered table-hover">
                      <tr>
                        <th style="text-align:center">编号</th>
                        <th style="text-align:center">来源</th>
                        <th style="text-align:center">类别</th>
                        <th style="text-align:center">标题</th>
                        <th style="text-align:center">发布</th>
                        <th style="text-align:center">时间</th>
                        <th style="text-align:center"></th>
                      </tr>';
	while ($news=$result->fetch_array()) {
		echo '
                      <tr align="center"><td>'.$i.'</td><td><a href="./?url=news_index&newsweb='.$news["news_web"].'">'.$news["news_web"].'</a></td><td><a href="./?url=news_index&newsweb='.$news["news_web"].'&newstype='.$news["news_type"].'">'.$news["news_type"].'</a></td><td align="left"><a href="?url=news_view&id='.$news["news_id"].'" title="'.$news["news_title"].'">'.$news["news_title"].'</a></td><td>'.$news["news_post"].'</td><td>'.substr($news["news_posttime"],0,10).'</td><td><a href="'.$news["news_url"].'" title="'.$news["news_url"].'" target="_blank">链接</a></td></tr>';
		$i=$i+1;
	}
	echo '
                    </table>';
if ($page<4 || $allpage==4 || $allpage==5) $start=2;
elseif ($page>$allpage-4) $start=$allpage-4;
else $start=$page-2;
if ($page>$allpage-4 || $allpage==5) $stop=$allpage;
elseif ($page<4) $stop=6;
else $stop=$page+2;
$tmpurl="&url=news_index&searchtype=".$searchtype;
if ($newsweb) $tmpurl.="&newsweb=$newsweb";
if ($newstype) $tmpurl.="&newstype=$newstype";
if ($keyword) $tmpurl.="&keyword=$keyword";
echo '
                    <div style="height:40px;line-height:40px;vertical-align:middle"><span align="left">';
//if ($keyword) {
//	echo '搜索结果：<b>'.$searchname.'</b> 含有  <font color="red"><b>'.$keyword.'</b></font> 的新闻公告共 <b>'.$count.'</b> 条';
//} else echo '共'.$count.'条记录';
echo '</span><span style="float:right;"><ul class="pagination">';
if ($page==1) echo '<li class="disabled"><a>上页</a></li> <li class="active"><a>1</a></li>';
else echo '<li><a href="?page='.($page-1).$tmpurl.'">上页</a></li> <li><a href="?page=1'.$tmpurl.'">1</a></li>';
if ($page>3 && $allpage!=4 && $allpage!=5 && $allpage!=6) echo '<li class="disabled"><a>...</a></li>';
for ($i=$start;$i<$stop;$i++) {
	if ($i==$page) echo ' <li class="active"><a>'.$i.'</a></li> ';
	else echo ' <li><a href="?page='.$i.$tmpurl.'">'.$i.'</a></li> ';
}
if ($page<$allpage-3 && $allpage!=5 && $allpage!=6) echo '<li class="disabled"><a>...</a></li>';
if ($allpage<=1) echo ' <li class="disabled"><a>下页</a></li>';
elseif ($page==$allpage) echo ' <li class="active"><a>'.$allpage.'</a></li> <li class="disabled"><a>下页</a></li>';
else echo '<li><a href="?page='.$allpage.$tmpurl.'">'.$allpage.'</a></li> <li><a href="?page='.($page+1).$tmpurl.'">下页</a></li>';
echo '</ul></span></div>';
} else echo '<p>无数据！</p>';
echo '
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
if ($keyword) $pagename='搜索结果：'.$keyword;
else $pagename='新闻公告';
