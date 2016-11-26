<?php
/*
本文件源代码来自网络，出处不详，如涉及版权问题，请原作者与本人联系。
*/
error_reporting(0);
$mingci=trim(htmlspecialchars($_GET['mingci']));
$kaoshi=trim(htmlspecialchars($_GET['kaoshi']));
$nianji=trim(htmlspecialchars($_GET['nianji']));
if (!$mingci || !$kaoshi || !$nianji) Header("Location:./");
$mingci=explode(";",$mingci);
$kaoshi=explode(";",$kaoshi);
$nianji=explode(";",$nianji);
class build_graph {
	var $graphwidth=400;
	var $graphheight=220;
	var $width_num=0;                //宽分多少等分
	var $height_num=10;                //高分多少等分，默认为10
	var $height_var=0;                //高度增量（用户数据平均数）
	var $width_var=0;                //宽度增量（用户数据平均数）
	var $height_max=0;                //最大数据值
	var $height_min=1;                //最小数据值
	var $array_data=array();          //用户待分析的数据的二维数组
	var $kaoshi=array();
	var $array_error=array();          //收集错误信息
	var $colorBg=array(255,255,255);    //图形背景-白色
	var $colorGrey=array(192,192,192);    //灰色画框
	var $colorBlue=array(0,0,255);       //蓝色
	var $colorRed=array(0,0,0);       //红色（点）
	var $colorBlack=array(255,255,255);       //黑色（点）
	var $colorDarkBlue=array(0,0,255);    //深色
	var $colorLightBlue=array(200,200,255);       //浅色
	var $array_color;                //曲线着色（存储十六进制数）
	var $image;                      //我们的图像

