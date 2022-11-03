<?php
//require `login.php`; //For some reason "this" login.php doesn't work
include 'webhooklogutils.php';

require 'login.php';
//error_reporting(E_ALL);
//ini_set("display_errors", 1);
$userName = $userName ?? "Testing Environment";
$group = $group ?? "Testing Environment";
/*
 * This file handles the banning SQL logic
 * TODO Consolidate to function
 * TODO Improve SQL statements
 * NOTE: I realise that this is a lot of SpaghettiQL and if you're reading this
 * and having trouble understanding how the ban logic tables work feel free
 * to contact me:-DM Hoodoo
 */
if(file_exists('/web_info/web.ini')){
    $ini = parse_ini_file('/web_info/web.ini');
}else{
    $ini = parse_ini_file('web.ini');
}

$con = mysqli_connect($ini['db_ip'], $ini['db_user'], $ini['db_password'], $ini['db_name']);

if($_POST["CDKey"] || $_POST['IPAdd']){
    $keyString = $_POST["CDKey"];
    $ipString  = $_POST["IPAdd"];
    $descString = $_POST["description"] . " ";
    $isKeyLinked = $_POST["linkedKeys"];
    $useIP = $_POST['useIP'] ? 1 : 0;
    $linkedBGs = array();

    $UNIXtime = time();

    // Parse the delimited key, ip strings, trim, and merge into one array for ban handling
    $keys = explode("," , $keyString);
    $ips  = explode("," , $ipString);
    foreach($keys as $key => &$val) {
        $val = trim($val);
        $val = strtoupper($val);
        // Keys can only be 8 characters long
        if(strlen($val) != 8)
            unset($keys[$key]);
    }

    foreach($ips as $key => &$val){
        $val = trim($val);

        if(!filter_var($val, FILTER_VALIDATE_IP))
            unset($ips[$key]);
    }

            $banData = array_merge($keys, $ips);

    $descString .= $banData[0];

    // If the key is linked, we have to compile BGIDs for all elements to delete/adjust
    if($isKeyLinked){

        foreach($banData as $val2){

            $banSqlPrepare = mysqli_prepare($con, "SELECT bn.bn_data, bg.bg_id FROM fb_ban_nodes AS bn INNER JOIN fb_ban_links AS bl ON bl.bl_node=bn.bn_id INNER JOIN fb_ban_groups AS bg ON bg.bg_id=bl.bl_group WHERE bn_data=?");
            mysqli_stmt_bind_param($banSqlPrepare, "s", $val2);
            mysqli_stmt_execute($banSqlPrepare);

            $result = mysqli_stmt_get_result($banSqlPrepare);

            if($row = mysqli_fetch_array($result)){
                array_push($linkedBGs, $row['bg_id']);
            }
         }
        // Use the first BG found for key linking, just the first for convenience. No real reason, could pick any realistically
        $bg_id = $linkedBGs[0];

        // If no BG exists for elements, we'll have to make our own
        if(!$bg_id){
        $addBanSqlPrepared = mysqli_prepare($con, "INSERT INTO fb_ban_groups (bg_timestamp, bg_useip, bg_lock, bg_description) VALUES(?, ?,?,?)");
        mysqli_stmt_bind_param($addBanSqlPrepared, "iiss", $UNIXtime, $useIP, $userName, $descString);
        mysqli_stmt_execute($addBanSqlPrepared);
        $bg_id = mysqli_insert_id($con);
        mysqli_stmt_close($addBanSqlPrepared);

        // We set useIP here because we only need to set it once and save operations compared to running it in the foreach
        } else{
            $updateBanUseIP = mysqli_prepare($con, "UPDATE fb_ban_groups SET bg_useip=? WHERE bg_id=?");
            mysqli_stmt_bind_param($updateBanUseIP, "ii", $useIP, $bg_id);
            mysqli_stmt_execute($updateBanUseIP);
            mysqli_stmt_close($updateBanUseIP);
        }
    }

    // https://i.redd.it/nkrot0rpzjl41.png
    foreach($banData as $val3) {
        //bn_type=(0 = cdkey, 1 = playername, 2 = ip) Playername is deprecated
        if(filter_var($val3, FILTER_VALIDATE_IP)){
            $bn_type = 2;
        }else{
            $bn_type = 0;
        }

//        $testdm = "testdm";



        /*
         * We check if a node exists for the element already.
         * If the node exists, and Keylink is inactive. We don't need to add anything, so we just update bg_useip
         * If the node doesn't exist, and the keylink is inactive. We add the node normally, group, and link.
         * If the node exists, and keylink is active. We have to delete, and recreate the node to pair it to the new "common" ban group
         * that we picked earlier. Also delete the ban_group unless it's the node with the common group that we picked earlier.
         * If the node doesn't exist, and keylink is active. We create the node and link it to the common ban group.
         */
        $nodeExistsSql = mysqli_prepare($con, "SELECT a.bn_id, b.bl_group, c.bg_id FROM fb_ban_nodes AS a INNER JOIN fb_ban_links AS b ON a.bn_id=b.bl_node INNER JOIN fb_ban_groups AS c ON b.bl_group=c.bg_id WHERE a.bn_data=?");
        mysqli_stmt_bind_param($nodeExistsSql, "s", $val3);
        mysqli_stmt_execute($nodeExistsSql);

        $result = mysqli_stmt_get_result($nodeExistsSql);

        // Keylink is active. Refer to docblock lines: 99-106
        if($isKeyLinked){
            // Delete Node
            while($row = mysqli_fetch_array($result)) {
                $deleteNodeSQL = mysqli_prepare($con, "DELETE FROM fb_ban_nodes WHERE bn_data=?");
                mysqli_stmt_bind_param($deleteNodeSQL, "s", $val3);
                mysqli_stmt_execute($deleteNodeSQL);
                mysqli_stmt_close($deleteNodeSQL);

                  //Bottom block for dealing with group deletion has been deprecated to retain group ban hisstory
//                // Store bgID to delete for comparison
//                $bgIDToDelete = $row['bl_group'];
//
//                // Delete ban group unless it's the ban-group we picked for the keylink earlier.
//                if($bgIDToDelete != $bg_id) {
//                    $deleteGroupSQL = mysqli_prepare($con, "DELETE FROM fb_ban_groups WHERE bg_id=?");
//                    mysqli_stmt_bind_param($deleteGroupSQL, "i", $bgIDToDelete);
//                    mysqli_stmt_execute($deleteGroupSQL);
//                    mysqli_stmt_close($deleteGroupSQL);
//                    }
            }
                // Insert Node
                $addBanSqlPrepared = mysqli_prepare($con, "INSERT INTO fb_ban_nodes (bn_type, bn_data) VALUES(?,?)");
                mysqli_stmt_bind_param($addBanSqlPrepared, "is", $bn_type, $val3);
                mysqli_stmt_execute($addBanSqlPrepared);
                $bn_id = mysqli_insert_id($con);
                mysqli_stmt_close($addBanSqlPrepared);

            // Link
            $addBanSql = "INSERT INTO fb_ban_links (bl_node, bl_group)
                                VALUES(?, ?)";

            $addBanSqlPrepared = mysqli_prepare($con, $addBanSql);
            mysqli_stmt_bind_param($addBanSqlPrepared, "ii", $bn_id, $bg_id);
            mysqli_stmt_execute($addBanSqlPrepared);
            mysqli_stmt_close($addBanSqlPrepared);

            // Not keylinked, and node does not already exist
            } else if(!($row = mysqli_fetch_array($result))){

            $addBanSqlPrepared = mysqli_prepare($con, "INSERT INTO fb_ban_nodes (bn_type, bn_data) VALUES(?, ?)");
            mysqli_stmt_bind_param($addBanSqlPrepared, "is", $bn_type, $val3);
            mysqli_stmt_execute($addBanSqlPrepared);
            $bn_id = mysqli_insert_id($con);
            mysqli_stmt_close($addBanSqlPrepared);

            $addBanSqlPrepared = mysqli_prepare($con, "INSERT INTO fb_ban_groups (bg_timestamp, bg_useip, bg_lock, bg_description) VALUES(?, ?,?,?)");
            mysqli_stmt_bind_param($addBanSqlPrepared, "iiss", $UNIXtime, $useIP, $userName, $descString);
            mysqli_stmt_execute($addBanSqlPrepared);
            $bg_id = mysqli_insert_id($con);
            mysqli_stmt_close($addBanSqlPrepared);



            $addBanSql = "INSERT INTO fb_ban_links (bl_node, bl_group)
                                VALUES(?, ?)";
            //mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
            $addBanSqlPrepared = mysqli_prepare($con, $addBanSql);
            mysqli_stmt_bind_param($addBanSqlPrepared, "ii", $bn_id, $bg_id);
            mysqli_stmt_execute($addBanSqlPrepared);
            mysqli_stmt_close($addBanSqlPrepared);

        // Not keylinked, node does exist. All we need to do is update the UseIP field
        }else{
                $bg_id = $row['bg_id'];
                $updateBanUseIP = mysqli_prepare($con, "UPDATE fb_ban_groups SET bg_useip=? WHERE bg_id=?");
                mysqli_stmt_bind_param($updateBanUseIP, "ii", $useIP, $bg_id);
                mysqli_stmt_execute($updateBanUseIP);
                mysqli_stmt_close($updateBanUseIP);

        }

    }
    //TODO consolidate to string THEN send
    if(is_dir("/web_info/DMLogs/")) {
        ArLogStandard($userName, $ClientIPaddress, '\"' . $_POST['CDKey'] . "\"\"" . $userName . "\"\"" .
            $_POST['IPAdd'] . "\"\"" . $_POST['useIP'] . "\"\"" . $_POST['description'] . "\"\"" . $_POST['linkedKeys'] . "\"");
    }

    DiscordPageQuery($userName, $ClientIPaddress, $ini['dc_api'],'\"' . $_POST['CDKey'] . "\"\n\"" . $userName . "\"\n\"" . $_POST['IPAdd'] .
        "\"\n`UseIP:`\"" . $_POST['useIP'] . "\"\n\"" . $_POST['description'] . "\"\n`Keys Linked:`\"" . $_POST['linkedKeys'] . "\"");

    DiscordBanNotif($userName, $ini['dc_ban_api'], 1, "\n" . $_POST['CDKey']  . "\n" . $_POST['IPAdd'] . "\n" . $_POST['description']);
}else if($_POST["unbanData"]){
    $dataString = $_POST["unbanData"];



    // Parse the delimited key, ip strings, trim, and merge into one array for ban handling
    $data = explode("," , $dataString);
    foreach($data as $key => &$val) {
        $val = trim($val);
        $val = strtoupper($val);
        if(!filter_var($val, FILTER_VALIDATE_IP) && strlen($val) !=8 && !$_POST['groupIDs'])
            unset($data[$key]);
    }

    if(!$_POST['groupIDs']) {
        // Whatever you do, don't change $val2 to $val. Language level bug where the reference operator doesn't
        // Unassign after the previous loop.
        foreach ($data as $key2 => $val2) {
            $removeNodeSQL = mysqli_prepare($con, "DELETE FROM fb_ban_nodes WHERE bn_data=?");
            mysqli_stmt_bind_param($removeNodeSQL, "s", $val2);
            mysqli_stmt_execute($removeNodeSQL);
            mysqli_stmt_close($removeNodeSQL);

        }
    }else{
       // mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
        foreach($data as $key3 => $val3) {
            $removeNodeGroupSQL = mysqli_prepare($con, "DELETE fb_ban_nodes FROM fb_ban_nodes 
            INNER JOIN fb_ban_links ON fb_ban_links.bl_node=fb_ban_nodes.bn_id INNER JOIN fb_ban_groups ON fb_ban_links.bl_group=fb_ban_groups.bg_id WHERE fb_ban_groups.bg_id=?");
            mysqli_stmt_bind_param($removeNodeGroupSQL, "s", $val3);
            mysqli_stmt_execute($removeNodeGroupSQL);
            mysqli_stmt_close($removeNodeGroupSQL);
        }
    }

    if(is_dir("/web_info/DMLogs/")) {
        ArLogStandard($userName, $ClientIPaddress, 'Unban \"' . $_POST['unbanData'] . "\"\"");
    }

    DiscordPageQuery($userName, $ClientIPaddress, $ini['dc_api'],'UNBAN \"' . $_POST['unbanData'] . "\"");

    DiscordBanNotif($userName, $ini['dc_ban_api'], 0, "\n" . $_POST['unbanData'], "008000");

}


mysqli_close($con);


header("Location: ban-page.php");

?>
