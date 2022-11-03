<?php

include 'webhooklogutils.php';

require 'login.php';

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>DM :: Logon Notifier</title>
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

/*DM DISCORD ID BLOCK, update this block when there are new DMs added or DMs gone inactive.
Go Discord->Settings->Appearance->Enable Dev Mode. Then right-click their username to get the ID.
If you're updating the DM list. Update the dropdown at Line 90*/

//Ping all DMs
$dmPing             = "<@&328142012840935425>";

//Specific DMs
//TODO Should probably make this a map. Read up on PhP maps
$dms = array(
  "DM Atropos"      => "<@404352164232953866>",
  "DM Avalon Soul"  => "<@442513037212123137>",
  "DM Butterfly"	=> "<@716864939200872457>",
  "DM Chatsworth"   => "<@484180690666258432>",
  "DM Dionysus"     => "<@576771952073506817>",
  "DM Eyeball"      => "<@485046165675704321>",
  "Grumpycat"       => "<@122793031546961920>",
  "DM Honey"        => "<@645295496860008450>",
  "DM Hoodoo"       => "<@516054437987614730>",
  "DM Magpie"       => "<@695121780272267275>",
  "DM MoonMoon"		=> "<@726489859266445504>",
  "DM Rex"          => "<@687070361535512820>",
  "DM Ricebowl"		=> "<@426935842557132820>",
  "DM Starfish"     => "<@654094255647621120>",
  "DM Spyre"        => "<@328170204196765707>",
  "DM Titania"      => "<@328184592970285066>",
  "DM Wake"		    => "<@699730880272400414>",
  "DM Wish"         => "<@193783522077442049>",
  "DM Wraith"       => "<@500325354666197003>",
  "DM Zinzerena"    => "<@642818409347678208>"
);



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
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

  ArLogStandard($userName, $ClientIPaddress, "\"" . array_search($_POST["mentiontarget"], $dms) . "\" \"" . $_POST["CDKey"] . "\" \"" . $_POST["threadurl"] . "\"");
  DiscordPageQuery($userName, $ClientIPaddress, $ini['dc_api'], "\r\n`" . array_search($_POST["mentiontarget"], $dms) . "`\r\n`" . $_POST["CDKey"] . "`\r\n" . $_POST["threadurl"]);

  // The most important function in the whole code, don't remove or it will break the whole code.
  if($_POST["mentiontarget"] == "Mord"){
    echo "<script> location.href='https://i.imgur.com/QpPKyRH.jpg'; </script>";
  }

  echo "<div align = \"center\">Attempting to update database.</div>";

  trim($_POST["CDKey"]);

  //All CD-Keys are 8 characters long. Don't bother processing if it isn't that long.
  if (strlen($_POST["CDKey"]) == 8) {
    $CDKey = $_POST["CDKey"];
    $mentionRef = $_POST["mentiontarget"];
    $threadRef = $_POST["threadurl"];

    $mentionRef = trim($mentionRef);
    $threadRef = trim($threadRef);
    $CDKey = strtoupper(trim($CDKey));

      if(file_exists('/web_info/web.ini')){
          $ini = parse_ini_file('/web_info/web.ini');
      }else{
          $ini = parse_ini_file('web.ini');
      }

    $con = mysqli_connect($ini['db_ip'], $ini['db_user'], $ini['db_password'], $ini['db_name']);

    if(!$con){
      echo "Error: Unable to connect to MySQL.";
      echo "Debugging errno: " . mysqli_connect_errno();
      echo "Debugging error: " . mysqli_connect_error();
      exit;
    }


    //Execute SQL Statements based on user-input. Yes I know these conditionals are spaghetti and inefficient. Feel free to fix them :- DM Hoodoo
    if(!strlen($mentionRef) > 0 && !strlen($threadRef) > 0){
      $sendSQLPrepared = mysqli_prepare($con, "INSERT INTO gs_player_notify (cdkey, notify_mention, notify_thread) VALUES(?,?,?)");
      mysqli_stmt_bind_param($sendSQLPrepared, "sss", $CDKey, $emptyStr, $emptyStr);
      $dbUpdated = true;

    }
    if(strlen($mentionRef) > 0){
      $sendSQLPrepared = mysqli_prepare($con, "INSERT INTO gs_player_notify (cdkey, notify_mention, notify_thread) VALUES(?,?,?)");
      mysqli_stmt_bind_param($sendSQLPrepared, "sss", $CDKey, $mentionRef, $emptyStr);
      $dbUpdated = true;
    }
    if(strlen($threadRef) > 0){
      $sendSQLPrepared = mysqli_prepare($con, "INSERT INTO gs_player_notify (cdkey, notify_mention, notify_thread) VALUES(?,?,?)");
      mysqli_stmt_bind_param($sendSQLPrepared, "sss", $CDKey, $emptyStr, $threadRef);
      $dbUpdated = true;
    }
    if(strlen($mentionRef) > 0 && strlen($threadRef) > 0){
      $sendSQLPrepared = mysqli_prepare($con, "INSERT INTO gs_player_notify (cdkey, notify_mention, notify_thread) VALUES(?,?,?)");
      mysqli_stmt_bind_param($sendSQLPrepared, "sss", $CDKey, $mentionRef, $threadRef);
      $dbUpdated = true;
    }

      mysqli_stmt_execute($sendSQLPrepared);
      mysqli_stmt_close($sendSQLPrepared);
      mysqli_close ($con);

    }else{
      $errs .= " Invalid CD-Key. Too short.";
    }
}

