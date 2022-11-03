<?php
$db_host = 'localhost';
$db_name = 'u992535075_Website';
$db_username = 'root';
$db_password = 'huzumaki2';
$dsn = "mysql:host=$db_host;dbname=$db_name"; 

$con=mysqli_connect($db_host,$db_username,$db_password,$db_name);

// Check connection
if (mysqli_connect_errno()) {
  echo "Failed to connect to MySQL: " . mysqli_connect_error();
}

?>