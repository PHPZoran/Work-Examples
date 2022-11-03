<?php

ini_set('session.gc_maxlifetime', 1440);
ini_set('session.cookie_lifetime', 1440);
//Always start the session in order to handle login details.
session_start();

//TODO: Move this conditional to $_SESSION || !(Statement) across all files
// if(time() > $_SESSION['expiry']){
//   session_destroy();
//   session_regenerate_id();
//   exit("Point A");
//   if($_SERVER['QUERY_STRING']){
//     exit("Point B");
//     header('Location: loginhandler.php?logout=1&refUrl=' . urlencode(basename($_SERVER["PHP_SELF"]) . "?" . $_SERVER['QUERY_STRING']));

//     } else{
//       exit("Point C");
//     header('Location: loginhandler.php?logout=1&refUrl=' . urlencode(basename($_SERVER["PHP_SELF"])));
//   }
// }

//Check if logged in, assign session username and group for use in page.
if($_SESSION["loggedin"]){
  $userName = $_SESSION["username"];
  $group    = $_SESSION['user_group'];

  //Destroy the session if not logged in (just in case) and send user to login page with current link reference for quick re-entry.
  }else{
    session_destroy();
    session_regenerate_id();
    if($_SERVER['QUERY_STRING']){
      header('Location: loginhandler.php?logout=1&refUrl=' . urlencode(basename($_SERVER["PHP_SELF"]) . "?" . $_SERVER['QUERY_STRING']));
    }else{
    header('Location: loginhandler.php?logout=1&refUrl=' . urlencode(basename($_SERVER["PHP_SELF"])));
    }
}
?>
