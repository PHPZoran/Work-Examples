<?php

include 'webhooklogutils.php';

//Below two lines enable going back and forth
header('Cache-Control: no cache'); //no cache
session_cache_limiter('private_no_expire'); // works

require 'login.php';


/** Case-tool.php
  * DM Hoodoo
  * Use-case: Expose variables about the PC, their factions,
  * ,quarters, and past cases. Creates a convenient piece of
  * copy/pasteable BB-Code for DMs to use when creating cases
  * Note: Has one GET variable available for use currently
  * ?key=(PUBLICKEY) via any link will allow remote use of
  * this form.
  * Special thanks to DM Titania for helping me with the HTML and CSS
  */
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>DM :: Case Tool</title>
  <!-- Idle handler -->
  <script src="idle.js" type="text/javascript"></script>
  <link href="css/case-tool.css" type= "text/css" rel="stylesheet" />
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

// Init arrays and default values because PhP doesn't like them being undefined
$pcData = array();
$quarterData = array();
$factionData = array();
$playerNames = array();
$uniquePlayerNames = array();
$cases = array();
$playerNames = array();

$deletedCharacterCount = 0;
$characterCount = 0;

$pcNamesDeletedString = "";
$pcNamesString = "";
$playerNamesString = "";
$casesString = "";
$param = "";

//$errs handles the error to be printed out
$errs = "";

//$succMess works same as $errs see above. Except it's green!
$succMess = "";

//Need emptyStr as a variable for mysqli_stmt_bind_params. Don't change.
$emptyStr = "";


