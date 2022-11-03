<?php

include 'webhooklogutils.php';
//Below two lines enable going back and forth
header('Cache-Control: no cache'); //no cache
session_cache_limiter('private_no_expire'); // works

require 'login.php';

?>
<html id="admin">
  <head>
    <title>DM :: Logs</title>
    <!-- Idle handler -->
 <!--    <script src="idle.js" type="text/javascript"></script> -->
    <style>
        html, body, div { border: 0; font-size: 100%; vertical-align: baseline; }
        body { text-align: justify; line-height: 120%; }
      .server { width: 100%; height: 700px; overflow:auto}
      .dark-theme { background-color: #42474f; color: #b7b7b7; }
    </style>
    <link href="css/userinfo.css" type= "text/css" rel="stylesheet" />
  </head>
<?php $theme = $_GET["theme"] == "dark" || $_POST["theme"] == "1" ? 1 : 0; ?>
<body class="ccm-page <?php echo $theme == 1 ? "dark-theme" : "light-theme" ?>">
<?php
    //ini_set('display_errors', 1);
    //ini_set('display_startup_errors', 1);
    //error_reporting(E_ALL);

if(file_exists('/web_info/web.ini')){
    $ini = parse_ini_file('/web_info/web.ini');
}else{
    $ini = parse_ini_file('web.ini');
}

$yesterday   = date("Y-m-d", time() - 60 * 60 * 24);
$currentDate =  date('Y-m-d');

//$user = !empty($_SERVER['PHP_AUTH_USER']) ? $_SERVER['PHP_AUTH_USER'] : $_SERVER['REMOTE_USER'];
//$isAdmin = $user === "admin";
  $isAdmin = $_SESSION["isAdmin"];

if ($isAdmin) {
    echo '<h2 style="color:#f57972">Admin Mode</h2>';
}

    $queries    =  array();
    $fullPath   = './gamelogs/';
    $dateStart  = FALSE;
    $dateEnd    = FALSE;

    //::  Get query value
    $queries[0] = get_value('q1', "");
    $queries[1] = get_value('q2', "");
    $queries[2] = get_value('q3', "");
    $queries[3] = get_value('q4', "");
    $queries[4] = get_value('q5', "");
    $dateStart = strtotime(get_value('dateFrom', $yesterday));
    $dateEnd = strtotime(get_value('dateTo', $currentDate));

    $dateStartString = date('Y-m-d', $dateStart);
    $dateEndString = date('Y-m-d', $dateEnd);
echo
<<<ADMIN_FORM
  <div class="userinfo">
    <p>
      <strong>User:</strong> $userName <strong>Role:</strong> $group
      <form action="loginhandler.php" method="GET">
        <button type="submit" name="logout" value="1">Logout</button>
      </form>
      <span id="timer"></span>
    </p>
  </div>
    <form action="{$_SERVER['PHP_SELF']}" method="POST">
    	<input type="text" name="theme" value="$theme" style="display:none"/>
        From:
        <input type="date" name="dateFrom" value="$dateStartString"/>
        To:
        <input type="date" name="dateTo" value="$dateEndString" />
        <p>Query</p>
        <input name="q1" id="q1" type="text" maxlength="100" value="{$queries[0]}" /> AND
        <input name="q2" id="q2" type="text" maxlength="100" value="{$queries[1]}"  /> AND
        <input name="q3" id="q3" type="text" maxlength="100" value="{$queries[2]}"  /> AND
        <input name="q4" id="q4" type="text" maxlength="100" value="{$queries[3]}"  /> AND
        <input name="q5" id="q5" type="text" maxlength="100" value="{$queries[4]}"  />
        <input type="submit" value="Submit" />
    </form>
ADMIN_FORM;

    if(checkString($queries[0]) === FALSE && checkString($queries[1]) === FALSE && checkString($queries[2]) === FALSE && checkString($queries[3]) === FALSE && checkString($queries[4]) === FALSE) {
      if(($dateStart === FALSE || $dateEnd === FALSE || $dateStart > $dateEnd) && !($dateStart === FALSE && $dateEnd === FALSE))
          echo "Insert a valid Query, Start and End Date.<br>";

      displayHelpText();
      return;
    }else{
      $queryDateString = "";

      foreach($queries as $query){
        if($query)
        $queryDateString .= "\"" . $query . "\" ";
      }

      $queryDateString .= date('Y-m-d', $dateStart) . "->" . date('Y-m-d', $dateEnd);

      ArLogStandard($userName, $ClientIPaddress, $queryDateString);
      DiscordPageQuery($userName, $ClientIPaddress, $ini['dc_api'], $queryDateString);
    }

    //::  Date ranges bigger than or equal to 61 days will be ignored!
    $daysDiff = $dateEnd - $dateStart;
    if (round($daysDiff / (60 * 60 * 24)) >= 61) {
        echo "Date Range is too big!  Will only show logs from Start date + 61 days.<br>";
        $startDate = date('Y-m-d', $dateStart);
        $dateEnd = strtotime(date('Y-m-d', strtotime($startDate . ' + 61 days')));
    }

    //::  Iterate over all subdirs of /gamelogs/ and get every zip file
    $Directory  = new RecursiveDirectoryIterator($fullPath);
    $Iterator   = new RecursiveIteratorIterator($Directory);
    $Regex      = new RegexIterator($Iterator, '/^.+(.tar.gz)$/i', RecursiveRegexIterator::GET_MATCH);

    //::  Sort out only the zip files that are within the date range (Go by filename as we have copied over old logs)
    $validFiles = array();
    foreach($Regex as $path => $Regex) {
        $fileName  = pathinfo($path, PATHINFO_FILENAME);
		// fileName will be of the form 2018-07-26-19-32-01.tar
		//:: Search backwards from 14 characters from the end to get just the date with no timestamp -- strrpos($fileName, "-", -14).
        $fileName  = substr($fileName, 0, 10);
        $namedDate = strtotime($fileName);

        if ($namedDate >= $dateStart && $namedDate <= $dateEnd)     $validFiles[] = $path;
    }

    asort($validFiles);

    $addon   = '';
    $query   = '';
	echo "Searching on query: '" . $queries[0] . "' ";

    for ($i = 0; $i < count($queries); $i++) {
        $q = str_replace("|","\|",$queries[$i]);

        if (strlen($q) < 3) {
			if(trim($q) !== ''){
				echo "Query: " . $q . " was too short, skipping.</br>";
			}
            continue;
        }

		if($i > 0){
			echo " AND '" . $queries[$i] . "' ";
		}

        if($query === ''){
          $query = '"' . $q . '" ';
        } else {
          $addon = $addon . ' | /bin/grep -i "' . $q . '" ';
        }
    }
	echo "<br>";

    //::  Go over all valid files and zgrep the query from them
    if ($isAdmin) {
        $filterCmd = "-e 'Channel (13)' -e 'EXTERNAL' -e 'forum_'";
    } else {
        $filterCmd = "-e '[Tell]' -e 'Channel (13)' -e 'EXTERNAL' -e 'forum_' -e 'NWNX'";
    }

    $lastServer = "";
    $currentServer = "";
    foreach ($validFiles as $filePath) {
        $cmd = "/bin/zgrep -i -a " . $query . $filePath . $addon . " | /bin/grep -F -vi " . $filterCmd;   //::  Exclude Tell logging, Channel 13 and forum_*
	//	echo "Command: '" . $cmd . "' <br>";
        $output = shell_exec($cmd);

        if ($output) {
            $currentServer = getServerName($filePath);
            if ($lastServer !== $currentServer) {
                if($lastServer !== "")
                    echo "</div>";
                $lastServer = $currentServer;
                echo "<br>" . $lastServer;
                echo "<div class='server'>";
            }
            echo "<pre>$output</pre><br>";
        }
    }

  if($lastServer == $currentServer && $currentServer != "")
    echo "</div>";
  if($currentServer == "")
    echo "No logs from query.";

	displayHelpText();

  function checkString($query){
      return $query !== '' || strlen($query) >= 3 ;
  }

    function getServerName($path) {
        $serverName = "Cordor & Planes";
        if (strpos($path, 'distantshores') !== false) {
            $serverName = "Distant Shores";
        } else if (strpos($path, 'surface') !== false) {
            $serverName = "Surface";
        } else if (strpos($path, 'underdark') !== false){
            $serverName = "Underdark";
        }

        $namedDate  = pathinfo($path, PATHINFO_FILENAME);
        $namedDate  = substr($namedDate, 0, strrpos($namedDate, "-"));
        $namedDate  = date('M jS', strtotime($namedDate));

        return "<h2>" . $serverName . "</h2>";
    }
  function get_value($valueName, $defaultValue){
    $getValue = isset($_GET[$valueName]) ? $_GET[$valueName] : "";
    $postValue = isset($_POST[$valueName]) ? $_POST[$valueName] : "";

    if($getValue != "")
      return $getValue;
    if($postValue != "")
      return $postValue;
    return $defaultValue;
   }

	function displayHelpText(){
		echo "<br>
			<p>ENTER -- Checks characters entering an area (can filter on area res ref / player name)</p>
			<p>FIXTURES -- Checks who picked up, placed and destroyed a fixture (character name / fixture name / FIXTURE [for all])</p>
			<p>QUARTER -- Checks who touched their quarter to refresh (Character Name / Quarter Tag)</p>
			<p>SHOP -- Checks who touched their shop to refresh (Character Name / Shop Tag)</p>
			<p>CONTAINER -- Checks who accessed a storage chest (Character Name / Chest Tag)</p>
			<p>AWIA -- A werewolf in Arelith - who turned (character name / AWIA)</p>
			<p>CITIZENSHIP -- Who purchased citizeship in a settlement (Character Name / Nation Name)</p>
			<p>TRAPS -- Checks who triggered trap (character Name / TRAPS)</p>
			<p>DEATH -- Character died (PvP or PvE / Character Name)</p>
			<p>MESSENGERS -- Who sent a message (Search by MESSENGERS or message context - have to ID player by Player ID #)</p>
			<p>ASSASSIN WARNING -- Who received it and who placed it (Player ID # / Character Name)</p>
			<p>LEADER -- Who performed settlement powers (eviction of quarter / shop) (Character Name / Tag of Quarter or Shop)</p>
			<p>ACQUIRE -- Who picked up something due to muling (IP / CD Key / Character Name)</p>
			<p>Crafting Log: --  Same player craft attempt (Character Name)</p>
			<p>QUEST ITEM -- Quest items</p>
			<p>!!!VOTING!!! -- Checks who voted from what CD Key for who in an election (Search by !!!Voting!!! - check for similar CD Keys / Lack of RP)</p>
			<p>TRADE_CZAR — Detects who bought or sold to the expanded warehouse.</p>
            <p>FINANCE -- Detects when a player has several characters within a faction and attempts a withdrawal.</p>
            <p>LANDBROKER -- Views details on landbids. \"DM Hoodoo has bid 5 million hoodoo coins on Darrowdeep\"</p>";
	}
?>
</body>
</html>
