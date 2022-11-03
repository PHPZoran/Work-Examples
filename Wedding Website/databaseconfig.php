<?php
$ini = parse_ini_file('ini/database.ini');
$con=mysqli_connect($ini['db_ip'],$ini['db_username'],$ini['db_password'],$ini['db_name']);
if(mysqli_connect_errno()){
  echo "Failed to connect to MySQL: " . mysqli_connect_error();
}

