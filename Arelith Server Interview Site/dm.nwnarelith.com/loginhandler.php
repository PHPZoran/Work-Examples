<?php

include 'webhooklogutils.php';

//TODO: Consolidate into functions. Code is messy.
ini_set('session.gc_maxlifetime', 1440);
ini_set('session.cookie_lifetime', 1440);
//NOTE: session_start/session_destroy is PhP wide but session_begin/session_kill is PhPBB wide.
//Always start the session in order to handle login details.
session_start();

//PhPBB API Bootstrap
define('IN_PHPBB', true);

//Root path for forums
define('ROOT_PATH', "../forum.nwnarelith.com");

//DEBUG ONLY USED FOR LOCAL TESTING, DO NOT PUSH THIS TO PROD WITH THIS UNCOMMENTED
//define('ROOT_PATH', "./quickinstall-1.3.5/boards/root");

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

//Begin a PhPBB session, prepare the permissions.
$user->session_begin();
$auth->acl($user->data);
$user->setup();

$pageTitle = "DM :: Login";


//Store the refURL if user was linked to a specific page
$refURLPath = request_var("refUrl","");
$refURL = basename($refURLPath);


//Check if refURL exists
if($refURL){

  //Assign title appropriately
  if($refURL == "index.php"){

    $pageTitle = "DM :: Tools";

  }else if($refURL == "logsearch.php"){

    $pageTitle = "DM :: Logs";

  }else if($refURL == "logon-notifier.php"){

    $pageTitle = "DM :: Logon Notifier";

  }else if($refURL == "portal.php"){

    $pageTitle = "DM :: Portal";

  }else if($refURL == "send-message.php"){

    $pageTitle = "DM :: Send Message";

  }else if($refURL == "VPN-checker.php"){

    $pageTitle = "DM :: VPN Checker";

  }else if($refURL == "quarter-tool.php"){

    $pageTitle = "DM :: Quarter Tool";

  }else if($refURL == "server-management.php"){

    $pageTitle = "DM :: Server Management";

  }else if($refURL == "election-search.php"){

    $pageTitle = "DM :: Election Search";

  }
}


?>
<!DOCTYPE html>
<html>
<head>
  <title><?=$pageTitle?></title>
  <!--<meta name="viewport" content="width=device-width, initial-scale=1.0">-->
  <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1">
  <link href="css/loginhandler.css" type= "text/css" rel="stylesheet" />
  <style>
  @font-face {font-family: Dalelands; src: url('css/Dalelands Uncial.otf');}
  </style>
</head>
<body>
  <?php
  //Check if the system/user needs to be logged out. Unset the session variable and destroy it for extra measure. 1 or 0
  if(request_var("logout", 0)){
    $_SESSION = array();
    session_destroy();
  }

  //Attempt to login with the supplied user-name/password
  $result = $auth->login(request_var("username",""), request_var("password",""));

  if(file_exists('/web_info/web.ini')){
      $ini = parse_ini_file('/web_info/web.ini');
  }else{
      $ini = parse_ini_file('web.ini');
  }

  $discordAPI = $ini['dc_api'];

