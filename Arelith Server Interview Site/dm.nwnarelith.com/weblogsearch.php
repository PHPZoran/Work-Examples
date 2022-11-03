<?php

include 'webhooklogutils.php';
//Below two lines enable going back and forth
header('Cache-Control: no cache'); //no cache
session_cache_limiter('private_no_expire'); // works

require 'login.php';

if(!$_SESSION['isAdmin']){
  session_destroy();
  if($_SERVER['QUERY_STRING']){
    header('Location: loginhandler.php?logout=1&refUrl=' . basename($_SERVER["PHP_SELF"]) . "?" . $_SERVER['QUERY_STRING']);
  }else{
  header('Location: loginhandler.php?logout=1&refUrl=' . basename($_SERVER["PHP_SELF"]));
  }
}

?>
<html id="admin">
  <head>
    <title>Admin :: Web Log Search</title>
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

$yesterday   = date("Y-m-d", time() - 60 * 60 * 24);
$currentDate =  date('Y-m-d');

//$user = !empty($_SERVER['PHP_AUTH_USER']) ? $_SERVER['PHP_AUTH_USER'] : $_SERVER['REMOTE_USER'];
//$isAdmin = $user === "admin";
  $isAdmin = $_SESSION["isAdmin"];

if ($isAdmin) {
    echo '<h2 style="color:#f57972">Admin Mode</h2>';
}

    $queries    =  array();
    $fullPath   = "/web_info/DMLogs";
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
    $Regex      = new RegexIterator($Iterator, '/^.+(.txt)$/i', RecursiveRegexIterator::GET_MATCH);

    //::  Sort out only the zip files that are within the date range (Go by filename as we have copied over old logs)
    $validFiles = array();
    foreach($Regex as $path => $Regex) {
        $fileName  = pathinfo($path, PATHINFO_FILENAME);
		// fileName will be of the form 2018-07-26.txt
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
    foreach ($validFiles as $filePath) {
        $cmd = "/bin/zgrep -i -a " . $query . $filePath . $addon;

        $output = shell_exec($cmd);

        if ($output) {
                    echo "</div>";
                echo "<div class='weblog'>";
            }
            echo "<pre>$output</pre><br>";
    }


    echo "</div>";

    if(!$output)
    echo "No logs from query.";

	displayHelpText();

  function checkString($query){
      return $query !== '' || strlen($query) >= 3 ;
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
		echo "<br>";

	}
?>
</body>
</html>