if ($_SERVER["REQUEST_METHOD"] == "POST" || $_SERVER["REQUEST_METHOD"] == "GET") {



  //All CD-Keys are 8 characters long. Don't bother processing if it isn't that long.
  if ((strlen($_POST["CDKey"]) == 8 || strlen($_GET["key"]) == 8) && $userName) {


    $CDKey = $_POST["CDKey"] ?: $_GET["key"];

    $CDKey = strtoupper(trim($CDKey));

      if(file_exists('/web_info/web.ini')){
          $ini = parse_ini_file('/web_info/web.ini');
      }else{
          $ini = parse_ini_file('web.ini');
      }

    ArLogStandard($userName, $ClientIPaddress, $_POST["CDKey"] ? $_POST["CDKey"] : $_GET["key"]);
    DiscordPageQuery($userName, $ClientIPaddress, $ini['dc_api'], $CDKey);

    $con = mysqli_connect($ini['db_ip'], $ini['db_user'], $ini['db_password'], $ini['db_name']);

    if(!$con){
      echo "Error: Unable to connect to MySQL.";
      echo "Debugging errno: " . mysqli_connect_errno();
      echo "Debugging error: " . mysqli_connect_error();
      exit;
    }

    //SQL Below to get relevant player, faction, quarter, and case details.
    $playerSqlPrepared = mysqli_prepare($con, "SELECT id, rp, tells FROM gs_player_data WHERE cdkey=?");
    mysqli_stmt_bind_param($playerSqlPrepared, "s", $CDKey);
    mysqli_stmt_execute($playerSqlPrepared);

    // echo mysqli_error($con);

    $result = mysqli_stmt_get_result($playerSqlPrepared);

    while($row = mysqli_fetch_array($result)){
      $playerRPR    = $row['rp'];
      $playerID     = $row['id'];
      $playerTells  = $row['tells'];
    }
    mysqli_stmt_close($playerSqlPrepared);

    $characterInfoSqlPrepared = mysqli_prepare($con, "SELECT pc.id, pc.name AS pc_name, pc.playername, pc.subrace, pc.bank, pc.wealth, pc.gender, pc.nation, pc.c1, pc.c2, pc.c3, pc.c4, pc.c5, pc.c6,
      pc.deleted, pc.classname_1, pc.classlevel_1, pc.classname_2, pc.classlevel_2, pc.classname_3, pc.classlevel_3, pc.awia, quarter.row_key, quarter.owner,
      quarter.name AS quarter_name, quarter.public, quarter.for_sale, quarter.sale_price, quarter.last_used, quarter.last_tax, quarter.area_id,
      facmem.faction_id, facmem.is_OwnerRank, fac.name AS faction_name, fac.nation AS faction_nation, fac.bank AS faction_bank FROM gs_pc_data AS pc
      LEFT JOIN gs_quarter AS quarter ON pc.id=quarter.owner LEFT JOIN md_fa_members AS facmem ON facmem.pc_id=pc.id LEFT JOIN
      md_fa_factions AS fac ON facmem.faction_id=fac.id WHERE pc.keydata=? ORDER BY pc.deleted ASC, pc.name ASC"
    );

    mysqli_stmt_bind_param($characterInfoSqlPrepared, "i", $playerID);
    mysqli_stmt_execute($characterInfoSqlPrepared);

    $result = mysqli_stmt_get_result($characterInfoSqlPrepared);

    while($row = mysqli_fetch_array($result)){

      $pcID = $row['id'];
      $quarterID = $row['row_key'];
      $factionID = $row['faction_id'];
      $pcData[$pcID]['pc_name'] = $row['pc_name'];
      $pcData[$pcID]['playername'] = $row['playername'];
      $pcData[$pcID]['subrace'] = $row['subrace'];
      $pcData[$pcID]['bank'] = $row['bank'];
      $pcData[$pcID]['wealth']  = $row['wealth'] ?? "N/A";
      $pcData[$pcID]['gender']  = $row['gender'];
      $pcData[$pcID]['nation']  =$row['nation'] ?? "N/A";
      $pcData[$pcID]['c1']  = $row['c1'];
      $pcData[$pcID]['c2'] =$row['c2'];
      $pcData[$pcID]['c3']  =$row['c3'];
      $pcData[$pcID]['c4']  =$row['c4'];
      $pcData[$pcID]['c5']  =$row['c5'];
      $pcData[$pcID]['c6']  = $row['c6'];
      $pcData[$pcID]['deleted'] =$row['deleted'];
      $pcData[$pcID]['classname_1'] = $row['classname_1'];
      $pcData[$pcID]['classlevel_1']  = $row['classlevel_1'];
      $pcData[$pcID]['classname_2'] = $row['classname_2'] ?? "N/A";
      $pcData[$pcID]['classlevel_2']  = $row['classlevel_2'] ?: "N/A";
      $pcData[$pcID]['classname_3'] = $row['classname_3'] ?? "N/A" ;
      $pcData[$pcID]['classlevel_3']  = $row['classlevel_3'] ?: "N/A";
      if($quarterID){
        $quarterData[$pcID][$quarterID]['quarter_name']  = $row['quarter_name'];
        $quarterData[$pcID][$quarterID]['public'] = $row['public'];
        $quarterData[$pcID][$quarterID]['for_sale']  = $row['for_sale'];
        $quarterData[$pcID][$quarterID]['sale_price']  = $row['sale_price'] ?? "N/A";
        $quarterData[$pcID][$quarterID]['last_used'] = $row['last_used'];
        $quarterData[$pcID][$quarterID]['last_tax']  = $row['last_tax'];
        $quarterData[$pcID][$quarterID]['area_id'] = $row['area_id'];
      }
      if($factionID){
        $factionData[$pcID][$factionID]['faction_name']  = $row['faction_name'];
        $factionData[$pcID][$factionID]['faction_nation']  = $row['nation'];
        $factionData[$pcID][$factionID]['faction_bank']  = $row['bank'];
      }
    }

    mysqli_stmt_close($characterInfoSqlPrepared);

    $casesSqlPrepared = mysqli_prepare($con,
    "SELECT topic_first_post_id, topic_id, topic_title, forum_id FROM case_f_topics WHERE topic_title LIKE ? ORDER BY forum_id DESC");

    $param = "%" . $CDKey . "%";
    if($casesSqlPrepared){
      mysqli_stmt_bind_param($casesSqlPrepared, "s", $param);
      mysqli_stmt_execute($casesSqlPrepared);

      $result = mysqli_stmt_get_result($casesSqlPrepared);

      // PhPBB has their get vars set as f=forum_id t=topic_id and p=topic_first_post_id. You need all 3 to make the forum link
      while($row = mysqli_fetch_array($result)){
        $forumLink = "http://forum.nwnarelith.com/viewtopic.php?f=" . $row['forum_id'] ."&t=".$row['topic_id']."&p=".$row['topic_first_post_id'];
        $cases[$forumLink] = $row['topic_title'];
      }

      mysqli_stmt_close($casesSqlPrepared);
    }

    mysqli_close($con);

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
<h1 align="center">Case Tool</h1>
<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
  <div class ="key" align = "center">
    CD-Key
    <input type="text" name="CDKey" maxlength="8" required />
    <br><br>
  </div>
  <div class="cutebut">
    <input class = "cutebutt" type="submit" name="submit" value="Submit">
  </div>
</form>
<br />

<br />
<?php

  // Character/Deleted character count, and while we're at it create the Character/Deleted character string for the BBCode Copy/Paste
  // PlayerNames specifically is put into an array so we can remove duplicate values afterwards
  foreach($pcData as $ID => $IDInfo){

    array_push($playerNames, $pcData[$ID]['playername']);

    if($pcData[$ID]['deleted']){

      $pcNamesDeletedString .= "\r\n[color=#FF0000][*]". $pcData[$ID]['pc_name'] . " (Deleted)[/color]";
      $deletedCharacterCount++;

    }else{

      $pcNamesString .= "\r\n[*]". $pcData[$ID]['pc_name'];
      $characterCount++;

    }

  }

  //Remove all duplicate usernames, because if they've used them once that's all we need to know.
  $uniquePlayerNames = array_unique($playerNames);

  foreach($uniquePlayerNames as $pName)
    $playerNamesString .= "\r\n[*]" . $pName;

  //Creating the BBCode a href
  foreach($cases as $fLink => $title)
    $casesString .= "\r\n[*][url=".$fLink."]".$title."[/url]";

//Fun BBCode copy/paste below (not)
  echo
  "<div id=\"bbcode\">
    <textarea id=\"bbcodetext\" rows=\"10\" cols=\"60\">
[b][u]CD-Key:[/b][/u] " . $CDKey . "

[b][u]Character(s):[/b][/u]
[list=1] " . $pcNamesString . $pcNamesDeletedString . "
[/list]

[b][u]Player Name(s):[/b][/u]
[list]  " . $playerNamesString . "
[/list]

[b][u]Summary of Charge(s):[/b][/u]

[b][u]Logs/Screenshots:[/b][/u]

[b][u]Previous Cases for this Player/Character:[/b][/u]
[list] " . $casesString . "
[/list]

[b][u]Decision taken and reasoning:[/b][/u]

[b][u]Transcript from interview with the player:[/b][/u]

[b][u]Follow up notice sent?:[/b][/u]

    </textarea>
  </div>";
 ?>

<div id="keyInfo">
    <div id="playerInfo">
      <table id="playerInfoTable">
        <tr><td><h3>CD-Key</h3></td><td><h3>RPR</h3></td><td><h3>Tells Enabled</h3></td><td><h3>Player ID</h3></td><td><h3>Active Characters</h3></td><td><h3>Deleted Characters</h3></td></tr>
        <?="<tr><td>" . $CDKey . "</td><td>" . $playerRPR . "</td><td>" . $playerTells . "</td><td>" . $playerID . "</td><td>" . $characterCount . "</td><td>" . $deletedCharacterCount . "</td></tr>"?>
      </table>
    </div>
</div>

    <!-- Legend for DMs/Users to know the difference -->
    <div id="orange">Deleted with < 16 levels</div>
    <div id="red">Deleted with > 16 levels</div>


<div id="infoTable">
<?php
  $pcDeleted  = "";

  foreach($pcData as $ID => $IDInfo){

    //Used to differentiate and color both cases
    $pcDeleted = $pcData[$ID]['deleted'] ? "greater_than_fifteen" : "";

    //Not AF, affirmative
    $pcDeletedAf = $pcDeleted ? "Yes" : "No";

    // PhP has type-inference but it was throwing a fuss adding these three values together unless I casted them
    $totalPCLevel = (int)$pcData[$ID]['classlevel_1'] + (int)$pcData[$ID]['classlevel_2'] + (int)$pcData[$ID]['classlevel_3'];


    if($pcDeleted && ($totalPCLevel < 16)){
      $pcDeleted = "less_than_sixteen";
    }

    echo
    "<div class=\"pcinfo " . $pcDeleted ."\">
        <h1 class=\"pcheader\"><strong>" . $pcData[$ID]['pc_name'] . "</strong></h1>
        <table>
        <tr><td><strong>Last Used Playername: </strong>". $pcData[$ID]['playername'] . "</td><td><strong>Bank: </strong>" . $pcData[$ID]['bank'] . "</td>
        <td><strong> Wealth: </strong>" . $pcData[$ID]['wealth'] . "</td><td><strong> Gender: </strong>" . $pcData[$ID]['gender'] . "</td>
        <td><strong> Nation: </strong>" . $pcData[$ID]['nation'] . "</td><td><strong> Subrace: </strong>" . $pcData[$ID]['subrace'] . "</td></tr>

        <tr><td><strong>Carpentry: </strong>" . $pcData[$ID]['c1'] . "</td><td><strong> Cooking: </strong>" . $pcData[$ID]['c2'] . "</td>
        <td><strong> Art Crafting: </strong>" . $pcData[$ID]['c3'] . "</td><td><strong> Forging: </strong>" . $pcData[$ID]['c4'] . "</td>
        <td><strong> Alchemy </strong>" . $pcData[$ID]['c5'] . "</td><td><strong> Tailoring: </strong>" . $pcData[$ID]['c6'] . "</td></tr>

        <tr><td><strong>Class One: </strong>" . $pcData[$ID]['classname_1'] . " " . $pcData[$ID]['classlevel_1'] . "</td>
        <td><strong> Class Two: </strong>" . $pcData[$ID]['classname_2'] . " " . $pcData[$ID]['classlevel_2'] . "</td>
        <td><strong> Class Three: </strong> " . $pcData[$ID]['classname_3'] . " " . $pcData[$ID]['classlevel_3'] . "</td>
        <td></td><td></td><td><strong>Deleted: </strong> " . $pcDeletedAf . "</td></tr>
        </table>";


        foreach($quarterData as $quID => $qID){

          if($quID == $ID){

            foreach($qID as $qFinal){

            $quarterName      = $qFinal['quarter_name'];
            $quarterIsPublic  = $qFinal['public'];
            $quarterIsForSale = $qFinal['for_sale'] ? "Yes" : "No";
            $quarterSalePrice = $qFinal['sale_price'];
            $quarterLastUsed  = $qFinal['last_used'];
            $quarterLastUsedRealTimeUnixTimestamp      = $quarterLastUsed + 1119410000; // Time used + Arelith EPOCH_START = RealTime
            $quarterLastUsedRealFormattedUnixTimestamp = gmdate("Y-m-d\ H:i:s", $quarterLastUsedRealTimeUnixTimestamp);
            $quarterLastTax   = $qFinal['last_tax'];
            $quarterLastTaxRealTimeUnixTimestamp       = $quarterLastTax + 1119410000;
            $quarterLastUsedRealFormattedUnixTimeStamp = gmdate("Y-m-d\ H:i:s", $quarterLastTaxRealTimeUnixTimestamp);
            $quarterAreaID    = $qFinal['area_id'];

          echo
            "<div class=\"quarterinfo\">
              <table>
                <tr><td><strong>Quarter Name: </strong>: $quarterName </td><td><strong>For Sale: </strong> $quarterIsForSale </td><td><strong>Sale Price: </strong> $quarterSalePrice </td><td></td></tr>

                <tr><td><strong>Last Used: </strong> $quarterLastUsedRealFormattedUnixTimestamp ($quarterLastUsed) </td><td><strong>Last Tax: </strong> $quarterLastUsedRealFormattedUnixTimeStamp ($quarterLastTax) </td><td><strong>Area ID: </strong> $quarterAreaID </td><td></td></tr>
              </table>
            </div>";
          }
        }
      }
      foreach($factionData as $fuID => $fID){

        if($fuID == $ID){

          foreach($fID as $fFinal => $fFinalTest){

          $factionName   = $fID[$fFinal]['faction_name'];
          $factionBank   = $fID[$fFinal]['faction_bank'];
          $factionNation = $fID[$fFinal]['faction_nation'];
          $factionID     = $fFinal;

          echo
          "<div class=\"factioninfo\">
          <h2>
            <table>
              <tr><td><strong>Faction Name: </strong><a href=\"faction.php?id=$factionID\"> $factionName </a></td><td><strong>Faction Bank: </strong> $factionBank </td><td><strong>Faction Nation: </strong> $factionNation </td></tr>
            </table>
            </h2>
          </div>";
          }
        }
      }
      echo "</div>";
  }
 ?>
</div>
<div class = "footer">
</div>
</html>
