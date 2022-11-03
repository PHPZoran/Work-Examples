<?php

include 'webhooklogutils.php';

require 'login.php';

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>DM :: Quarter Inspection Tool</title>
	<link href="https://unpkg.com/vanilla-datatables@latest/dist/vanilla-dataTables.min.css" rel="stylesheet" type="text/css">
    <script src="https://unpkg.com/vanilla-datatables@latest/dist/vanilla-dataTables.min.js" type="text/javascript"></script>
    <!-- Idle handler -->
    <script src="idle.js" type="text/javascript"></script>
</head>
<div class="userinfo">
  <p>
    <strong>User:</strong> <?=$userName;?> <strong>Role:</strong> <?=$group;?>
    <form action="loginhandler.php" method="GET">
      <button type="submit" name="logout" value="1">Logout</button>
    </form>
</p>
</div>
<?php

    //ini_set('display_errors', 1);
    //ini_set('display_startup_errors', 1);
    //error_reporting(E_ALL);
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

    $curTimeQuery = mysqli_query($con, "SELECT value FROM gs_system WHERE row_key='time' LIMIT 1");
    $curTime = mysqli_fetch_array($curTimeQuery)[0];

    $quarterSql = "SELECT quarter.row_key, quarter.owner, quarter.timeout, quarter.lock_dc, pc.name, pc.playername, quarter.last_used, pc.keydata, quarter.for_sale, area.name area_name
                     FROM gs_quarter quarter
                     INNER JOIN gs_pc_data pc ON quarter.owner = pc.id
					 LEFT OUTER JOIN gvd_area_data area ON quarter.area_id = area.id
                     GROUP BY quarter.row_key
                     ORDER BY pc.keydata";
  /*
    $quarterSql = "SELECT quarter.row_key, quarter.owner, quarter.timeout, quarter.lock_dc, pc.name, pc.playername, pc.keydata, quarter.last_used, quarter.for_sale
                     FROM gs_quarter quarter
                     INNER JOIN gs_pc_data pc ON quarter.owner = pc.id
                     GROUP BY quarter.row_key
                     ORDER BY pc.keydata";*/
    $quarterQuery = mysqli_query($con, $quarterSql);

    $quarterData = array();
    while ($row = mysqli_fetch_array($quarterQuery))
    {
        $id = $row['row_key'];
        $quarterData[$id]['row_key'] = $id;
        $quarterData[$id]['area_name'] = $row['area_name'];
        $quarterData[$id]['owner'] = $row['owner'];
        $quarterData[$id]['lock_dc'] = $row['lock_dc'];
        $quarterData[$id]['timeout'] = $row['timeout'];
        $quarterData[$id]['name'] = $row['name'];
        $quarterData[$id]['playername'] = $row['playername'];
        $quarterData[$id]['keydata'] = $row['keydata'];
        $quarterData[$id]['last_used'] = $row['last_used'];
        $quarterData[$id]['for_sale'] = $row['for_sale'];
        $quarterData[$id]['expires_in'] = ($row['last_used'] + $row['timeout']) - $curTime;
    }

    //echo mysqli_error($con);

    echo "<span style='margin-right:30px;background-color:#CCFF99;'>Available for Purchase</span>";
    echo "<span style='margin-right:30px;background-color:#FFCC99;'>Duplicate for Sale OR Expiring soon</span>";
    echo "<span style='margin-right:30px;background-color:#FF9999;'>Duplicate not for Sale</span>";

    echo "<table border=\"1\">";
    echo "<thead>";
    echo "<tr>";
    echo "<th>Quarter Name</th>";
    echo "<th>Area Name</th>";
    echo "<th>Owner PC ID</th>";
    echo "<th>Owner PC Name</th>";
    echo "<th>Owner Player Name</th>";
    echo "<th>Owner PC CD Key ID</th>";
    echo "<th title=\"Default 40 is probably a shop\">Lock DC</th>";
    // echo "<th>Last Used</th>";
    echo "<th>Timeout</th>";
    echo "<th>For Sale</th>";
    // echo "<th>Expires In</th>";
    echo "</tr>";
    echo "</thead>";
    echo "<tbody>";

    $dupeQuarters = array();
    $dupeShops = array();

    foreach ($quarterData as $key => $value)
    {
		if ($value['lock_dc'] == "40")
		{
			if ($value['for_sale'] != 1 && $value['expires_in'] > 0)
			{
				$dupeShops[$value['keydata']] += 1;
			}
		}
		else
		{
			if ($value['for_sale'] != 1 && $value['expires_in'] > 0)
			{
				$dupeQuarters[$value['keydata']] += 1;
			}
		}
    }

    foreach ($quarterData as $key => $value)
    {
		if ($value['expires_in']  < -4000)
		{
			// Quarter is defunct.
			continue;
		}
        else if ($dupeShops[$value['keydata']] > 1 || $dupeQuarters[$value['keydata']] > 1)
        {
            if ($value['for_sale'] == 1 || $value['expires_in'] < 0)
            {
                echo "<tr bgcolor=\"#FFCC99\">";
            }
            else
            {
                echo "<tr bgcolor=\"#FF9999\">";
            }
        }
        else
        {
			echo "<tr>";
        }
	$isShop = $value['lock_dc'] == 40;
        echo "<td>" . $value['row_key'] . ($isShop ? "(SHOP)" : "") . "</td>";
        echo "<td>" . $value['area_name'] . "</td>";
        echo "<td>" . $value['owner'] . "</td>";
        echo "<td>" . $value['name'] . "</td>";
        echo "<td>" . $value['playername'] . "</td>";
        echo "<td>" . $value['keydata'] . "</td>";
        echo "<td>" . ($isShop ? "" : $value['lock_dc'] ) . "</td>";
        //echo "<td>" . $value['last_used'] . "</td>";
        echo "<td>" . round($value['timeout'] / 60 / 60, 2) . "</td>";

        if ($value['for_sale'] == 1)
        {
            echo "<td bgcolor=\"#CCFF99\">" . $value['for_sale'] . "</td>";
        }
        else
        {
            echo "<td>" . $value['for_sale'] . "</td>";
        }

        //$formattedExpiresIn = round($value['expires_in'] / 60 / 60, 2);
        //
        //if ($formattedExpiresIn <= 0.0)
        //{
        //     echo "<td bgcolor=\"#CCFF99\">" . $formattedExpiresIn . "</td>";
        //}
        //else if ($formattedExpiresIn <= 12.0)
        //{
        //     echo "<td bgcolor=\"#FFCC99\">" . $formattedExpiresIn . "</td>";
        // }
        // else
        // {
        //     echo "<td>" . $formattedExpiresIn . "</td>";
        // }


        echo "</tr>";
    }
    echo "</tbody>";
    echo "</table>";
?>
<script>
    var table = new DataTable("table", {
        perPage: 1500,
        layout: {
        top: "{search}",
        bottom: ""
        },
    });
</script>
</html>
