<?php

if(file_exists('web_info/interview.ini')){
    $ini = parse_ini_file('web_info/interview.ini');
}else{
   $ini = parse_ini_file('web.ini');
}

$con = mysqli_connect($ini['db_ip'], $ini['db_user'], $ini['db_password'], $ini['db_name']);
/*if (mysqli_connect_errno()) {
  echo "Failed to connect to MySQL: " . mysqli_connect_error();
}*/

?>