    //方法：接受用户数据
	function add_data($array_user_data){
		if(!is_array($array_user_data) or empty($array_user_data)){
			$this->array_error['add_data']="没有可供分析的数据";
			return false;
			exit();
		}
		$i=count($this->array_data);
		$this->array_data[$i]=$array_user_data;
	}
	function kaoshi($kaoshidata){
		if(!is_array($kaoshidata) or empty($kaoshidata)){
			$this->array_error['kaoshi']="没有可供分析的数据";
			return false;
			exit();
		}
		$aaa=count($this->array_data);
		$this->kaoshi[0]=$kaoshidata;
	}
	function nianji($nianjidata){
		if(!is_array($nianjidata) or empty($nianjidata)){
			$this->array_error['nianji']="没有可供分析的数据";
			return false;
			exit();
		}
		$aaa=count($this->array_data);
		$this->nianji[0]=$nianjidata;
	}
    //方法：定义画布宽和长
	function set_img($img_width,$img_height){
		$this->graphwidth=$img_width;
		$this->graphheight=$img_height;
	}
    //设定Y轴的增量等分，默认为10份
	function set_height_num($var_y){
		$this->height_num=$var_y;
	}
    //定义各图形各部分色彩
	function get_RGB($color){             //得到十进制色彩
	$R=($color>>16) & 0xff;
	$G=($color>>8) & 0xff;
	$B=($color) & 0xff;
	return (array($R,$G,$B));
	}
    #定义背景色
	function set_color_bg($c1,$c2,$c3){
		$this->colorBg=array($c1,$c2,$c3);
	}
    #定义画框色
	function set_color_Grey($c1,$c2,$c3){
		$this->colorGrey=array($c1,$c2,$c3);
	}
    #定义蓝色
	function set_color_Blue($c1,$c2,$c3){
		$this->colorBlue=array($c1,$c2,$c3);
	}
    #定义色Red
	function set_color_Red($c1,$c2,$c3){
		$this->colorRed=array($c1,$c2,$c3);
	}
    #定义深色
	function set_color_DarkBlue($c1,$c2,$c3){
		$this->colorDarkBlue=array($c1,$c2,$c3);
	}
    #定义浅色
	function set_color_LightBlue($c1,$c2,$c3){
		$this->colorLightBlue=array($c1,$c2,$c3);
	}
    //方法:由用户数据将画布分成若干等份宽
    //并计算出每份多少像素
	function get_width_num(){
		$this->width_num=count($this->array_data[0]);
	}
	function get_max_height(){
		$tmpvar=array(); 
		foreach($this->array_data as $tmp_value){ 
			$tmpvar[]=max($tmp_value); 
		} 
		$this->height_max=max($tmpvar); 
		return max($tmpvar); 
	}
	function get_min_height(){
       //获得用户数据的最小值
		$tempvar=array(); 
		foreach($this->array_data as $temp_value){ 
			$tempvar[]=min($temp_value); 
		} 
		$this->height_min=min($tempvar); 
		return min($tempvar); 
	}
	function get_height_length(){
       //计算出每格的增量长度(用户数据，而不是图形的像素值)
		$max_var=$this->get_max_height();
		$min_var=$this->get_min_height();
		$max_var=round($max_var/$this->height_num);
		$first_num=substr($max_var,0,1);
		if(substr($max_var,1,1)){
			if(substr($max_var,1,1)>=5)
			$first_num+=1;
		}
		for($i=1;$i<strlen($max_var);$i++){
			$first_num.="0";
		}
		return (int)$first_num;
	}
	function get_var_wh(){          //得到高和宽的增量
		$this->get_width_num();
       //得到高度增量和宽度增量
		$this->height_var=$this->get_height_length();
		$this->width_var=round($this->graphwidth/$this->width_num);
	}
	function set_colors($str_colors){
       //用于多条曲线的不同着色，如$str_colors="ee00ff,dd0000,cccccc"
		$this->array_color=explode(",",$str_colors);
	}
	function build_line($var_num){
		if(!empty($var_num)){                   //如果用户只选择显示一条曲线
			$array_tmp[0]=$this->array_data[$var_num-1];
			$this->array_data=$array_tmp;
		}

		for($j=0;$j<count($this->array_data);$j++){
			list($R,$G,$B)=$this->get_RGB(hexdec($this->array_color[$j]));
			$colorBlue=imagecolorallocate($this->image,$R,$G,$B);
			for($i=0;$i<$this->width_num-1;$i++){
				$height_pix=round(($this->array_data[$j][$i]/$this->height_max)*$this->graphheight)+20;
				$height_next_pix=round($this->array_data[$j][$i+1]/$this->height_max*$this->graphheight)+20;
				imageline($this->image,($this->width_var*$i+$this->width_var/2)+40,$height_pix,($this->width_var*($i+1)+$this->width_var/2)+40,$height_next_pix,$colorBlue);
			}
		}
       //画点
		$colorRed=imagecolorallocate($this->image, $this->colorRed[0], $this->colorRed[1], $this->colorRed[2]);
		$strposition='';
		for($j=0;$j<count($this->array_data);$j++){
			for($i=0;$i<$this->width_num;$i++){
				$height_pix=round(($this->array_data[$j][$i]/$this->height_max)*$this->graphheight)+20;
				imagearc($this->image,($this->width_var*$i+$this->width_var/2)+40,$height_pix,6,5,0,360,$colorRed);
				imagefilltoborder($this->image,($this->width_var*$i+$this->width_var/2)+40,$height_pix-1,$colorRed,$colorRed);
				if (strlen($this->array_data[$j][$i])==3) $strposition=28;
				if (strlen($this->array_data[$j][$i])==2) $strposition=32;
				if (strlen($this->array_data[$j][$i])==1) $strposition=36;
				imagestring($this->image,5,($this->width_var*$i+$this->width_var/2)+$strposition,$height_pix-18,$this->array_data[$j][$i],$colorRed);
			}
		}
	}
    function build_rectangle($select_gra){
       if(!empty($select_gra)){                   //用户选择显示一个矩形
          $select_gra-=1;
       }
       //画矩形
       //配色
       $colorDarkBlue=imagecolorallocate($this->image, $this->colorDarkBlue[0], $this->colorDarkBlue[1], $this->colorDarkBlue[2]);
       $colorLightBlue=imagecolorallocate($this->image, $this->colorLightBlue[0], $this->colorLightBlue[1], $this->colorLightBlue[2]);

       if(empty($select_gra))
          $select_gra=0;
       for($i=0; $i<$this->width_num; $i++){
          $height_pix=round(($this->array_data[$select_gra][$i]/$this->height_max)*$this->graphheight);
          imagefilledrectangle($this->image,$this->width_var*$i,$this->graphheight-$height_pix,$this->width_var*($i+1),$this->graphheight, $colorDarkBlue);
          imagefilledrectangle($this->image,($i*$this->width_var)+1,($this->graphheight-$height_pix)+1,$this->width_var*($i+1)-5,$this->graphheight-2, $colorLightBlue);
       }
    }
       //创建画布
	function create_cloths(){
		$this->image=imagecreate($this->graphwidth+90,$this->graphheight+65);
	}
       //创建画框 
	function create_frame(){
		$this->get_var_wh();
       //配色 
		$colorBg=imagecolorallocate($this->image, $this->colorBg[0], $this->colorBg[1], $this->colorBg[2]);
		$colorGrey=imagecolorallocate($this->image, $this->colorGrey[0], $this->colorGrey[1], $this->colorGrey[2]);
       //创建图像周围的框
		imageline($this->image,40,20, $this->graphwidth+40,20,$colorGrey);//上边框
//		imageline($this->image,40,20,40,$this->graphheight+19,$colorGrey);//左边框
		imageline($this->image, ($this->graphwidth)+40,20,($this->graphwidth)+40,$this->graphheight+20,$colorGrey);//右边框
		imageline($this->image, 40,$this->graphheight+20,($this->graphwidth)+40,$this->graphheight+20,$colorGrey);//下边框
	}
	function create_line(){
       //创建网格。
		$this->get_var_wh();
		$colorBg=imagecolorallocate($this->image, $this->colorBg[0], $this->colorBg[1], $this->colorBg[2]);
		$colorGrey=imagecolorallocate($this->image, $this->colorGrey[0], $this->colorGrey[1], $this->colorGrey[2]);
 		$colorRed=imagecolorallocate($this->image, $this->colorRed[0], $this->colorRed[1], $this->colorRed[2]);
		imagestring($this->image,4,29,13,0,$colorRed);//标出0
		for($i=1;$i<=($this->height_num+4);$i++){
          //画横线
			if ($this->height_var*$i<=$this->height_max) {
				imageline($this->image,40,($this->height_var/$this->height_max*$this->graphheight)*$i+20,$this->graphwidth+40,($this->height_var/$this->height_max*$this->graphheight)*$i+20,$colorGrey);
          //标出数字
				$yposition='';
				if (strlen($this->height_var*$i)==3) $yposition=13;
				if (strlen($this->height_var*$i)==2) $yposition=21;
				if (strlen($this->height_var*$i)==1) $yposition=29;
				if ($this->height_var!=0) imagestring($this->image,4,$yposition,($this->height_var/$this->height_max*$this->graphheight)*$i+13,$this->height_var*$i,$colorRed);
			}
		}
	   for($i=0;$i<$this->width_num;$i++){
			imageline($this->image,$this->width_var*$i+40,20,$this->width_var*$i+40,$this->graphheight+19,$colorGrey);//画竖线

			imagestring($this->image,4,($this->width_var*$i+$this->width_var/2)+25,$this->graphheight+28,$this->kaoshi[0][$i],$colorRed);//标出数字
			imagettftext($this->image,10,0,($this->width_var*$i+$this->width_var/2)+28,$this->graphheight+58,$colorRed,'./font.ttf',num2text($this->nianji[0][$i]));//标出年级
		}
		imagettftext($this->image,11,0,448,50,$colorRed,'./font.ttf','最好');
		imagettftext($this->image,11,0,448,70,$colorRed,'./font.ttf','名次');
		imagestring($this->image,5,449,80,$this->height_min,$colorRed);
		imagettftext($this->image,11,0,448,160,$colorRed,'./font.ttf','最差');
		imagettftext($this->image,11,0,448,180,$colorRed,'./font.ttf','名次');
		imagestring($this->image,5,449,190,$this->height_max,$colorRed);
	}
	function build($graph,$str_var){
       //$graph是用户指定的图形种类,$str_var是生成哪个数据的图
		header("Content-type: image/jpeg");
 		$this->create_cloths();          //先要有画布啊~~
		switch ($graph){
			case "line":
				$this->create_frame();          //画个框先：）
				$this->create_line();          //打上底格线
				$this->build_line($str_var);          //画曲线
				break;
			case "rectangle":
				$this->create_frame();                   //画个框先：）
				$this->build_rectangle($str_var);          //画矩形
				$this->create_line();                   //打上底格线
				break;
		}
       //输出图形并清除内存
		imagepng($this->image);
		imagedestroy($this->image);
	}
}
$line=new build_graph();
$line->kaoshi($kaoshi);
$line->nianji($nianji);
$line->add_data($mingci);
//$line->add_data($mingci1);
$line->set_colors("00ff00");
//生成曲线图
$line->build("line",1);          //参数0表示显示所有曲线，1为显示第一条，依次类推
//生成矩形图
$line->build("rectangle","0");    //参数0表示显示第一个矩形，1也为显示第一条，其余依次类推
function num2text($cj){
	$cj=str_replace("g","高",$cj);
	$cj=str_replace("c","初",$cj);
	$cj=str_replace("1","一",$cj);
	$cj=str_replace("2","二",$cj);
	$cj=str_replace("3","三",$cj);
	return $cj;
}
?>
