<?php
if (!$userid) Header("Location:./");
if ($usersort) include("tea_info.php");
else include("stu_info.php");
