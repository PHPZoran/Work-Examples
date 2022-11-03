<?php

include 'webhooklogutils.php';

require 'login.php';

if(parse_ini_file('/web_info/web.ini')){
    $ini = parse_ini_file('/web_info/web.ini');
}else{
    $ini = parse_ini_file('web.ini');
}

  if($userName){
    ArLogStandard($userName, $ClientIPaddress);
    DiscordPageQuery($userName, $ClientIPaddress, $ini['dc_api']);
  }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>DM :: Server Management</title>
    <!-- Idle handler -->
    <script src="idle.js" type="text/javascript"></script>
    <style>
        .contents {
            display: flex;
            flex-flow: row nowrap;
            justify-content: space-around;
        }
		.contents > div {
			text-align: center;
		}
		button {
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
			padding: .375rem .75rem;
			font-size: 1rem;
			line-height: 1.5;
			border-radius: .25rem;
			transition: color .15s ease-in-out,background-color .15s ease-in-out,border-color .15s ease-in-out,box-shadow .15s ease-in-out;
			color: #fff;
			background-color: #17a2b8;
			border-color: #17a2b8;
			margin-bottom: 10px;
			cursor: pointer;
			width: 100%;
		}
		button:hover {
			color: #fff;
			background-color: #138496;
			border-color: #17a2b8;
		}
    </style>
</head>
<h2>Server Management</h2>
<?php

    // Seclib includes
    set_include_path(get_include_path() . PATH_SEPARATOR . 'phpseclib');
    include('Net/SSH2.php');
    include('Crypt/RSA.php');

    // Connect to the remote
    $ee_ssh = new Net_SSH2('ee.nwnarelith.com');
    $arena_ssh = new Net_SSH2('arena.nwnarelith.com');
    $key = new Crypt_RSA();

    $ini = parse_ini_file('/web_info/web.ini');
    // TEST CODE DO NOT PUSH WITH THIS IN IT TODO
    //$ini = parse_ini_file('web.ini');
    // TEST CODE DO NOT PUSH WITH THIS IN IT TODO

    $key->loadKey(file_get_contents($ini['ssh_key']));
    if (!$ee_ssh->login($ini['ssh_user'], $key))
    {
        die('Login Failed');
    }

	if (!$arena_ssh->login($ini['ssh_user'], $key))
    {
        die('Login Failed');
    }

    $remote_path = "/remote_access";

    $remote_kill_script = "server_kill.sh";
    $remote_start_script = "server_start.sh";
    $remote_debug_script = "server_diagnostics.sh";

    list($post_server, $post_command) = explode(";", $_POST['data'], 2);
    $ssh = $post_server == "pgcc-ee" ? $arena_ssh : $ee_ssh;

    if ($post_command == "startup")
    {
        $ssh->exec($remote_path . "/" . $remote_start_script . " " . $post_server);
    }
    else if ($post_command == "shutdown")
    {
        $ssh->exec($remote_path . "/" . $remote_kill_script . " " . $post_server . " INT");
    }
    else if ($post_command == "shutdownforce")
    {
        $ssh->exec($remote_path . "/" . $remote_kill_script . " " . $post_server . " KILL");
    }
    else if ($post_command == "diagnostics")
    {
        $ssh->exec($remote_path . "/" . $remote_debug_script . " " . $post_server);
    }

    ArLogStandard($userName, $ClientIPaddress, $post_command);
    DiscordPageQuery($userName, $ClientIPaddress, $ini['dc_api'], $post_command);

    $remote_status_script = "server_status.sh";

    $remote_status_down = 0;
    $remote_status_up = 1;

    $servers = array("distantshores", "citiesandplanes", "surface", "underdark", "pgcc-ee");
    $serversNames = array("Distant Shores", "Cities and Planes", "Surface", "Underdark", "PGCC Arena");
    $serversIds = array(8, 2, 1, 6, 5);
    echo "<div class='contents'>";
    for ($x = 0; $x < sizeof($servers); $x++)
    {

        $ssh = $servers[$x] == "pgcc-ee" ? $arena_ssh : $ee_ssh;
        $server = $servers[$x];
        $status = $ssh->exec($remote_path . "/" . $remote_status_script . " " . $server);
        $statusStr = $status == $remote_status_down ? "Down" : "Up";
        echo "<div>" ;
        echo "<div>" . $serversNames[$x] . ": " . $statusStr . "</div>";

        if ($status == $remote_status_down)
        {
            $startup_id = $server . ";startup";
            $startup_value = $server . ";startup";

            echo "<form method=\"post\">";
            echo "   <button type=\"submit\" name=\"data\" id=\"$startup_id\" value=\"$startup_value\" >Start</button>";
            echo "</form>";
        }
        else
        {
            $shutdown_id = $server . ";shutdown";
            $shutdown_value = $server . ";shutdown";

            echo "<form method=\"post\">";
            echo "   <button type=\"submit\" name=\"data\" id=\"$shutdown_id\" value=\"$shutdown_value\" >Restart</button>";
            echo "</form>";

            $shutdown_force_id = $server . ";shutdownforce";
            $shutdown_force_value = $server . ";shutdownforce";

            echo "<form method=\"post\">";
            echo "   <button type=\"submit\" name=\"data\" id=\"$shutdown_force_id\" value=\"$shutdown_force_value\" >Shutdown</button>";
            echo "</form>";

            $diagnostics_id = $server . ";diagnostics";
            $diagnostics_value = $server . ";diagnostics";

            echo "<form method=\"post\">";
            echo "   <button type=\"submit\" name=\"data\" id=\"$diagnostics_id\" value=\"$diagnostics_value\" >Save Diagnostics</button>";
            echo "</form>";

			echo "<form method=\"post\">";
			echo "   <button type=\"button\" onclick=\"openShout($serversIds[$x])\">Shout</button>";
			echo "</form>";
        }
        echo "</div>";
    }
    echo "</div>";
?>

<script>
    var openShout = function (server) {
        window.open("send-message.php?server=" + server)
    }
</script>
</html>
