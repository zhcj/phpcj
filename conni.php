<?php
$phphost='';
$username='root';
$password='phpcj';
$phpdata='phpcj_test';
$mysqli=new mysqli($phphost,$username,$password,$phpdata);
$mysqli->query('set names utf8');
if (mysqli_connect_errno()) die('Unable to connect!'). mysqli_connect_error();
include("commoni.php");
