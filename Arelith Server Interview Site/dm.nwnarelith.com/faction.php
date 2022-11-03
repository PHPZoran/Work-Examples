<?php

include 'webhooklogutils.php';

//Below two lines enable going back and forth
header('Cache-Control: no cache'); //no cache
session_cache_limiter('private_no_expire'); // works


require 'login.php';

/** Repurposed from case-tool.php :- DM Hoodoo
 * Note: Exposed GET variable id=(FACTIONID)
 * To remotely use this form
 */
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>DM :: Factions</title>
  <!-- Idle handler -->
  <script src="idle.js" type="text/javascript"></script>
  <link href="css/faction.css" type= "text/css" rel="stylesheet" />
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

$x = 0;

$factionData = array();

//$errs handles the error to be printed out
$errs = "";

$dbUpdated = FALSE;

//$succMess works same as $errs see above. Except it's green!
$succMess = "";

//Need emptyStr as a variable for mysqli_stmt_bind_params. Don't change.
$emptyStr = "";

$factionID = "";
$factionName = "";
$factionBank = "";
$factionNation = "";
$factionMemberCount = NULL;

$factionPCData = array();

if ($_SERVER["REQUEST_METHOD"] == "POST" || $_SERVER["REQUEST_METHOD"] == "GET") {


  //All CD-Keys are 8 characters long. Don't bother processing if it isn't that long.
  if (($_GET['id'] || $_POST['FactionName']) && $userName) {



    $factionInputID = trim($_GET['id']);

    $factionInputName = $_POST['FactionName'];

      if(file_exists('/web_info/web.ini')){
          $ini = parse_ini_file('/web_info/web.ini');
      }else{
          $ini = parse_ini_file('web.ini');
      }

    ArLogStandard($userName, $ClientIPaddress, $_GET['id'] ? $_GET['id'] : $_POST['FactionName']);
    DiscordPageQuery($userName, $ClientIPaddress, $ini['dc_api'], $_GET['id'] ? $_GET['id'] : $_POST['FactionName']);

    $con = mysqli_connect($ini['db_ip'], $ini['db_user'], $ini['db_password'], $ini['db_name']);

    if(!$con){
      echo "Error: Unable to connect to MySQL.";
      echo "Debugging errno: " . mysqli_connect_errno();
      echo "Debugging error: " . mysqli_connect_error();
      exit;
    }

    if($factionInputID){
    $factionSqlPrepared = mysqli_prepare($con, "SELECT fac.id, fac.name, fac.nation, fac.bank, facmem.rank_id, facmem.is_ownerRank,
      pc.name AS pc_name, pc.keydata, pc.id AS pc_id, player.cdkey FROM md_fa_factions AS fac INNER JOIN md_fa_members AS facmem ON fac.id=facmem.faction_id INNER JOIN gs_pc_data AS pc ON
      facmem.pc_id=pc.id INNER JOIN gs_player_data AS player ON pc.keydata=player.id WHERE fac.id=? ORDER BY pc.keydata DESC");
    mysqli_stmt_bind_param($factionSqlPrepared, "i", $factionInputID);
  }else if($factionInputName){
    $factionSqlPrepared = mysqli_prepare($con, "SELECT fac.id, fac.name, fac.nation, fac.bank, facmem.rank_id, facmem.is_ownerRank,
      pc.name AS pc_name, pc.keydata, pc.id AS pc_id, player.cdkey FROM md_fa_factions AS fac INNER JOIN md_fa_members AS facmem ON fac.id=facmem.faction_id INNER JOIN gs_pc_data AS pc ON
      facmem.pc_id=pc.id INNER JOIN gs_player_data AS player ON pc.keydata=player.id WHERE fac.name=? ORDER BY pc.keydata DESC");
    mysqli_stmt_bind_param($factionSqlPrepared, "s", $factionInputName);
  }

    mysqli_stmt_execute($factionSqlPrepared);

    $result = mysqli_stmt_get_result($factionSqlPrepared);

    while($row = mysqli_fetch_array($result)){
        if(!$x){
        $factionID = $row['id'];
        $factionName = $row['name'];
        $factionNation = $row['nation'];
        $factionBank = $row['bank'];
        $x++;
      }
      $pcID = $row['pc_id'];
      $factionPCData[$pcID]['rank_id'] = $row['rank_id'];
      $factionPCData[$pcID]['is_ownerRank'] = $row['is_ownerRank'];
      $factionPCData[$pcID]['pc_name'] = $row['pc_name'];
      $factionPCData[$pcID]['keydata'] = $row['keydata'];
      $factionPCData[$pcID]['cdkey'] = $row['cdkey'];
    }

    $factionMemberCount = count($factionPCData);

    mysqli_stmt_close($factionSqlPrepared);

    mysqli_close($con);

    }else{
      $errs .= " Invalid CD-Key. Too short.";
    }
}

$errs = trim($errs);


?>
<body>
<div class="userinfo">
  <p>
    <strong>User:</strong> <?=$userName;?> <strong>Role:</strong> <?=$group;?>
    <form action="loginhandler.php?logout=1" method="GET">
      <button class="cutebutt" type="submit" name="logout" value="1">Logout</button>
    </form>
</p>
</div>
<h1 align="center">Faction Tool</h1>
<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
  <div class ="name" align = "center">
    <br>
    Faction Name (Exact):
    <br><br>
    <input type="text" name="FactionName" required />
    <br><br>
  </div>
  <div class="cutebut">
    <input class = "cutebut" type="submit" name="submit" value="Submit">
  </div>
</form>
<br />

<br />
<div id="factionInfo">
    <div id="facInfo">
      <table id="facInfoTable">
        <tr><td><h3><strong>ID<strong></h3></td><td><h3><strong>Name</strong></h3></td><td><h3><strong>Nation</strong></h3></td><td><h3><strong>Bank</strong></h3></td><td><h3><strong>Members</strong></h3></td></tr>
        <?="<tr><td>" . $factionID . "</td><td>" . $factionName. "</td><td>" . $factionNation . "</td><td>" . $factionBank. "</td><td>" . $factionMemberCount . "</td></tr>"?>
      </table>
    </div>
</div>
<div id="infoTable">

<?php
  echo
    "
    <div class=\"memberData\">
      <table>
      <tr><td><h1><strong>CD-Key</strong></h1></td><td><h1><strong>Key ID</strong></h1></td><td><h1><strong>Name</strong></h1></td><td><h1><strong>Rank</strong></h1></td><td><h1><strong>Owner?</strong></h1></td></tr>";
  foreach($factionPCData as $ID => $IDInfo){
    $factionIsOwner = $factionPCData[$ID]['is_ownerRank'] ? "Yes" : "No";
    $factionMemberCDKey = $factionPCData[$ID]['cdkey'];

    echo
    "<tr><td><a href=\"case-tool.php?key=" . $factionMemberCDKey ."\"><h3>". $factionMemberCDKey . "</h3></a></td>
    <td><h3>" . $factionPCData[$ID]['keydata'] . "</h3></td><td><h3>" . $factionPCData[$ID]['pc_name'] . "</h3></td><td><h3>".$factionPCData[$ID]['rank_id']."</h3></td>
      <td><h3>" . $factionIsOwner . "</h3></td></tr>
      ";
  }
  echo
  " </table>
  </div>";
 ?>
</div>
</body>
  <div class = "footer">
  </div>
  </html>
