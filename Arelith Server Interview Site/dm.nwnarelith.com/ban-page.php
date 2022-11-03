<?php

include 'webhooklogutils.php';

require 'login.php';
$userName = $userName ?? "Testing Environment";
$group = $group ?? "Testing Environment";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>DM :: Ban Page</title>
<!--	<link href="https://unpkg.com/vanilla-datatables@latest/dist/vanilla-dataTables.min.css" rel="stylesheet" type="text/css">-->
<!--    <script src="https://unpkg.com/vanilla-datatables@latest/dist/vanilla-dataTables.min.js" type="text/javascript"></script>-->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.20/css/jquery.dataTables.css">
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.js"></script>
    <link href="css/ban-page.css" rel="stylesheet" type="text/css">
    <script src="https://kit.fontawesome.com/55dbb0c074.js" crossorigin="anonymous"></script>
    <!-- Idle handler -->
<!--    <script src="idle.js" type="text/javascript"></script>-->
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

    $i = 0;
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
//    DiscordPageQuery($userName, $ClientIPaddress, $ini['dc_api']);
    }

    $con = mysqli_connect($ini['db_ip'], $ini['db_user'], $ini['db_password'], $ini['db_name']);

    $curTimeQuery = mysqli_query($con, "SELECT value FROM gs_system WHERE row_key='time' LIMIT 1");
    $curTime = mysqli_fetch_array($curTimeQuery)[0];

//    $quarterSql = "SELECT quarter.row_key, quarter.owner, quarter.timeout, quarter.lock_dc, pc.name, pc.playername, quarter.last_used, pc.keydata, quarter.for_sale, area.name area_name
//                     FROM gs_quarter quarter
//                     INNER JOIN gs_pc_data pc ON quarter.owner = pc.id
//					 LEFT OUTER JOIN gvd_area_data area ON quarter.area_id = area.id
//                     GROUP BY quarter.row_key
//                     ORDER BY pc.keydata";


    $banSQL =
    "SELECT gro.bg_id, gro.bg_timestamp, gro.bg_useip, gro.bg_description, link.bl_node, link.bl_group, node.bn_id, node.bn_type, node.bn_data
    FROM fb_ban_groups AS gro
    INNER JOIN fb_ban_links AS link ON gro.bg_id=link.bl_group
    INNER JOIN fb_ban_nodes AS node ON node.bn_id=link.bl_node
    ORDER BY gro.bg_id DESC";
    $banQuery = mysqli_query($con, $banSQL);


    $banData = array();
    $tempBGID = "";
    $bgID = "";

    while ($row = mysqli_fetch_array($banQuery))
    {
        if($bgID != $row['bg_id']) {

            $bgID = $row['bg_id'];
            $banData[$bgID]['bg_timestamp'] = $row['bg_timestamp'];
            $banData[$bgID]['bg_useip'] = $row['bg_useip'];
            $banData[$bgID]['bg_description'] = $row['bg_description'];

            $nodeID   = $row['bn_id'];
            $nodeType = $row['bn_type'];
            $nodeData = $row['bn_data'];

            $banData[$bgID]['banNodes'][$nodeID] = array();

            array_push($banData[$bgID]['banNodes'][$nodeID], $nodeType);
            array_push($banData[$bgID]['banNodes'][$nodeID], $nodeData);
        }else{
            $nodeID   = $row['bn_id'];
            $nodeType = $row['bn_type'];
            $nodeData = $row['bn_data'];

            $banData[$bgID]['banNodes'][$nodeID] = array();

            array_push($banData[$bgID]['banNodes'][$nodeID], $nodeType);
            array_push($banData[$bgID]['banNodes'][$nodeID], $nodeData);
        }
    }
   // var_dump(json_encode($banData, JSON_PRETTY_PRINT));

    ?>

    <h1 align="center">Ban Tool</h1>
    <form method="post" id="usrform" action="<?php echo "banutil.php" ;?>">
        <div class ="key" align = "center" id="usrform">

            <i class="fas fa-key"></i>

            <textarea name="CDKey" placeholder = "CD Key(s)" id="keys" form="usrform" ></textarea>

            <br />

            <i class="fas fa-globe"></i>


            <textarea name="IPAdd" placeholder = "I.P. Address(es)" id="ips" form="usrform"></textarea>

            <br />
            <i class="fas fa-info-circle"></i>
            <textarea name="description" placeholder ="Description of Incident (Character Name/Username also recommended)" id ="desc" form="usrform"></textarea>
            <br><br>
            Use IP

            <input type="checkbox" name="useIP" id = "test" />
            Keys linked?
            <input type="checkbox" name="linkedKeys" />
        </div>
        <div class="cutebut">
            <input class = "cutebutt-ban btn fa-input" type="submit" name="submit" value="&#xf0e3;">
        </div>
    </form>
