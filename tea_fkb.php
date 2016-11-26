<?php
if (!$usersort) {Header("Location:".$web_root."/");exit();}
$infors=$mysqli->query("select * from phpcj_fkb where fkb_name='$username'");
$info=$infors->fetch_array();
if (!$info) $error='<h3><font color="red">只需备课组长填写，如备课组长出现此提示，请联系管理员！</font></h3>';
else $error='';
$alert=_get('alert');
if ($alert=='ok') $alert=' <font color="red">提交成功！</font>';
else $alert='';
$errorid=_get('error');
if ($errorid==1) $errorid=' <font color="red">时间填写错误！</font>';
else $errorid='';
$fkb_grade=$info['fkb_grade'];
$fkb_grade=str_replace('g','高中',$fkb_grade);
$fkb_grade=str_replace('c','初中',$fkb_grade);
$fkb_grade.='级';
$fkb_subject=$info['fkb_subject'];
$fkb_wenti=$info['fkb_wenti'];
if (!$fkb_wenti) $fkb_wenti='1、'.chr(13).'2、';
$fkb_pingjia=$info['fkb_pingjia'];
if (!$fkb_pingjia) $fkb_pingjia='1、'.chr(13).'2、';
echo '
<link href="'.$web_root.'/css/bootstrapValidator.min.css" rel="stylesheet">
<script src="'.$web_root.'/js/bootstrapValidator.min.js"></script>
';?>
<script language=JavaScript>
$(document).ready(function() {
    $('#defaultForm').bootstrapValidator({
        message: 'This value is not valid',
        feedbackIcons: {
            valid: 'glyphicon glyphicon-ok',
            invalid: 'glyphicon glyphicon-remove',
            validating: 'glyphicon glyphicon-refresh'
        },
        fields: {
            fkb_wenti: {
                validators: {
                    notEmpty: {
                        message: '学生暴露的问题不能为空！'
                    }
                }
            },
            fkb_pingjia: {
                validators: {
                    notEmpty: {
                        message: '试卷评价不能为空！'
                    }
                }
            },
        }
    });
});
</script>
<?php echo '  <div class="content-wrapper">
    <section class="content-header">
      <h1>桥西区试卷评价反馈表</h1>
    </section>
    <section class="content">
      <div class="row">
        <div class="col-lg-12">
          <div class="box box-primary">
            <div class="box-header with-border">
              <h4>桥西区2015-2016学年度第二学期期末考试试卷评价反馈表（备课组），要求各备课组长填写以下信息！'.$errorid.$alert;
echo '</h4>
            </div>
            <div class="box-body">
              <div class="col-lg-12">';
if ($error) echo $error;
else {
	echo '
                <h4>备课组：'.$fkb_grade.$fkb_subject.'</h4>
                <form id="defaultForm" action="'.$web_root.'/" method="post">
                  <input type="hidden" name="url" value="tea_operate"><input type="hidden" name="action" value="fkb">
                  <div class="form-group">
                    <label>一、学生暴露的问题：</label>
                    <textarea name="fkb_wenti" class="form-control" rows="5">'.$fkb_wenti.'</textarea>
                  </div>
                  <div class="form-group">
                    <label>二、对试卷的评价：</label>
                    <textarea name="fkb_pingjia" class="form-control" rows="5">'.$fkb_pingjia.'</textarea>
                  </div>
                  <div class="form-group">
                    <div class="col-sm-offset-3 col-sm-9">
                      <button type="submit" class="btn btn-primary" name="signup">提交</button>
                    </div>
                  </div>
                </form>';
}
if ($username=='代春燕' || $username=='张利红' || $username=='管理员') {
//	echo '<table class="table table-bordered table-condensed table-hover">';
	$i=1;
	$infors=$mysqli->query("select * from phpcj_fkb order by fkb_grade,fkb_subject,fkb_wenti");
	while ($data=$infors->fetch_array()) {
		echo '<h4>'.$i.'、'.$data['fkb_name'].' <a class="btn btn-primary" href="'.$web_root.'/fkb.doc.php?id='.$data['fkb_id'].'">'.$data['fkb_grade'].$data['fkb_subject'].'</a></h4>
	<table class="table table-bordered table-condensed table-hover">
	<tr><td>问题</td><td>'.$data['fkb_wenti'].'</td></tr>
	<tr><td>评价</td><td>'.$data['fkb_pingjia'].'</td></tr>
</table>';
		$i++;
	}
}
echo '
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
  </div>
';
$pagename='桥西区试卷评价反馈表（备课组）';
