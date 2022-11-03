<?php

include 'webhooklogutils.php';

require 'login.php';

?>
<!DOCTYPE html>
<html lang="en">
    <head>
      <!-- Idle handler -->
      <script src="idle.js" type="text/javascript"></script>
        <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400" rel="stylesheet">
        <link rel="stylesheet" type="text/css" href="http://portal.nwnarelith.com/main.css">
        <link rel="stylesheet" type="text/css" href="css/portal.css">
		<link rel="stylesheet" type="text/css" href="/contextMenu.css">

		<style>
			#portal-contents{
				top: -50px;
			}
			.portal-player-list {
				width: 400px;
			}
			.player-row.disguised .character-name {
    				background-color: lightgrey;
			}
			.player-row.low-rpr {
			    background-color: #ffdbb6;
			}

			.dark-theme .player-row.low-rpr {
			    background-color: darkred;
			}

			.dark-theme .player-row.disguised .character-name {
				background-color: darkblue;
			}

			.dark-theme .player-row a {
				color: white;
			}

			#send-message-popup{
				width: 250px;
				height: 150px;
				text-align: center;
			}
			#send-message-popup textarea{
				width: 233px;
				height: 126px;
			}
		</style>
    </head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1.0">
<!--<meta http-equiv="refresh" content="10" > -->
    <title>DM :: Portal</title>

    <body class="ccm-page dark-theme">
        <div id="portal-contents">
            <?php
            if(file_exists('/web_info/web.ini')){
                $ini = parse_ini_file('/web_info/web.ini');
            }else{
                $ini = parse_ini_file('web.ini');
            }
                if($userName){
                ArLogStandard($userName, $ClientIPaddress);
                DiscordPageQuery($userName, $ClientIPaddress, $ini['dc_api']);
                }
                $con = mysqli_connect($ini['db_ip'], $ini['db_user'], $ini['db_password'], $ini['db_name']);
                $webServerSql = 'SELECT sid, name, pub_address, port_nwn, curr_players, curr_dms, state, startup
                                 FROM web_server
                                 WHERE sid=1 OR sid=2 OR sid=6 OR sid=8';
                //TODO temporary patch for above. switch back to visible_on_portal when redesign is finished.
                $webServerQuery  = mysqli_query($con, $webServerSql) or die(mysqli_error($con));
                $webResults = array();
                while($row = mysqli_fetch_array($webServerQuery)) {
                    $row['active'] = count($webResults) < 4 ? 'active' : '';
                    $webResults[] = $row;
                }
                $len = count($webResults);

                echo "<div class='filters'>";
                    for($x = 0; $x < $len; $x++) {
                        $wrow = $webResults[$x];
                        echo "<button class='filter-button ". $wrow['active']  . "' onclick='toggleFilter(" . $wrow['sid'] . ")' id='list-filter-" . $wrow['sid'] . "'>" . $wrow['name'] . "</button>";
                    }
                echo "</div>";

                echo "<div class='contents'>";
                    for($x = 0; $x < $len; $x++) {
                        $wrow = $webResults[$x];

                        $playerCount = ($wrow['curr_players'] < 250 ? $wrow['curr_players'] . " / 250" : "Full") . "(" . $wrow['curr_dms'] . " DMs)";
                        switch ($wrow['state']) {
                            case 2:
                            case 3:
                                $state = "Restarting";
                                break;
                            case 5:
                                $state = "Stabilizing";
                                break;
                            case 6:
                            case 7:
                                $state = "Restart Signalled";
                                break;
                            case 8:
                                $state = "Online";
                                break;
                            default:
                                $state = "Offline";
                                break;
                        }
                        $duration =  time() - $wrow['startup'];
                        $numDays = floor($duration / 86400);
                        $remainder = $duration % 86400;
                        $time = gmdate("H:i:s",$remainder);
                        $days = $numDays < 1 ? "" : ($numDays < 2 ? "1 day " : $numDays . " days ");

                        echo "<div class='portal-player-list " . $wrow['active'] . "' id='list-" . $wrow['sid'] . "'>";
                            echo "<table class='player-list-header' id='player-" . $wrow['sid'] . "-list'>";
                                echo "<thead>";
                                    echo "<tr><th><h2 class='server-name'>" . $wrow['name'] . "</h2></th></tr>";
                                    echo "<tr><td>" . $wrow['pub_address'] . ":" . $wrow['port_nwn'] . " | " . $state . " | " . $days . $time
                                    . " | " . $playerCount . "</td></tr>";
                                echo "</thead>";
                                echo "<tbody>";
                                    $playerSql = 'SELECT pcid, server, visiblename, name, current_playername, current_location, subrace, cdkey, rp, pc.options
                                    FROM mixf_currentplayers cp
									INNER JOIN gs_pc_data pc ON cp.pcid = pc.id
									INNER JOIN gs_player_data pd ON pc.keydata = pd.id
                                    WHERE server = ' . $wrow['sid'] . '
                                    ORDER BY visiblename ASC';
                                    $playerQuery  = mysqli_query($con, $playerSql) or die(mysqli_error($con));

									while ($prow = mysqli_fetch_array($playerQuery))
									{
										$isDisguised = $prow['visiblename'] !== $prow['name'];

										$visibleName = str_replace("~", "'", html_entity_decode($prow['visiblename'], ENT_QUOTES));
															$characterName = str_replace("~", "'", html_entity_decode($prow['name'], ENT_QUOTES));
															$playerName = str_replace("~", "'", html_entity_decode($prow['current_playername'], ENT_QUOTES));
										$areaStart = 6;
										$areaEnd = strrpos($prow['current_location'], "#POSITION_X", $areaStart + 1);
										$currentLocation = substr($prow['current_location'], $areaStart, $areaEnd - $areaStart);
										$rpr = $prow['rp'] - 1;
										$needsDM = (($prow['options']&2)==2) ? 1 : 0;
										$rowClass = "player-row";
										if($isDisguised == true)
											$rowClass .= " disguised";
										if($rpr < 19)
											$rowClass .= " low-rpr";

										echo "<tr class='" . $rowClass . "'>";
										echo "<td>"
											. "<span class='character-name'>". $visibleName . " / ". $characterName . "</span><br>"
											. $currentLocation; if($needsDM){ echo "<div class='badge'><div class=\"img-description\">Badge</div><img src='css/Images/DM.png'></img></div>";} echo "<br>"
											. "<span class='player-name'>" . $playerName . " (" . $prow['cdkey'] . ")</span>"
											. " [" . $rpr . "]<br>"
											. "<a href='#' data-pcid='" . $prow['pcid'] . "' data-server='" . $prow['server'] . "' data-cdkey='" . $prow['cdkey'] . "' data-player-name='" . $playerName . "' data-char-name='" . $characterName . "'>...</a>"
										. "</td>";
										echo "</tr>";
									}
								echo "</tbody>";
							echo "</table>";
						echo "</div>";
                    }
                echo "</div>";
                mysqli_close ($con);
            ?>
        </div>

        <div id="footer">
            <p><a href="/">Arelith</a> <br>__</p>
            <p>An online persistent role playing world</p>
            <p>Built in Neverwinter Nights</p>
            <p><a href="http://nwnarelith.com/index.php/promote">Promote!</a> | <a href="http://nwnarelith.com/index.php/faq">Contact Us</a> | <a href="https://www.patreon.com/arelith">Donate</a></p>
        </div>
    </body>
	<script src="https://code.jquery.com/jquery-3.3.1.min.js" integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8=" crossorigin="anonymous"></script>
	<script src="/contextMenu.js"></script>
	<script>
		function toggleFilter(sid) {
            document.getElementById("list-"+sid).classList.toggle("active");
            document.getElementById("list-filter-"+sid).classList.toggle("active");
        }

		var menu = [{
			name: 'Jump To Player',
			title: 'Jump To Player',
			fun: function (data,event) {
			    var playerInfo = data.trigger;
				var pcid = playerInfo.attr("data-pcid");
				window.open("dmjump-toggle.php?pcid=" + pcid)
			},
            className: 'send-message-button'
		}, {
			name: 'Send Message',
			title: 'Send Message',
			fun: function (data,event) {
				var playerInfo = data.trigger;
				var pcid = playerInfo.attr("data-pcid");
				var server = playerInfo.attr("data-server");
				var playerName = playerInfo.attr("data-player-name");
				var charName = playerInfo.attr("data-char-name");
				window.open("send-message.php?pcid=" + pcid + "&server=" + server + "&playerName=" + playerName + "&charName=" + charName)
			},
			className: 'send-message-button'
		}, {
			name: 'One Week Lookup',
			title: 'One Week Lookup',
			fun: function (data,event) {
				var playerInfo = data.trigger;
				var cdKey = playerInfo.attr("data-cdkey");
				var charName = playerInfo.attr("data-char-name");
				var date = new Date(new Date - 1000 * 60 * 60 * 24 * 7);
				var dateFrom = `${date.getFullYear()}-${date.getMonth() + 1}-${date.getDate()}`;
				window.open("logsearch.php?dateFrom=" + dateFrom + "&q1=" + cdKey);
				window.open("logsearch.php?dateFrom=" + dateFrom + "&q1=" + charName);
			},
			className: 'send-message-button'
		}];

		$('.player-row a').contextMenu(menu);
	</script>
</html>