<hr>
    <br />
<h1 align="center">Unban Tool</h1>
<form method="post" id="unbanform" action="<?php echo "banutil.php" ;?>">
    <div class ="key" align = "center" id="unbanform">
        <i class="far fa-address-card"></i>
        <textarea name="unbanData" form="unbanform" placeholder="CD-Key/IP-Address(es) (Or Group ID(s), but be sure to check the box)"></textarea>
        <br>
        Group IDs?
        <input type="checkbox" name="groupIDs" />
    </div>
    <div class="cutebut">
        <input class = "cutebutt-unban btn fa-input" type="submit" name="submit" value="&#xf0e3;">

    </div>
</form>
    <br />

    <div id="flexContainer">
    <?php
    echo "<table id='mainTable' border=\"1\">";
    echo "<thead>";
    echo "<tr>";
    echo "<th>Ban Group ID</th>";
    echo "<th>Timestamp</th>";
    echo "<th>Use IP</th>";
    echo "<th>Lock</th>";
    echo "<th>Description</th>";
    echo "</tr>";
    echo "</thead>";
    //echo "<tbody>";
    //   foreach ($banData as $key => $value)
//    {
//        echo "<tr data-child-name=\"test1\" data-child-value=\"10\" class = 'banGroup' id=\"b" . $i ."\">";
//
//        echo "<td>" . $key . "</td>";
//        echo "<td>" . $value['bg_useip'] . "</td>";
//        echo "<td>" . $value['bg_description'] . "</td>";
//
//        echo "</tr>";

//             echo "<tr class = 'banNodeHeader'>";
//             echo "<th> Ban Type </th>";
//             echo "<th> Ban Data</th>";
//             echo "</tr>";
//            foreach($banData[$key]['banNodes'] as $ndID => $ndArr){
//
//                echo "<tr class ='banNode'>";
//
//                echo "<td>" . $ndArr[0] . "</td>";
//                echo "<td>" . $ndArr[1] . "</td>";
//
//                echo "</tr>";
//            }
//            echo "</div>";
//            $i++;
//    }
    //echo "</tbody>";
    echo "<tfoot>";
    echo "<tr>";
    echo "<th>Ban Group ID</th>";
    echo "<th>Timestamp</th>";
    echo "<th>Use IP</th>";
    echo "<th>Lock</th>";
    echo "<th>Description</th>";
    echo "</tr>";
    echo "</tfoot>";
    echo "</table>";

?>
<table id="linkTable" class="display">
    <thead>
        <tr>
            <th>Node ID</th>
            <th>Group ID</th>
        </tr>
    </thead>
    <tfoot>
    <tr>
        <th>Node ID</th>
        <th>Group ID</th>
    </tr>
    </tfoot>
</table>

<table id="nodeTable" class="display">
    <thead>
    <tr>
        <th>Node ID</th>
        <th>Ban Type</th>
        <th>Ban Data</th>
    </tr>
    </thead>
    <tfoot>
    <tr>
        <th>Node ID</th>
        <th>Ban Type</th>
        <th>Ban Data</th>
    </tr>
    </tfoot>
</table>
    </div>

<div id="banPageHelp">
    Hello and welcome to the ban-page, we hope you have a pleasant stay here.

    <h2><b><u>Ban tool Info</u></b></h2>
    The ban-tool has 5 fields. <br />
    CD-Keys: You can enter CD-Keys that you want to ban.<br />
    IP-Addresses: You can enter IP Addresses that you want to ban.<br />
    Description: You should add a short description of the ban here along with a character name ideally. By default, the data that you enter will be appended to the end of your description. <br />
    UseIP: Using this will enable an IP ban on whoever you select in CD-Keys or IP-Addresses. Note: If you want to IP-Ban an existing player, you can create a new node through their key/ip or find their existing node from the tables below.<br />
    Then proceed to enter the key/ip and submit the form with "Use IP" checked. <br />
    Keys Linked?: For this to work, you will need to enter several keys/IPs into the boxes. With all the keys/IPs entered (I will show you below how to do that) and this field checked, all the bans will be linked together as if they
    were the same player. A great use for multiboxers/known ban evaders.<br />

    <h2><b><u>Unban Tool Info</u></b></h2>
    Enter the keys/IPs to ban using the method below and click the unban button. Presto. <br />
    NOTE: If you want to unban by Group ID, you can use the comma separated list as before, but be sure to check the "Group IDs?" box before submitting the form, or nothing will happen.

    <h2><b><u>How to ban/unban several keys at once (Or Link keys)</u></b></h2>
    Separate each key with a , e.g. Key1, key2, key3 <br />
    Or<br />
    Key1, <br />
    Key2, <br />
    Key3, <br />
    Either method works, whitespace is ignored. Same with IPS. Key/IP must be valid or said key/IP will be ignored<br />
    <br />
    Have fun banning/unbanning.
    <br />
    <br />
    <br />





