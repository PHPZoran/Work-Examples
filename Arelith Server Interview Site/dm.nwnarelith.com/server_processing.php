<?php
//require 'login.php';
/*
 * DataTables example server-side processing script.
 *
 * Please note that this script is intentionally extremely simple to show how
 * server-side processing can be implemented, and probably shouldn't be used as
 * the basis for a large complex system. It is suitable for simple use cases as
 * for learning.
 *
 * See http://datatables.net/usage/server-side for full details on the server-
 * side processing requirements of DataTables.
 *
 * @license MIT - http://datatables.net/license_mit
 */

/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 * Easy set variables
 */
if($_GET['id'] == "mainTable") {
// DB table to use
    $table = 'fb_ban_groups';

// Table's primary key
    $primaryKey = 'bg_id';

// Array of database columns which should be read and sent back to DataTables.
// The `db` parameter represents the column name in the database, while the `dt`
// parameter represents the DataTables column identifier. In this case simple
// indexes
    $columns = array(
        array('db' => 'bg_id', 'dt' => 0),
        array('db' => 'bg_timestamp', 'dt' => 1),
        array('db' => 'bg_useip', 'dt' => 2),
        array('db' => 'bg_lock', 'dt' => 3),
        array('db' => 'bg_description', 'dt' => 4)
    );
}else if($_GET['id'] == "linkTable"){
    // DB table to use
    $table = 'fb_ban_links';

// Table's primary key
    $primaryKey = 'bl_node';

// Array of database columns which should be read and sent back to DataTables.
// The `db` parameter represents the column name in the database, while the `dt`
// parameter represents the DataTables column identifier. In this case simple
// indexes
    $columns = array(
        array('db' => 'bl_node', 'dt' => 0),
        array('db' => 'bl_group', 'dt' => 1)
    );
}else if($_GET['id'] == "nodeTable"){
    // DB table to use
    $table = 'fb_ban_nodes';

// Table's primary key
    $primaryKey = 'bn_id';

// Array of database columns which should be read and sent back to DataTables.
// The `db` parameter represents the column name in the database, while the `dt`
// parameter represents the DataTables column identifier. In this case simple
// indexes
    $columns = array(
        array('db' => 'bn_id', 'dt' => 0),
        array('db' => 'bn_type', 'dt' => 1),
        array('db' => 'bn_data', 'dt' => 2)
    );
}
if(file_exists('/web_info/web.ini')){
    $ini = parse_ini_file('/web_info/web.ini');
}else{
    $ini = parse_ini_file('web.ini');
}

// SQL server connection information
$sql_details = array(
    'user' => $ini['db_user'],
    'pass' => $ini['db_password'],
    'db'   => $ini['db_name'],
    'host' => $ini['db_ip']
);

/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 * If you just want to use the basic configuration for DataTables with PHP
 * server-side, there is no need to edit below this line.
 */

require( 'ssp.class.php' );

echo json_encode(
    SSP::simple( $_GET, $sql_details, $table, $primaryKey, $columns )
);
?>
