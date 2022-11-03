<?php
//PhPBB API Bootstrap
define('IN_PHPBB', true);

//Root path for forums
define('ROOT_PATH', "../forum.nwnarelith.com");

//DEBUG ONLY USED FOR LOCAL TESTING, DO NOT PUSH THIS TO PROD WITH THIS UNCOMMENTED
// define('ROOT_PATH', "../forum.nwnarelith.com/quickinstall/boards/Skitia2");

if (!defined('IN_PHPBB') || !defined('ROOT_PATH')) {
    exit();
}

$error = "";
$logMess = "";

$phpEx = "php";
$phpbb_root_path = (defined('PHPBB_ROOT_PATH')) ? PHPBB_ROOT_PATH : ROOT_PATH . '/';
include($phpbb_root_path . 'common.' . $phpEx);

if ( !function_exists('group_memberships') ){
    include($phpbb_root_path . 'includes/functions_user.'.$phpEx);
}

$sql = 'SELECT user.username, user.user_id FROM ' . USER_GROUP_TABLE . ' AS groups LEFT JOIN ' . USERS_TABLE . ' AS user ON groups.user_id=user.user_id' .
          ' WHERE groups.group_id = 8
          AND groups.user_pending = 0';
$leadRes = $db->sql_query($sql);
$rows = $db->sql_fetchrowset($leadRes);

$alldms = $rows;
?>