</div>
<script>
    // var table = new DataTable("#mainTable", {
    //     perPage: 50,
    //     layout: {
    //     top: "{search}"
    //     },
    // });

    // $(document).ready( function(){
    //     $('#mainTable').DataTable();
    // });

    /* Formatting function for row details - modify as you need */
    function format (name, value) {
        return '<div>Name: ' + name + '<br />Value: ' + value + '</div>';
    }

    $(document).ready(function() {
        var table = $('#mainTable').DataTable( {
            "processing": true,
            "serverSide": true,
            "ajax": "server_processing.php?id=mainTable",
            "order": [[ 0, "desc" ]]

        } )});

        $(document).ready(function() {
        var table = $('#linkTable').DataTable( {
            "processing": true,
            "serverSide": true,
            "ajax": "server_processing.php?id=linkTable",
            "order": [[ 0, "desc" ]]

        } )});

    $(document).ready(function() {
        var table = $('#nodeTable').DataTable( {
            "processing": true,
            "serverSide": true,
            "ajax": "server_processing.php?id=nodeTable",
            "order": [[ 0, "desc" ]]

        } )});


        // Add event listener for opening and closing details
    //     $('#mainTable tbody').on('click', 'td.details-control', function () {
    //         var tr = $(this).closest('tr');
    //         var row = table.row( tr );
    //
    //         if ( row.child.isShown() ) {
    //             // This row is already open - close it
    //             row.child.hide();
    //             tr.removeClass('shown');
    //         }
    //         else {
    //             // Open this row
    //             row.child(format(tr.data('child-name'), tr.data('child-value'))).show();
    //             tr.addClass('shown');
    //         }
    //     } );
    // } );

    // let i;
    // let j;
    // let k;
    // let l;
    // let o ;
    //
    // let isBanNode = 0;
    // let banGroupIndices = [];
    // let tables = document.getElementById('mainTable');
    //
    // let groupRows = document.querySelectorAll('.banGroup');
    //
    //
    // for(i = 0; i < groupRows.length; i++) {
    //
    //     groupRows[i].addEventListener('click', function(){
    //         o = 0;
    //         while(banGroupIndices.length > 0){
    //             banGroupIndices.pop();
    //         }
    //         for(j = 0; j < tables.rows.length; j++){
    //             if(tables.rows[j].className == "banGroup"){
    //                 banGroupIndices.push(j);
    //             }
    //         }
    //
    //         let groupRowID = tables.rows[this.dataIndex + 1].id;
    //         // group i'th row
    //         let groupithRow = groupRowID[groupRowID.length - 1];
    //
    //
    //         let currHeight;
    //         for(k = this.dataIndex + 1; k < banGroupIndices[parseInt(groupithRow)+1]; k++){
    //
    //                 if(!(tables.rows[k].className == "banGroup")){
    //                    // console.log("Row");
    //                     currHeight = window.getComputedStyle(tables.rows[k]).getPropertyValue('height');
    //                     console.log(tables.rows[k].childNodes);
    //                     if(currHeight != "36px") {
    //                        // console.log("Expanding");
    //                         tables.rows[k].style.height = "36px";
    //                         for(l = 0; l < tables.rows[k].childNodes.length; l++){
    //                             tables.rows[k].childNodes[l].style.fontSize = "initial";
    //                             //tables.rows[k].childNodes[l].style.padding = "8px 10px";
    //                             //tables.rows[k].childNodes[l].setProperty("padding", "8px 10px", "important");
    //                         }
    //
    //                     }else{
    //                        // console.log("Shrinking");
    //                         tables.rows[k].style.height = "0px";
    //                         for(l = 0; l < tables.rows[k].childNodes.length; l++){
    //
    //                         }
    //                     }
    //                 }
    //         }
    //
    //
    //     });
    // }
    //
    // for(i = 0; i < groupRows.length; i++){
    //
    // }
    //
    //
    // for(i =0; i < tables.rows.length; i++){
    //
    // }
</script>
</html>
