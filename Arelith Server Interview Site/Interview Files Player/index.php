<?php
require 'login.php';
require_once("data-config.php");
//Get interview state
$query = "SELECT status  as state, first_round_end_date, second_round_start_date, second_round_end_date from int_status where id = 1";
$state_results = $con->query($query);
$state_result = mysqli_fetch_assoc($state_results);

$interview_state = $state_result['state'];

$username = $_SESSION['username'];
$status_query = "SELECT interview_status, pseudonym from int_applicant where username = ?";


$stmt = $con->prepare($status_query);
$stmt->bind_param('s',$username);
$stmt->execute();
$status_results = $stmt->get_result();
$status_row = mysqli_fetch_assoc($status_results);
$pseudonym = $status_row['pseudonym'];

$status = $status_row['interview_status'];

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>DM Interviews</title>
</head>
<link rel="stylesheet" type="text/css" href="css/interview.css">
<body>
<?php
  include "userinfo.php";
?>
  <!-- The sidebar -->


<div class="rightsidebar">

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

</div>
<!-- Page content -->
<div class="content">
  <div class="header">
    

   <h1>
Welcome to Arelith's DM Interview     
  </h1> 
  
<img src="img/tinkerbell.jpg" alt="DM Queen Titania"> 
    <img src="img/grumpycat.jpg" alt="The Grumpycat"> 
      </div>
  <!-- Begin Player Only Section: This entire section is Player only.-->
<?php if($status == 'accepted'){ ?>
    <h2>
    Interview Status: Accepted
  </h2>
  <p>
  Congratulations, you have been accepted by the DM Team. Please reach out to DM Titania on forums, email, or discord for next steps.  
  </p>
  <footer><img src="img/Arelith.jpg" alt="DM Queen Titania"></footer>

   <?php }  elseif($status == 'completed'){?>
  <h2>
    Interview Status: Received
  </h2>
  <p>
    Congratulations, you have finished the DM Interview! Please wait for the DM Team to go over everyone's answer. On <?php echo $state_result['second_round_end_date'] ?>, you may relogin and check to see if you were accepted. If you have any questions, please message DM Queen Titania.
  </p>
  <footer><img src="img/Arelith.jpg" alt="DM Queen Titania"></footer>

   <?php }  elseif($status == 'denied'){ ?>
      <h2>
    Interview Status: Denied
  </h2>
  <h3>Pseudonym (DO NOT SHARE THIS WITH ANYONE! EVEN DMS!!): <?=$pseudonym?></h3> 
  <p>
 Thank you for taking the time to go through the DM Interview. We had many excellent canidates, and it was a challenge to choose who would be included. At this time, we have decided not to take you into the team.</p>
  
<p> You are welcome to re-apply to be interviewed the next time there is an opening on the DM Team.
  </p>
  </div>
  <footer><img src="img/Arelith.jpg" alt="DM Queen Titania"></footer>

   <?php }  elseif($status == 'first_cut' && $interview_state == 'round2' ){ ?>
  <p>
    You will have 3 minutes to complete each question. When you are done, the DM Team will go over your answers. You will be told when you may relogin and check to see if you were accepted. 
    If you run out of time, your answer will be saved as you type. You are limited to 500 characters per answer.
  </p>

 <p>
   When you are ready to go to the next question, click next. You cannot go back and change your answer.
  </p>

  <p>
    When you are ready to start, click the Start button. The first timer will begin as soon as you hit the button, and will continue even if you close the browser.
  </p>
</div>
  <footer><img src="img/Arelith.jpg" alt="DM Queen Titania"><form method = "post" action = "questions.php" onsubmit="return confirm('Are you ready? Once you start, the timer for the first question begins.');">   
   <input type = "submit" value = "Start" id = "start"></form></footer>
 <?php }
elseif($status == 'first_cut' && $interview_state == 'round1.5'){ ?>
    <h2>
    Interview Status: Passed to second round.
  </h2>
  <h3>Pseudonym (DO NOT SHARE THIS WITH ANYONE! EVEN DMS!!): <?=$pseudonym?></h3> 
  <p>
  Congratulations, you have been accepted to the question process. The questions will start on <?php echo $state_result['second_round_start_date'] ?> and be open until <?php echo $state_result['second_round_end_date'] ?>. You must complete it in this time else you will not be taken as a DM.
  </p>
  <footer><img src="img/Arelith.jpg" alt="DM Queen Titania"></footer>

   
   <?php }  elseif($interview_state == 'round1' && ($status == 'preliminary' || $status == 'first_cut')){ ?>
    <h2>
      Interview Status: Under Review
    </h2>
    <h3>Pseudonym (DO NOT SHARE THIS WITH ANYONE! EVEN DMS!!): <?=$pseudonym?></h3> 
    <p>
      You have finished the first round of the DM Interview. DM Titania and Grumpycat will go over all first round canidates. This site will update on <?php echo $state_result['first_round_end_date'] ?> with the results.
    </p>
  </div>
    <footer><img src="img/Arelith.jpg" alt="DM Queen Titania"></footer>
   <?php }
  elseif(empty($status) && $interview_state == 'round1'){ ?>
   <p>
    You will have unlimited time to answer a few questions. When you are done, the Head DM and Community Manager will review your responses. You will be told when you may relogin and check to see if you were accepted into the next round.
     This section must be completed by <?php echo $state_result['first_round_end_date'] ?>.
  </p>

 <p>
   Your answer cannot be edited once submitted, so please take your time, and fill in as little or as much into your answer as you need.
  </p>
</div>
  <footer><img src="img/Arelith.jpg" alt="DM Queen Titania"><form method = "post" action = "application.php" onsubmit="return confirm('Ready?');">   
   <input type = "submit" value = "Start" id = "start"></form></footer>
</div>

<?php } 
else{ ?>
  <h2>
    Interview Status: Closed
  </h2>
  <p>
    The DM Team is not taking any canidates at this time. When they are opened, a message will be made in forum and discord announcements.
  </p>
</div>
  <footer><img src="img/Arelith.jpg" alt="DM Queen Titania"></footer>
 <?php } ?>

  <!-- End Player Only Section: This entire section is Player only.-->
</body>
</html>