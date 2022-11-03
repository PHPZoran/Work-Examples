<?php

include 'webhooklogutils.php';

require 'login.php';

//677474315490295818 DM Discord @everyone ID

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta charset="utf-8">
    <title>DM :: Tools</title>
    <!-- Idle handler -->
    <script src="idle.js" type="text/javascript"></script>
    <!--<link href="css/userinfo.css" type= "text/css" rel="stylesheet" />-->
    <link href="css/index.css" type="text/css" rel="stylesheet" />
</head>

    <body>
    <div class="userinfo">
        <strong>User:</strong> <?=$userName;?> <strong>Role:</strong> <?=$group;?>
        <form action="loginhandler.php" method="GET">
          <button class="logout" type="submit" name="logout" value="1"><span>Logout</span></button>
        </form>
    </div>
    <hr />
		<h3>Tools</h3>
    <div class="tools">
      <a class="logs" href="logsearch.php">Light Logs</a>
      <a class="dark" href="logsearch.php?theme=dark">Dark Logs</a>
      </div>
      <br>
      <?php if($_SESSION['isAdmin']){ echo
      "<br>
      <a class=\"weblog\" href=\"weblogsearch.php\">Web Logs</a>
      <br>";} ?>
      <a class="casetool" href="case-tool.php">Case Tool</a>
      <br>
  		<a class="portal" href="portal.php">Portal</a><!--<p>Tool allows you to see where player's location, public CD key, RPR, as well as send people messages by clicking the elipsis and do a one week lookup on them. Players with BLUE names are disguised, Players with RED squares are just 10 RPR people</p>-->
      <br>
    <a class="banpage" href="ban-page.php">Ban-Page</a>
      <br>
  		<a class="QI" href="quarter-tool.php">Quarter Inspection</a><!--<p>Tool for looking at quarters, used to help figure out quarter hoggers, especially people with multi quarters</p>-->
  		<a class="SM" href="server-management.php">Server Management</a><!--<p>Includes start/stop and server shout features</p>-->
      <br>
  	  <a class="ES" href="election-search.php">Election Search</a><!--<p>Tool for searching elections. Set the last day of the election and it'll bring up a list of each candidate, who voted for them and how many estimated lines of RP they had.</p>-->
      <a class="faction" href="faction.php">Faction Tool</a>
      <br>
      <a class="VPN" href="VPN-checker.php">VPN Checker</a><!--<p>Tool for checking associated CDKeys for a certain player. Primarily used if they have been banned previously and are using a VPN to evade their ban.</p>-->
      <br>
      <a class="LN" href="logon-notifier.php">Logon Notifier</a><!--<p>Tool to let a DM get notified via Discord when a CDKey logs onto the server.</p>-->
      <a class="DM" href="dmjump.php">DM Jump Ref</a>
      <br>
  	<br>
    </div>
    <hr />
		<h3>Info</h3>
    <div class="info">
  		<a href="http://forum.nwnarelith.com/viewtopic.php?f=8&t=8278">DM Instruction Manual 2.0</a><br>
  		<a href="http://forum.nwnarelith.com/viewtopic.php?f=8&t=14971">Record of Major DM Decisions</a><br>
  		<a href="http://forum.nwnarelith.com/viewtopic.php?f=9&t=13028&sid=b2272b76d317f74832e7ea4c981aa772">DM Punishment Form</a><br>
  		<a href="http://forum.nwnarelith.com/viewtopic.php?f=8&t=2053">VFX and Creatures</a><br>
    </div>
    </body>
</html>
