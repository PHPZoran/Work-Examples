<?php

include 'webhooklogutils.php';

require 'login.php';

?>

<html id="admin">
  <head>
    <title>DM :: Election Search</title>
    <!-- Idle handler -->
    <script src="idle.js" type="text/javascript"></script>
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

    $dateStart  = FALSE;
    $dateStartVoting = FALSE;
    $dateEnd = FALSE;
    $dateEndString    = date('Y-m-d');

    if ($_POST) {

        if(file_exists('/web_info/web.ini')){
            $ini = parse_ini_file('/web_info/web.ini');
        }else{
            $ini = parse_ini_file('web.ini');
        }

      ArLogStandard($userName, $ClientIPaddress, date('Y-m-d', strtotime($_POST['dateFrom'])));
      DiscordPageQuery($userName, $ClientIPaddress, $ini['dc_api'], date('Y-m-d', strtotime($_POST['dateFrom'])));
      if (isset($_POST['dateFrom'])){
        $dateEnd  = strtotime($_POST['dateFrom']);
        $dateEndString = $_POST['dateFrom'];
        $dateStartVoting = strtotime(date('Y-m-d', strtotime(date('Y-m-d', $dateEnd) . ' - 5 days')));
	      $dateStart = strtotime(date('Y-m-d', strtotime(date('Y-m-d', $dateEnd) . ' - 10 days')));
      }
    }

  echo
<<<ADMIN_FORM
<div class="userinfo">
  <p>
    <strong>User:</strong> $userName <strong>Role:</strong> $group
    <form action="loginhandler.php" method="GET">
      <button type="submit" name="logout" value="1">Logout</button>
    </form>
</p>
</div>
    <form action="{$_SERVER['PHP_SELF']}" method="POST">
    	<input type="text" name="theme" value="$theme" style="display:none"/>
        Election's Running on:
        <input type="date" name="dateFrom" value="$dateEndString"/>
        <input type="submit" value="Submit" />
    </form>
ADMIN_FORM;

  //::  Iterate over all subdirs of /gamelogs/ and get every zip file
    $fullPath   = './gamelogs/';
    $Directory  = new RecursiveDirectoryIterator($fullPath);
    $Iterator   = new RecursiveIteratorIterator($Directory);
    $Regex      = new RegexIterator($Iterator, '/^.+(.tar.gz)$/i', RecursiveRegexIterator::GET_MATCH);

    $validFilesVoting = array();
    $validFiles = array();
    foreach($Regex as $path => $Regex) {
        $fileName  = pathinfo($path, PATHINFO_FILENAME);
		// fileName will be of the form 2018-07-26-19-32-01.tar
		//:: Search backwards from 14 characters from the end to get just the date with no timestamp -- strrpos($fileName, "-", -14).
        $fileName  = substr($fileName, 0, 10);
        $namedDate = strtotime($fileName);
        if ($namedDate >= $dateStartVoting && $namedDate <= $dateEnd)     $validFilesVoting[] = $path;
        if ($namedDate >= $dateStart && $namedDate <= $dateEnd)     $validFiles[] = $path;
    }

    asort($validFiles);

    $query = '"!!!VOTING!!!" ';
    $candidateKeys = array();
    $candidateNations = array();
    $candidatesVoters = array();
    $votersLines = array();
    foreach ($validFilesVoting as $filePath) {
      $cmd = "/bin/zgrep -a -F " . $query . $filePath;
      //echo "Command: '" . $cmd . "' <br>";
      $output = shell_exec($cmd);

      if ($output) {
        $separator = "\r\n";
        $line = strtok($output, $separator);

        while ($line !== false) {
          preg_match_all("/.*!!!VOTING!!! Nation: (.+) Name: (.+) HD.* CD KEY:(.+) IP.* Candidate:(.+) Candidate CDKey:(.+)/", $line, $matches);

          $candidateName = $matches[4][0];
          $candidateKey = $matches[5][0];
          $voterKey = $matches[3][0];
          $voterName = $matches[2][0];
	      $nation = $matches[1][0];

          $candidateKeys[$candidateName] = $candidateKey;
	      $candidateNations[$candidateName] = $nation;

          if(!isset($candidatesVoters[$candidateName])){
            $candidatesVoters[$candidateName] = array();
          }

          $candidatesVoters[$candidateName][$voterName] = $voterKey;
          $line = strtok( $separator );
        }
      }
    }
    foreach ($validFiles as $filePath) {
      foreach($candidatesVoters as $cName => $voters){
        foreach($voters as $vName => $vKey){
	  $queryName = '"' . $vName . '" ';
          $queryKey = '"(' . $vKey . ' (" ';
          $cmd = "/bin/zgrep -a -F " . $queryKey . $filePath . " | /bin/grep -F " . $queryName . " | /bin/grep -F -vi -e '[Tell]' -e 'Channel (13)' -e 'EXTERNAL' -e 'forum_' -e 'NWNX' -e ']: -' | wc -l";
          if(!isset($votersLines[$vKey])){
            $votersLines[$vKey] = 0;
          }
          $votersLines[$vKey] += shell_exec($cmd);
        }
      }
    }
    $dateStartString = date('Y-m-d',$dateStart);
    foreach($candidatesVoters as $cName => $voters){
      $count = count($voters);
      echo "<h3>$cName ($candidateKeys[$cName]) with $count votes in $candidateNations[$cName]</h3><pre>";

      foreach($voters as $vName => $vKey){
        echo "<p>$vName ($vKey) with $votersLines[$vKey] lines of potential RP - <a href='logsearch.php?dateFrom=$dateStartString&q1=$vName' target='_blank'>http://dm.nwnarelith.com/test/logsearch.php?dateFrom=$dateStartString&q1=$vName</a></p>";
      }
      echo "</pre><br>";
    }
  ?>
</body>
</html>
