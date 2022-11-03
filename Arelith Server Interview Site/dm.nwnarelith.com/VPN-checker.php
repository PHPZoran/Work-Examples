<?php

include 'webhooklogutils.php';

require 'login.php';

?>

<html id="admin">
  <head>
    <title>DM :: VPN Checker</title>
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

  <div class="userinfo">
    <p>
      <strong>User:</strong> <?=$userName;?> <strong>Role:</strong> <?=$group;?>
      <form action="loginhandler.php" method="GET">
        <button type="submit" name="logout" value="1">Logout</button>
      </form>
  </p>
  </div>



<?php
/*ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);*/

/* The basic logic of this tool follows most of the examples of DM Apollo's election-search tool.
The tool strays away from functionality when it comes to Array structure and querying. Details are
available where implemented*/


if(file_exists('/web_info/web.ini')){
    $ini = parse_ini_file('/web_info/web.ini');
}else{
    $ini = parse_ini_file('web.ini');
}

$dateStart  = FALSE;
$dateEnd = FALSE;
$dateEndString = date('Y-m-d');



// Set default value for date input and parse it for use.
if ($_POST) {
  ArLogStandard($userName, $ClientIPaddress, $_POST["CDKey"] . " " . date('Y-m-d', strtotime($_POST['dateFrom'])));
  DiscordPageQuery($userName, $ClientIPaddress, $ini['dc_api'], "\r\n".$_POST["CDKey"] . "\r\n" . date('Y-m-d', strtotime($_POST['dateFrom'])));
  if (isset($_POST['dateFrom'])){
    $dateEnd  = strtotime($_POST['dateFrom']);
    $dateEndString = $_POST['dateFrom'];
    $dateStart = strtotime(date('Y-m-d', strtotime(date('Y-m-d', $dateEnd) . ' - 30 days')));
  }
}

echo
<<<ADMIN_FORM
<div><p><strong>User:</strong> $userName</p></div>
<div><p><b>NOTE: Before using this tool, be sure to input some of their IPs through <a href="https://getipintel.net/#web">here</a>.</b></p>
  <ul>
    <li><p><i> Value of <b>0.3 or less for all IPs found.</b> Pretty unlikely. <b>Don't use this tool.</b></i></p></li>
    <li><p><i> Value of <b>0.7 to 0.3 for most IPs found.</b> Likely. <b>Use this tool.</b></i></p></li>
    <li><p><i> Value of <b>0.8+ for ANY IP.</b> Very likely. <b>Use this tool.</b></i></p></li>
  </ul>
  </div>
  <hr />
  <form action="{$_SERVER['PHP_SELF']}" method="POST">
    <input type="text" name="theme" value="$theme" style="display:none"/>
      <p>From Date: (Checks from one month before date)</p>
      <input type="date" name="dateFrom" value="$dateEndString"/>
      <p>CDKey to check:</p>
      <input type="text" name="CDKey" value="{$_POST["CDKey"]}" maxlength = "8"/>
      <input type="submit" value="Submit"/>
  </form>
ADMIN_FORM;


//::  Iterate over all subdirs of /gamelogs/ and get every zip file
  $fullPath   = './gamelogs/';
  $Directory  = new RecursiveDirectoryIterator($fullPath);
  $Iterator   = new RecursiveIteratorIterator($Directory);
  $Regex      = new RegexIterator($Iterator, '/^.+(.tar.gz)$/i', RecursiveRegexIterator::GET_MATCH);

  $validVPNFiles = array();
  $validFiles = array();
  foreach($Regex as $path => $Regex) {
      $fileName  = pathinfo($path, PATHINFO_FILENAME);
  // fileName will be of the form 2018-07-26-19-32-01.tar
  //:: Search backwards from 14 characters from the end to get just the date with no timestamp -- strrpos($fileName, "-", -14).
      $fileName  = substr($fileName, 0, 10);
      $namedDate = strtotime($fileName);
      if ($namedDate >= $dateStart && $namedDate <= $dateEnd)     $validVPNFiles[] = $path;
  }

  asort($validVPNFiles);


          $CDKey = NULL;

          //Initialising Test file for use in testing environment
          $query = $_POST["CDKey"];
          $originalCDKey = $query;

          // Regex to retrieve and group player information.
          $playerInfoRegex = "/.*\[(.+)\] .*Player: (.+), CD key: (.+), IP: (.+), PC: (.+).*/";

          //Trim query in case user enters spaces
          $query = trim($query);

          //$potentialMatches used to store CDKey and list of IPs with Timestamps.
          $potentialMatches = array();


