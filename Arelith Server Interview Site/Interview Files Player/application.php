<?php
require 'login.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);

//Get database connection
require_once("data-config.php");
//Check to make sure user doesn't exist in dm_app table
$check = "SELECT count(username) as count FROM int_applicant where username = ?";
$stmt = $con->prepare($check);
$stmt->bind_param('s',$_SESSION['username']);
$stmt->execute();
$results = $stmt->get_result();
$row = mysqli_fetch_assoc($results);

if($row['count'] != 0){
  header('Location: index.php');
  exit();  
}
if(isset($_POST['next'])){

  require_once("data-config.php");

  $cdkey = "";
  $keySqlPrepare = mysqli_prepare($nwncon, "SELECT player.cdkey FROM gs_player_data AS player LEFT JOIN gs_pc_data AS pc ON player.id=pc.keydata WHERE pc.playername=? LIMIT 1");
  mysqli_stmt_bind_param($keySqlPrepare, "s", $userName);
  mysqli_stmt_execute($keySqlPrepare);

  $result = mysqli_stmt_get_result($keySqlPrepare);

  if($row = mysqli_fetch_array($result)){
    $cdkey = $row['cdkey'];  
  }
  if(!$cdkey)
    die("There was an issue submitting your response. No CD-Key found. Please contact Hoodoo or Spyre");
  
  /*$cd_query = "SELECT player.cdkey
  FROM gs_player_data AS player 
  LEFT JOIN gs_pc_data AS pc ON player.id=pc.keydata 
  WHERE pc.playername=? LIMIT 1"; 
  $stmt = $con->prepare($query);
  $stmt->bind_param('s',$_SESSION['username']);
  $results = $stmt->execute();
  $row = mysqli_fetch_assoc($results);
  $cdkey = $row['cdkey'];*/
  // $cdkey = 'ABC123';


  $pseudonymValid = FALSE;
  $pseudonym = "UNDEFINED";
  do
  {
    // $pseudonym = json_decode(file_get_contents("http://names.drycodes.com/1?nameOptions=starwarsFirstNames&separator=space"))[0];
    $pseudonym = json_decode(file_get_contents("https://randomuser.me/api/"), JSON_OBJECT_AS_ARRAY)['results'][0]['name']['first'];

    $checkPseudonymExistsQuery = "SELECT * FROM int_applicant WHERE pseudonym=?";

    $stmt = $con->prepare($checkPseudonymExistsQuery);
    $stmt->bind_param("s", $pseudonym);

    $stmt->execute();

    if($stmt->num_rows() == 0)
      $pseudonymValid = TRUE;

    $stmt->close();

  }while(!$pseudonymValid);
  
  $query = "INSERT INTO int_applicant (username, cdkey, dm_reason, pseudonym) VALUES (?, ?, ?, ?)";

  $stmt = $con->prepare("INSERT INTO int_applicant (username, cdkey, dm_reason, pseudonym) VALUES (?,?,?,?)");
  $stmt->bind_param('ssss',$userName, $cdkey, $_POST['textbox'], $pseudonym);
  $stmt->execute();
  header('Location: index.php');
  exit();
}






?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>DM Interviews</title>
</head>
<link rel="stylesheet" type="text/css" href="css/interview.css">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<body>
<?php
  include "userinfo.php";
?>
  <!-- The sidebar -->


<!-- <div class="rightsidebar">

<div>
  

<img src="img/avalon.png" alt="DM Avalon Soul">
  </div>
  <div>
    

 <img src="img/Spyre.jpg" alt="Spyre">    
      </div>  
  <div>
    

  <img src="img/Dionysus.jpg" alt="DM Dionysus">    
      </div>
  <div>
    

  <img src="img/MoonMoon.png" alt="DM Snowcat">    
      </div>  
    <div>
    

  <img src="img/Monkey.jpg" alt="DM Monkey">    
      </div>  
 <div>
<img src="img/Starfish.jpg" alt="DM Starfish">
  </div>  
  <div>   
  <img src="img/Janitor.png" alt="DM Janitor">    
      </div>    
  
 
</div>
<div class="leftsidebar">
  <div>
<img src="img/Hoodoo.PNG" alt="DM Hoodoo">      
  </div>  
  <div>
<img src="img/Wraith.jpg" alt="DM Wraith">      
  </div>
  <div>
<img src="img/Butterfly.jpg" alt="DM Butterfly">      
  </div>
   <div>
  <img src="img/Snowcat.jpg" alt="DM Snowcat">    
      </div> 
    <div>   
  <img src="img/Potato.jpg" alt="DM Potato">    
      </div>  
      <div>   
  <img src="img/Strawhat.jpg" alt="DM Strawhat">    
      </div>  
    <div>    
  <img src="img/Zinzerena.jpg" alt="DM Zinzerena">    
      </div>  

</div> -->
<!-- Page content -->
<div class="content">
  <div class="header">
    

   <h1>
DM Team Application 
  </h1> 

<!-- <img src="img/tinkerbell.jpg" alt="DM Queen Titania"> 
    <img src="img/grumpycat.jpg" alt="Empress Grumpycat">  -->
    <h2 id = "qtext">
   
    </h2>
      <form method = "post">
        <!-- <label for = "character">Known/Current Character(s)</label>
       <input type = "text" id = "character" name = "character">
     -->    <br>
          <label for = "textbox"><h1>Type as little or as much as you like here regarding becoming a DM.</h1></label>
          <br />
      <textarea rows="7" cols="74" id = "textbox" name = "textbox"></textarea>
      <br>
   
    <input type = "submit" id = "next" value = "Submit" name = "next"> 
      <br>
     </form>
      </div>


</div>
  <!-- <footer><img src="img/Arelith.jpg" alt="Arelith"> </footer> -->
<input type="hidden" id="session_something" style="display:none" value="desired_value">
  


    


</body>
</html>