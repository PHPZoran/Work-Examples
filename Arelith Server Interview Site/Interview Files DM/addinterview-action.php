<?php
// include 'webhooklogutils.php';

require 'login.php';
if(!$admin)
  die("not allowed, bad boy");

if(file_exists('/web_info/web.ini')){
  $ini = parse_ini_file('/web_info/web.ini');
}else if(file_exists('../web_info/web.ini')){
  $ini = parse_ini_file('../web_info/web.ini');
}else{
  $ini = parse_ini_file('web.ini');
}

if($_POST['adding']){
	$intPseudonym = $_POST['pseudonym'];
	$forumName = $_POST['forumName'];

  if(file_exists('/web_info/interview.json')){
    $intJson = file_get_contents('/web_info/interview.json');
  }else if(file_exists('../web_info/interview.json')){
    $intJson = file_get_contents('../web_info/interview.json');
  }else{
    $intJson = file_get_contents('interview.json');
  }

	$ints = json_decode($intJson, JSON_OBJECT_AS_ARRAY);

	$ints[$intPseudonym] = $forumName;
	ksort($ints, SORT_STRING);
	$intJson = json_encode($ints);

  if(file_exists('/web_info/interview.json')){
    $intJsonFile = fopen('/web_info/interview.json', 'w');
  }else if(file_exists('../web_info/interview.json')){
    $intJsonFile = fopen('../web_info/interview.json', 'w');
  }else{
    $intJsonFile = fopen('interview.json', 'w');
  }

	fwrite($intJsonFile, $intJson);
	fclose($file);
  // ArLogStandard($userName, $ClientIPaddress, "ADDING\"" . $intPseudonym . "\" \"" . $forumName);
  // DiscordPageQuery($userName, $ClientIPaddress, $ini['dc_api'], "\r\nADDING\r\n`" . $intPseudonym . "`\r\n`" . $forumName . "`");


}else if($_GET['forumrem']){
	$forumName = $_GET['forumrem'];

	$intJson = file_get_contents('dmdiscord.json');
	$ints = json_decode($intJson, JSON_OBJECT_AS_ARRAY);
	// ArLogStandard($userName, $ClientIPaddress, "REMOVAL\"" . $ints[array_search($forumName, $ints)] . "\" \"" . $forumName);
  	// DiscordPageQuery($userName, $ClientIPaddress, $ini['dc_api'], "\r\nREMOVAL\r\n`" . $ints[array_search($forumName, $ints)] . "`\r\n`" . $forumName . "`\r\n");
	unset($ints[array_search($forumName, $ints)]);
	ksort($ints, SORT_STRING);
	$intJson = json_encode($ints);

  if(file_exists('/web_info/interview.json')){
    $intJsonFile = fopen('/web_info/interview.json', 'w');
  }else if(file_exists('../web_info/interview.json')){
    $intJsonFile = fopen('../web_info/interview.json', 'w');
  }else{
    $intJsonFile = fopen('interview.json', 'w');
  }
	fwrite($intJsonFile, $intJson);
	fclose($intJsonFile);

}

header('Location: addinterview.php');