<?php

include 'webhooklogutils.php';

require 'login.php';

//Refer to index.php for explanation of below few lines
session_start();

if(time() > $_SESSION['expiry']){
  session_destroy();
  header('Location: loginhandler.php?logout=1&refUrl=' . basename($_SERVER["PHP_SELF"]) . "?" . $_SERVER['QUERY_STRING']);
}


if($_SESSION["loggedin"]){
  $userName = $_SESSION["username"];
  $group    = $_SESSION['user_group'];
}else{
  session_destroy();
  header('Location: loginhandler.php?logout=1&refUrl=' . basename($_SERVER["PHP_SELF"]) . "?" . $_SERVER['QUERY_STRING']);
}



//This file is used to toggle the notify that's detailed in logon-notifier.php

$CDKey = $_GET["id"];
if(file_exists('/web_info/web.ini')){
  $ini = parse_ini_file('/web_info/web.ini');
}else{
  $ini = parse_ini_file('web.ini');
}

$CDKey = strtoupper(trim($CDKey));

$con = mysqli_connect($ini['db_ip'], $ini['db_user'], $ini['db_password'], $ini['db_name']);


if(!$con){
  echo "Error: Unable to connect to MySQL.";
  echo "Debugging errno: " . mysqli_connect_errno();
  echo "Debugging error: " . mysqli_connect_error();
  exit;
}

$SQLStatement = "DELETE FROM gs_player_notify WHERE cdkey='" . $CDKey . "'";

if(mysqli_query($con, $SQLStatement)){
  ArLogStandard($userName, $ClientIPaddress, $CDKey);
  DiscordPageQuery($userName, $ClientIPaddress,  $ini['dc_api'], "Removed notify for " . $CDKey);
  mysqli_close($con);
  echo "<script> location.href='logon-notifier.php?id=success'; </script>";
}

echo mysqli_close($con);

?>
