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



//This file is used to toggle the row that's detailed in dmjump

$id = $_GET["id"];
$pcID = $_GET["pcid"];

if(file_exists('/web_info/web.ini')){
    $ini = parse_ini_file('/web_info/web.ini');
}else{
    $ini = parse_ini_file('web.ini');
}

$pass = $ini["pass"];

$con = mysqli_connect($ini['db_ip'], $ini['db_user'], $ini['db_password'], $ini['db_name']);


if(!$con){
    echo "Error: Unable to connect to MySQL.";
    echo "Debugging errno: " . mysqli_connect_errno();
    echo "Debugging error: " . mysqli_connect_error();
    exit;
}
if($id) {
    $SQLStatementPrepared = mysqli_prepare($con, "UPDATE mixf_currentplayers SET jumping_dm=NULL WHERE pcid=?");
    mysqli_stmt_bind_param($SQLStatementPrepared, "i",  $id);
    mysqli_stmt_execute($SQLStatementPrepared);
    mysqli_stmt_close($SQLStatementPrepared);
    mysqli_close($con);

    if (mysqli_query($con, $SQLStatement)) {

        ArLogStandard($userName, $ClientIPaddress, $id);
        DiscordPageQuery($userName, $ClientIPaddress, $ini['dc_api'], "Removed jump for " . $id);
        mysqli_close($con);
        echo "<script> location.href='dmjump.php?id=success'; </script>";
    }
    echo "<script> location.href='dmjump.php?id=success'; </script>";
}

if($pcID){
    $query = "SELECT server FROM mixf_currentplayers WHERE pcid='$pcID'";
    $result = mysqli_query($con, $query);

    while($row = mysqli_fetch_array($result)) {
        $server = ReturnServerPort($row['server']);
    }

    $insertDMSQL = mysqli_prepare($con, "UPDATE mixf_currentplayers SET jumping_dm=? WHERE pcid=?");
    mysqli_stmt_bind_param($insertDMSQL, "si", $_SESSION["username"], $pcID);
    mysqli_stmt_execute($insertDMSQL);
    mysqli_stmt_close($insertDMSQL);
    mysqli_close($con);
    ArLogStandard($userName, $ClientIPaddress, $CDKey);
    DiscordPageQuery($userName, $ClientIPaddress, $ini['dc_api'], "DM Jumped to PCID " . $pcID);
    echo "<script type=\"text/javascript\">
        window.open('steam://run/704450//-dmc +connect game.arelith.com:".$server." +password ".$pass."')
        </script>";

    echo "<script> location.href='dmjump.php'; </script>";
}

function ReturnServerPort($serverNo){
    switch($serverNo){
        case 1:
            return "5123";
            break;
        case 2:
            return "5122";
            break;
        case 6:
            return "5124";
            break;
        case 8:
            return "5121";
            break;
        default:
            return "5121";
    }
}


?>