foreach ($validVPNFiles as $filePath) {

      $query = $originalCDKey;



      $cmd = "/bin/zgrep -a -F '" . $query . ", IP:' ". $filePath; //:: Linux Command

      $output = shell_exec($cmd);


      //$currentIPs used to prevent duplicate IPs from being stored.
      $currentIPs = array();


      if ($output) {


        $separator = "\r\n";
        $line = strtok($output, $separator);

        while ($line) {
        preg_match_all($playerInfoRegex, $line, $matches);

          //If there is no valid IP, check next line.
          if(isset($matches[4][0])){



          $timeStamp = $matches[1][0];
          $CDKey = $matches[3][0];
          $workingIp = $matches[4][0];



          // Compile IPs for single CDKey
          /* For the potentialMatches array, the CDKey is a key that points to an array of IPs, which are also keys.
          Every IP key points to a "Timestamp" key which stores the Timestamp of the IP used*/
          if(!in_array($workingIp,$currentIPs)){
            $potentialMatches[$CDKey][$workingIp]["Timestamp"] = $timeStamp;
            $currentIPs[] = $workingIp;
          }

        }


          $line = strtok( $separator );
        }
        //Unset storage variable for use later.
        unset($currentIPs);

      }
  }

  $matchingCDKeys = array();

  foreach($validVPNFiles as $filePath){


        // Query with all IPs from CDKey
        if(isset($potentialMatches[$CDKey])){


        $query = "";

        $query = join("|",array_keys($potentialMatches[$CDKey]));

        $cmd = "/bin/zgrep -a -E '" . $query . "' " . $filePath; //:: Linux Command

        $output = shell_exec($cmd);


        if ($output) {

          $currentIPs = array();
          $separator = "\r\n";
          $line = strtok($output, $separator);

          while ($line !== false) {

            preg_match_all($playerInfoRegex, $line, $matches);

                if(isset($matches[4][0])){


                $workingIp = $matches [4][0];
                $secondaryCDKey = $matches[3][0];
                $timeStamp = $matches[1][0];



                /* Look through all the IPs of all potential matches and check them against the IP per player. If it's the same CDKey, ignore it.
                 If it's the same IP but a different CDKey. Log it into $matchingCDKeys for printing along with timestamp.*/
                foreach($potentialMatches as $workingCDKey => $IPArr){
                  foreach($IPArr as $realIP => $TS){
                if(array_key_exists($workingIp,$IPArr) && ($workingCDKey !== $secondaryCDKey)){
                  if(!in_array($workingIp, $currentIPs)){
                  $potentialMatches[$secondaryCDKey][$workingIp]["Timestamp"] = $timeStamp;
                  $currentIPs[] = $workingIp;
                }



                /* ***SAME IMPLEMENTATION OF ARRAY USED FOR potentialMatches***
                For the $matchingCdKeys array, the CDKey is a key that points to an array of matched CDKeys, which are also keys.
                Every matched CDKey key points to an array of IPs, which are also keys.
                Each IP points to a "Timestamp" key which stores the Timestamp of the IP used*/
                $matchingCDKeys[$workingCDKey][$secondaryCDKey][$workingIp]["Timestamp"] = $timeStamp;




                  }
                }
              }
            }

              $line = strtok( $separator );
          }

    }
  }
}

/* Print out all matching keys with format:
XXXXXXXXX is associated width
KEY1 with IP: $IP at TimeStamp: $TimeStamp
KEY2 with IP: $IP at TimeStamp: $TimeStamp
etc.*/
foreach($matchingCDKeys as $CDKey => $RCArr){
  echo "<hr />";
  echo "<div><p><strong><h2 align=\"left\"> $CDKey is associated with </h2></strong></p></div>";
    foreach($RCArr as $secondKey => $IPArr){
      foreach($IPArr as $IP => $TS){
        echo  "<div><p><strong> $secondKey </strong>with <strong>IP: $IP </strong> at <strong>Timestamp: " . $TS["Timestamp"] . "</strong></p></div>";
          }
    }
}
?>
</html>
