<?php
if (!$userid) {Header("Location:".$web_root."/?url=user_login");exit();}
$id=_get('id');
$image_path="picture/"
$image_file=$image_path.$_GET['name'];
$sTmpVar = fread(fopen($image_file, 'r'), filesize($image_path));
header("Content-type: image/* ");
echo $sTmpVar;
