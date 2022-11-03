<?php

include 'webhooklogutils.php';

require 'login.php';

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>DM :: Send Message</title>
    <!-- Idle handler -->
    <script src="idle.js" type="text/javascript"></script>
</head>

<?php
	$charName = get_value('charName', "Unset");
	$playerName = get_value('playerName', "Unset");
	$dm = $_POST['dm'];
	$pcid = intval(get_value('pcid', -1));
	$server = intval(get_value('server', "-1"));

	$comment = "";
	$messageSentSuccessfully = false;

	if ($_SERVER["REQUEST_METHOD"] == "POST") {
		$pcIdToUse = $pcid == -1 ? NULL : $pcid;
		echo 'Attempting to send message to: "pcid-' . $pcIdToUse . '" server-"' . $server . '"';


		if (!empty($_POST["comment"]) && $server >= 0) {
			$comment = test_input($_POST["comment"]);
			echo 'Message to send is: "' . $comment . '"';
			$comment = $comment . " //" . $dm;
			$messageType = $pcid == -1 ? 'WEBAPP_DMSHOUT' : 'WEBAPP_DMTOPLAYER';
			$ini = parse_ini_file('/web_info/web.ini');
			$con = mysqli_connect($ini['db_ip'], $ini['db_user'], $ini['db_password'], $ini['db_name']);
			$sendMessagePrepared = mysqli_prepare($con, "INSERT INTO mixf_messages (pcid, server, message, type) VALUES (?, ?, ?, ?)");
			mysqli_stmt_bind_param($sendMessagePrepared, "iiss", $pcIdToUse, $server, $comment, $messageType);

			mysqli_stmt_execute($sendMessagePrepared);
			mysqli_stmt_close($sendMessagePrepared);

			mysqli_close ($con);
			$messageSentSuccessfully = true;
		}

	}

	function test_input($data) {
		$data = trim($data);
		$data = str_replace("'", "''", $data);
		return $data;
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
?>
<div><p><strong>User:</strong> <?=$userName;?></p></div>
<h2>Send Message to Player</h2>
<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
	Name: <?php echo $charName;?> <input type="hidden" name="charName" value="<?php echo $charName;?>">
	<br><br>
	Player Name: <?php echo $playerName;?> <input type="hidden" name="playerName" value="<?php echo $playerName;?>">
	<br><br>
	PcId: <?php echo $pcid;?> <input type="hidden" name="pcid" value="<?php echo $pcid;?>">
	<br><br>
	Server: <?php echo $server;?> <input type="hidden" name="server" value="<?php echo $server;?>">
	<br><br>
	DM Name: <input type="text" name="dm" value="<?php echo $dm;?>" required>
	<br><br>
	Message: <textarea name="comment" rows="5" cols="60" maxlength="2000" required></textarea>
	<br><br>
	<input type="submit" name="submit" value="Submit">
</form>
<?php
	if($messageSentSuccessfully == true)
		echo 'Message Sent!';
?>
</html>