$errs = trim($errs);


?>
<div class="userinfo">
  <p>
    <strong>User:</strong> <?=$userName;?> <strong>Role:</strong> <?=$group;?>
    <form action="loginhandler.php" method="GET">
      <button class="cutebutt" type="submit" name="logout" value="1">Logout</button>
    </form>
</p>
</div>
<h1 align="center">Logon Notifier</h1>
<div class= "mention" align = "center">
  <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
    <p>Who To Mention <select name ="mentiontarget">
      <option value ="">Nobody</option>
      <option value ="<?=$dmPing;?>">DM Group</option>
      <option value ="<?=$dms["DM Atropos"];?>">DM Atropos</option>
      <option value ="<?=$dms["DM Avalon Soul"];?>">DM Avalon Soul</option>
      <option value ="<?=$dms["DM Butterfly"];?>">DM Butterfly</option>
      <option value ="<?=$dms["DM Chatsworth"];?>">DM Chatsworth</option>
      <option value ="<?=$dms["DM Dionysus"];?>">DM Dionysus</option>
      <option value ="<?=$dms["DM Eyeball"];?>">DM Eyeball</option>
      <option value ="<?=$dms["Grumpycat"];?>">GrumpyCat</option>
      <option value ="<?=$dms["DM Honey"];?>">DM Honey</option>
      <option value ="<?=$dms["DM Hoodoo"];?>">DM Hoodoo</option>
      <option value ="<?=$dms["DM Magpie"];?>">DM Magpie</option>
      <option value ="<?=$dms["DM MoonMoon"];?>">DM MoonMoon</option>
      <option value ="<?=$dms["DM Rex"];?>">DM Rex</option>
      <option value ="<?=$dms["DM Ricebowl"];?>">DM Ricebowl</option>
      <option value ="<?=$dms["DM Starfish"];?>">DM Starfish</option>
      <option value ="<?=$dms["DM Spyre"];?>">DM Spyre</option>
      <option value ="<?=$dms["DM Titania"];?>">DM Titania</option>
      <option value ="<?=$dms["DM Wake"];?>">DM Wake</option>
      <option value ="<?=$dms["DM Wish"];?>">DM Wish</option>
      <option value ="<?=$dms["DM Wraith"];?>">DM Wraith</option>
      <option value ="<?=$dms["DM Zinzerena"];?>">DM Zinzerena</option>
      <option value ="Mord">Mord!!!</option>
    </select>

    <br>
  </div>
  <div class ="key" align = "center">
    CD-Key
    <input type="text" name="CDKey" maxlength="8" required />
    <br><br>
  </div>
  <div class ="thread" align="center">
    Thread reference (If Applicable)
    <br>
    <input type ="text" name="threadurl" />
    <br><br>
  </div>
  <div class="cutebut">
    <input class = "cutebut" type="submit" name="submit" value="Submit">
  </div>
</form>
<?php
if($dbUpdated == true)
$succMess = "Database Updated!";
?>
<hr class ="separator"/>
<div class ="end">
  <body>

    <h2 align = "center">Pending Notifies</h2>
    <h4 align = "center"><?=$errs?></h4>
    <h4 align = "center" class="success"><?=$succMess?></h4>
    <table style="width:100%">
      <tr align ="center">
        <th><h3>CDKey</h3></th>
        <th><h3>Mention</h3></th>
        <th><h3>Thread Reference</h3></th>
        <th><h3>Remove Notify</h3></th>
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


      $query = "SELECT cdkey, notify_mention, notify_thread FROM gs_player_notify";
      $result = mysqli_query($con, $query);

      while($row = mysqli_fetch_array($result)){

        $mention = $row["notify_mention"];
        $key     = $row['cdkey'];

        //Check if the Discord ID is for all DMs, if not, get DM Name from associative array $dms
        if($mention == "<@&328142012840935425>"){
          $mention = "All DMs";
        }else if($row["notify_mention"]){
          $mention = array_search($row["notify_mention"], $dms);
        }else{
          $mention = "Nobody";
        }

        if($row['notify_thread']){
          $link = "Reference Link";
        }else{
          $link = "";
        }

        // Remove button action is paired with the CD-Key via the $_GET ID in the url and populated on run-through.
        echo
        "<tr align=\"center\"><td> " . $key . "</td><td>" . $mention . "</td><td><a href=" . $row['notify_thread'] . " >" . $link . "</a></td>
        <td><form method =\"post\" action = \"logon-notifier-toggle.php?id=". $key. "\"</a>
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
