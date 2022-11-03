<?php
// include 'webhooklogutils.php';

require 'login.php';
if(!$admin)
  die("not allowed, bad Mord");

if(file_exists('/web_info/web.ini')){
  $ini = parse_ini_file('/web_info/web.ini');
}else if(file_exists('../web_info/web.ini')){
  $ini = parse_ini_file('../web_info/web.ini');
}else{
  $ini = parse_ini_file('web.ini');
}

if(file_exists('/web_info/interview.json')){
  $intJson = file_get_contents('/web_info/interview.json');
}else if(file_exists('../web_info/interview.json')){
  $intJson = file_get_contents('../web_info/interview.json');
}else{
  $intJson = file_get_contents('interview.json');
}

$ints = json_decode($intJson, JSON_OBJECT_AS_ARRAY);
?>
  Add Player
  <form method="post" action="addinterview-action.php">
  	Pseudonym (What DMs will see)
  	<input type="text" name="pseudonym">
  	Forum Name (EXACT)
  	<input type="text" name="forumName">
  	<input type="submit" name="adding">
  </form>

<table style="width:100%">
	<tr>
		<th>Pseudonym</th>
		<th>Forum Name (Exact!!)</th>
		<th>Remove</th>
	</tr>

<?php
foreach($ints as $name => $val)
{
	echo "<tr><td>$name</td><td>$val</td>
		  <td>
		  <form method=\"post\" action=\"addnotify-action.php?forumrem=$val\">
		  <button type=\"submit\" name=\"removing\" method=\"post\" value=\"1\">Remove</tr>
		  </form></tr>";
}

?>
</table>