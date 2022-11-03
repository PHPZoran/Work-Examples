<?php

include 'webhooklogutils.php';

require 'login.php';

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>DM :: Jumper</title>
    <!-- Idle handler -->
    <script src="idle.js" type="text/javascript"></script>
    <link href="css/logon-notifier.css" type= "text/css" rel="stylesheet" />
    <style>
        .cutebutt{
            display: inline-block;
            font-weight: 400;
            text-align: center;
            white-space: nowrap;
            vertical-align: middle;
            -webkit-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            user-select: none;
            border: 1px solid transparent;
            padding: .2rem .2rem;
            font-size: 1rem;
            line-height: 1.5;
            border-radius: .25rem;
            transition: color .15s ease-in-out,background-color .15s ease-in-out,border-color .15s ease-in-out,box-shadow .15s ease-in-out;
            color: #fff;
            background-color: #17a2b8;
            border-color: #17a2b8;
            margin-bottom: 10px;
            cursor: pointer;
            width: 5%;
        }

        .cutebutt:hover{
            color: #fff;
            background-color: #138496;
            border-color: #17a2b8;
        }
    </style>
</head>
<?php

//$errs handles the error to be printed out
$errs = "";

$dbUpdated = FALSE;

//$succMess works same as $errs see above. Except it's green!
$succMess = "";

//Need emptyStr as a variable for mysqli_stmt_bind_params. Don't change.
$emptyStr = "";


if($_SERVER["REQUEST_METHOD"] == "GET"){
    if($_GET["id"] == "success"){
        $succMess = "Selection succesfully removed!";
    }

    if($_GET["pc"]){

        if(file_exists('/web_info/web.ini')){
            $ini = parse_ini_file('/web_info/web.ini');
        }else{
            $ini = parse_ini_file('web.ini');
        }

        $pcName = $_GET["pc"];

        $con = mysqli_connect($ini['db_ip'], $ini['db_user'], $ini['db_password'], $ini['db_name']);

        if(!$con){
            echo "Error: Unable to connect to MySQL.";
            echo "Debugging errno: " . mysqli_connect_errno();
            echo "Debugging error: " . mysqli_connect_error();
            exit;
        }

    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

//    ArLogStandard($userName, $ClientIPaddress, "\"" . array_search($_POST["mentiontarget"], $dms) . "\" \"" . $_POST["CDKey"] . "\" \"" . $_POST["threadurl"] . "\"");
//    DiscordPageQuery($userName, $ClientIPaddress, $ini['dc_api'], "\r\n`" . array_search($_POST["mentiontarget"], $dms) . "`\r\n`" . $_POST["CDKey"] . "`\r\n" . $_POST["threadurl"]);


    //echo "<div align = \"center\">Attempting to update database.</div>";

    //All CD-Keys are 8 characters long. Don't bother processing if it isn't that long.


    $errs = trim($errs);
}

?>
<div class="userinfo">
    <p>
        <strong>User:</strong> <?=$userName;?> <strong>Role:</strong> <?=$group;?>
    <form action="loginhandler.php" method="GET">
        <button class="cutebutt" type="submit" name="logout" value="1">Logout</button>
    </form>
    </p>
</div>
<?php
if($dbUpdated == true)
    $succMess = "Database Updated!";
?>
<hr class ="separator"/>
<div class ="end">
    <body>

    <h2 align = "center">Pending Jumps</h2>
    <h4 align = "center"><?=$errs?></h4>
    <h4 align = "center" class="success"><?=$succMess?></h4>
    <table style="width:100%">
        <tr align ="center">
            <th><h3>ID</h3></th>
            <th><h3>DM</h3></th>
            <th><h3>PC</h3></th>
        </tr>

        <?php
        $ini = parse_ini_file('/web_info/web.ini');

        // TEST CODE DO NOT PUSH WITH THIS IN IT TODO
        //$ini = parse_ini_file('web.ini');
        // TEST CODE DO NOT PUSH WITH THIS IN IT TODO


        $con = mysqli_connect($ini['db_ip'], $ini['db_user'], $ini['db_password'], $ini['db_name']);

        if(!$con){
            echo "Error: Unable to connect to MySQL.";
            echo "Debugging errno: " . mysqli_connect_errno();
            echo "Debugging error: " . mysqli_connect_error();
            exit;
        }


        $query = "SELECT pcid, visiblename, jumping_dm FROM mixf_currentplayers WHERE jumping_dm IS NOT NULL";
        $result = mysqli_query($con, $query);

        while($row = mysqli_fetch_array($result)){

            $id = $row["pcid"];
            $dm = $row["jumping_dm"];
            $pc = $row["visiblename"];


            // Remove button action is paired with the CD-Key via the $_GET ID in the url and populated on run-through.
            echo
                "<tr align=\"center\"><td> ". $id . "</td><td> " . $dm . "</td><td>" . $pc . "</td>
        <td><form method =\"post\" action = \"dmjump-toggle.php?id=". $id. "\"</a>
        <button type=\"submit\" name=\"removenotify\" method=\"post\">Remove</button>
        </form>
        </td></tr>";

        }

        echo "</table>";

        mysqli_close($con);

        ?>
</div>
</body>
<div class = "footer">
</div>
</html>
