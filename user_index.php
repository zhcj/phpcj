<?php
if (!$userid) Header("Location:./");
if ($usersort) include("tea_index.php");
else include("stu_index.php");