//Check if username and password exist
if(request_var("username","") && request_var("password","")){

$logMess = "User: " . request_var("username", "") . " IP: " . $ClientIPaddress;

//On login success
if($result["status"] == LOGIN_SUCCESS){

  //25 is Spellhold, kill session and bring back
  if(group_memberships(25,$user->data['user_id'],true)){
    $logMess = "SPELLHOLD LOGIN ATTEMPT User:" . request_var("username","") . " IP: " . $ClientIPaddress;

    ArLogTimeStamped($logMess);

    DiscordWarning(request_var("username", ""), $ClientIPaddress, "SPELLHOLD LOGIN ATTEMPT", $discordAPI);

    session_destroy();
    $user->session_kill();
    Header:('Location: loginhandler.php');
  }

  //Check if logged in user is leader of the Active DMs group. 8=Active DM
    if(group_memberships(8,$user->data['user_id'],true)){

      $isDM = TRUE;

      $sql = 'SELECT * FROM ' . USER_GROUP_TABLE . '
        WHERE group_id = 8
        AND group_leader = 1
          AND user_pending = 0
          AND user_id=' . $user->data['user_id'] . '
          LIMIT 1';

          $leadRes = $db->sql_query($sql);

          $rows = $db->sql_fetchrowset($leadRes);

          if($rows){
             $isHeadDM = TRUE;
           }else{
             $isHeadDM = FALSE;
           }
    }else{
      $isDM = FALSE;
    }

    //Set the session username
    $_SESSION["username"] = request_var("username","");

    //Check user_group
    //17 = Active Administrator
    if(group_memberships(17,$user->data['user_id'],true) || $isHeadDM){
    $_SESSION["user_group"] = "Administrator";
    $_SESSION["isAdmin"]    = 1;
    //Used to determine logged in state Session wide for PhP.
    $_SESSION["loggedin"] = 1;
    //8 = Active DMs
    }else if($isDM){
    $_SESSION["user_group"] = "DM";
    $_SESSION["loggedin"] = 1;
    //If not Admin/DM, force and kill PhPBB logout and destroy PhP session.
    }else{
    ArLogTimeStamped($logMess . " attempted to login. Invalid group!");
    $request->enable_super_globals();
    DiscordWarning(request_var("username", ""), $ClientIPaddress, "Non-DM/Non-administrator attempted login!", $discordAPI, "FF0000");
    $request->disable_super_globals();
    $user->session_kill();
    session_destroy();
    header('Location: loginhandler.php?error=invalidgroup');
    }

    //Set an expiry time for the session
    $_SESSION["logintime"] = time();
    $_SESSION["expiry"] = time() + 7200; //Time is calculated in seconds. After 2 hours (7200 seconds) destroy the session

    if($_SESSION["loggedin"]){
      //If user was linked a specific page, take them there. Otherwise default to index.php
      if($refURL){
      ArLogTimeStamped($logMess . " has logged in. Entering " . $refURL);
      //SendRichEmbedMessageToDiscord("", "Login", "`".$_SESSION["username"] . "`\r\n`" . $ClientIPaddress . "`\r\nEntering `".basename($refURL)."`", "0004FF");
      header('Location: '.$refURL);
    }else{
      ArLogTimeStamped($logMess . " has logged in");
      //SendRichEmbedMessageToDiscord("", "Login", "`".$_SESSION["username"] . "`\r\n`" . $ClientIPaddress."`", "0004FF");
      header('Location: index.php');
    }
  }


    //Boilerplate error handling
    }else if($result['status'] == LOGIN_ERROR_USERNAME || $result['status'] == LOGIN_ERROR_PASSWORD){
      ArLogTimeStamped($logMess . " has attempted to login with an incorrect username/password.");
      $request->enable_super_globals();
      DiscordWarning(request_var("username", ""), $ClientIPaddress, "Incorrect username/password", $discordAPI, "FFA600", "");
      $request->disable_super_globals();
      $error = "Incorrect username/password.";
    }else if($result['status'] == LOGIN_ERROR_ATTEMPTS){
      ArLogTimeStamped($logMess . " has attempted to login too many times.");
      $request->enable_super_globals();
      DiscordWarning(request_var("username", ""), $ClientIPaddress, "Has surpassed the login limit", $discordAPI, "FF6600");
      $request->disable_super_globals();
      $error = "Too many login attempts. Please wait before attempting to login again.";
    }else{
      ArLogTimeStamped($logMess . " has attempted to login. Possible fraudulent behavior detected.");
      $request->enable_super_globals();
      DiscordWarning(request_var("username", ""), $ClientIPaddress, "Possible fraudulent behavior detected.", $discordAPI, "FF0000");
      $request->disable_super_globals();
      SendMessageToDiscord($logMess . " has attempted to login. Possible fraudulent behavior detected.");
      $error = "Error logging in. Please contact the web administrator.";
    }
  }
    if($refURL && !isset($result['status'])){
      $error = "";
    }

    if(request_var("error","")){
      $error = "Invalid Group";
    }
  ?>


<div id="login-form" class ="login-form">
  <div class = "title">
    <h1><p>Arelith <br></h1><h2 id="safarihater"><span style="font-family: Dalelands;color:white">DM LOGIN</span></h2></p>
  </div>

  <form action="loginhandler.php" method="POST">
        <p class="login-text">
          <label class = 'login-usernamelabel' for="username"><strong>Username</strong> </label>&nbsp;
          </p>
          <input type="text" name="username" id="username" size="10" title="Username" class="login-username" required="true" placeholder="Username"/>
          <p class ="login-text">
          <label class = 'login-passwordlabel' for="password"><strong>Password</strong></label>&nbsp;
        </p>
          <input type="password" name="password" id="password" size="10" title="Password" class="login-password" required="true" placeholder="Password"/>
          <!--<label for="autologin">Log me on automatically each visit <input type="checkbox" name="autologin" id="autologin" /></label>-->
          <input type="submit" name="login" value="Login" class="login-submit" />
          <!--Hidden type will save refURL so on form submission so it will be saved-->
          <input type="hidden" name="refUrl" value="<?=$refURL?>">
  </form>
  <div class ="error">
    <h4><p><strong><?=$error?></strong></p></h4>
  </div>
</div>

<!--<form action="loginhandler.php?logout=1" method ="GET">
  <input type="submit" name="logout" value="Logout" />
</form>-->
<div class="underlay-photo"></div>
<div class="underlay-black"></div>

<footer>
  <script type="text/javascript">
    if(navigator.vendor.indexOf('Apple') > -1){
      document.getElementById("safarihater").style.display = "none";
      console.log("In Safari");
    }else{
      console.log("Not In Safari");
    }
  </script>
</footer>
</body>
</html>
