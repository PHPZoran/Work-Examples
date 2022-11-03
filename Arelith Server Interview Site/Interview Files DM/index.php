<?php 
//Move this code somewhere


require 'login.php';
//  if(!isset($_SESSION["DM"])){
// 	 header('Location: dm_login.php');
//  }
require_once("data-config.php");
$a_deleted = false;
$q_created = false;
$q_updated = false;
$q_deleted = false;
// $admin = false;

// if($_SESSION[''])
// if($_SESSION['DM'] == 'Queen Titania' || $_SESSION['DM'] == 'Irongron' || $_SESSION['DM'] == 'Grumpycat' || $_SESSION['DM'] == 'Spyre'){
//   $admin = true;

// }
if(isset($_POST['scramble'])){
  $number = intval($_POST['number']);
  if($number < 1 || !is_int($number)){
  $error = "Number is too low or not an integer!";
    echo $error;
  }    
  else{
    //First, clear all current questions.
   $query = "UPDATE int_question set sequence = NULL"; 
     $clear = $con->query($query);
  $query = "SELECT * FROM int_question where integral = 1 and deleted = 0 order by RAND() LIMIT ?";  
     $stmt = $con->prepare($query);
   $stmt->bind_param('s',$number);
  $stmt->execute();
    $integrals = $stmt->get_result();
    $sequence = 0;
    foreach ($integrals as $key => $integral){
     $sequence = $key+1;
    $update = "UPDATE int_question set sequence = '$sequence' where q_id = '{$integral['q_id']}'"; 
     $question_update = $con->query($update);
    }
    //Now get non-core questions.
    $query = "SELECT * FROM int_question where integral != 1 and deleted = 0 order by RAND() LIMIT ?";  
    $number = $number - $sequence;
     $stmt = $con->prepare($query);
   $stmt->bind_param('s',$number);
  $stmt->execute();
    $non_integrals = $stmt->get_result();
     foreach ($non_integrals as $key => $non_integral){
     $sequence++;
    $update = "UPDATE int_question set sequence = '$sequence' where q_id = '{$non_integral['q_id']}'"; 
     $question_update = $con->query($update);
    }
  }
  }
  //Update Interview Status, can be round 1, pre-round 2, round 2, or closed.
  if(isset($_POST['update_status'])){
  $date = date('Y-m-d');
  $query = "UPDATE int_status set status = ?, date_modified = '$date' where id = 1";
  $query = mysqli_prepare($con,"INSERT INTO int_status
  (id, status, first_round_end_date, second_round_start_date, second_round_end_date, date_modified) 
VALUES 
  (1, ?,?,?,?,'$date')
ON DUPLICATE KEY UPDATE
  status = ?, first_round_end_date = ?, second_round_start_date = ?, second_round_end_date = ?,date_modified = '$date'");
 mysqli_stmt_bind_param($query, "ssssssss",$_POST['set_status'],$_POST['first_end_date'],$_POST['second_start_date'],$_POST['second_end_date'],$_POST['set_status'],$_POST['first_end_date'],$_POST['second_start_date'],$_POST['second_end_date']);
 mysqli_stmt_execute($query);   
  }
//
if(isset($_POST['deleteApplicant'])){ 

$update = "DELETE FROM int_applicant where A_ID = ?";
    	$stmt = $con->prepare($update);

$stmt->bind_param('s', $_POST['Applicant_ID']);	
	$stmt->execute();
  $a_deleted = true;   
}
//Add Question
if(isset($_POST['submitQ'])){
$integral = intval($_POST['integral']);
  $insert = "INSERT INTO int_question (Question,integral) VALUES (?,?)";
    	$stmt = $con->prepare($insert);

$stmt->bind_param('ss', $_POST['Question'],$integral);	
	$stmt->execute();
  $q_created = true;
}
//Update Question
if(isset($_POST['submitQ2'])){
  $integral = intval($_POST['integral']);
$update = "UPDATE int_question set Question = ?, integral = ? where Q_ID = ?";
    	$stmt = $con->prepare($update);

$stmt->bind_param('sss', $_POST['Question'],$integral,$_POST['question_id']);	
	$stmt->execute();
  $q_updated = true;  
}
//Delete Question
if(isset($_POST['submitQ3'])){ 

$update = "UPDATE int_question set deleted = 1 where Q_ID = ?";
    	$stmt = $con->prepare($update);

$stmt->bind_param('s', $_POST['question_id']);	
	$stmt->execute();
  $q_deleted = true;   
}
//Get Round status and dates 
$round_query = "SELECT status, first_round_end_date, second_round_start_date, second_round_end_date from int_status where id = 1";
$round = $con->query($round_query);
$interview = mysqli_fetch_assoc($round);
//Get Questions
$q_query = "Select Q_ID, QUESTION FROM int_question where deleted = 0 order by Q_ID";
	$stmt = $con->prepare($q_query);

	$stmt->execute();
	$questions = $stmt->get_result();

// Get Applicants
$a_query = "SELECT A_ID, username, pseudonym FROM int_applicant where deleted = 0 and interview_status = 'completed' order by A_ID";
	$stmt = $con->prepare($a_query);

	$stmt->execute();
	$applicants = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Main Menu</title>
<link rel="stylesheet" type="text/css" href="css/interview.css">
 <link href="https://fonts.googleapis.com/css?family=Cinzel+Decorative|Lato&display=swap" rel="stylesheet">  

  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

</head>
<!-- <body style="background:url('css/Images/Cloud.gif> -->
<body>
<?php
  include "userinfo.php";
?>
<div id = "addContainer">
  <h1>
    DM Interviews
     </h1>
    <h2>
    Navigation
     </h2>
         <button onclick="window.location.href = 'dm_intcandidate.php';"  >
      Go to First Candidate
    </button>
    <form method="get" action="dm_intcandidate.php" style="display:inline-block">
    <input type="submit" value="Go to Candidate:" name="goCandidate">
  <select name="candidate_number" class = "candidate">
    <?php foreach($applicants as $candidate){?>
  <option class = "candidate" value="<?php echo $candidate['A_ID'];?>"><?php echo $candidate['pseudonym'];?></option>  
  <?php } ?> </select>
    </form>

     <button onclick="window.location.href = 'dm_intsummary.php';">
      Go to End Page
    </button>
    <?php if($admin == true){ ?>
    <button onclick="window.location.href = 'dm_applications.php';">
      Go to Round 1
    </button>
    <?php } ?>
    <br>
    <?php if($_SESSION["isAdmin"]){ ?>
    <div id = "admin-panel">
    
    <form method="post">
      <h2 style="color:green;">
        Admin Panel
      </h2>
      <label for = "number"># of Questions</label>
  <input type = "number" name = "number" id = "number"></input><br>
  <input type = "submit" name = "scramble" value = "Scramble">
           <br>
     <h2>
    Interview Status 
  </h2>
  <h3>Dates</h3>
  <br>
 <label for="first_end_date">First End Date:</label>
 <input type="date" id="first_end_date" name="first_end_date" value = "<?php echo $interview['first_round_end_date']; ?>">
  <label for="second_start_date">Second Start Date:</label>
<input type="date" id="second_start_date" name="second_start_date" value = "<?php echo $interview['second_round_start_date']; ?>">
<label for="second_end_date">Second End Date:</label>
<input type="date" id="second_end_date" name="second_end_date" value = "<?php echo $interview['second_round_end_date']; ?>">
<br>
<h3>Current Round</h3>
<br>
  <label for = "round1">Round 1</label>
  <input type = "radio" title="Round 1" <?php if($interview['status'] == 'round1') {echo "checked";} ?> name = "set_status" value="round1" id = "round1"/>
   <label for = "round2">Round 2</label>
 <input type = "radio" title="Round 2" <?php if($interview['status'] == 'round2') {echo "checked";} ?> name = "set_status" value="round2" id = "round2"/>
   <label for = "closed">Closed</label>
   <input type = "radio" title="Closed" <?php if($interview['status'] == 'closed') {echo "checked";} ?> name = "set_status" value="closed" id = "closed"/>
  <input type = "submit" name = "update_status" value = "Update Interview Status">
 </form> 
   </div> <?php } ?>
  </div>
  <br>
  <div class = "addApplicantQuestion">
 
      <h1>
    Applicants
     </h1>

  <ul class = "a">
  <?php  foreach($applicants as $applicant){?>
  <li><?php echo $applicant['pseudonym'] ?></li>
    <?php } ?>
  </ul>
 
   </div> 
  
<div class = "addApplicantQuestion">
  <br>
<?php if($admin == true){ ?>
  <form method="post">
           <h1>
   Remove Applicant 
  </h1>
       <?php if($a_deleted == true){ ?>
    <p>
     Applicant Removed
   </p> 
     <br>
    <?php } ?>
  
         <select name="Applicant_ID" class = "question">
    <?php foreach($applicants as $applicant){?>
  <option class = "question" value="<?php echo $applicant['A_ID'];?>"><?php echo $applicant['pseudonym'];?></option>  
    <?php } ?>
          </select>
    <input type="submit" value="Delete Applicant" name="deleteApplicant">
  </form>
  <?php } ?>
  </div>
  <br>
 

   </div> 
     <div class = "addApplicantQuestion">
 
      <h1>
    Questions
     </h1>
 
  <ul id = "list_question">
  <?php  foreach($questions as $question){?>
  <li><?php echo $question['QUESTION'] ?></li><br>
    <?php } ?>
  </ul>
 
   </div> 
<div class = "addApplicantQuestion">
  <form method="post" action="index.php#questionInput">
        <h1>
   Add/Update Question 
  </h1>
<?php if($q_created == true){ ?>
    <p>
     Question Created, make sure to hit scramble in the admin panel to have them available for interview.
   </p>
   <br>
   <?php } if($q_updated == true){ ?>
    <p>
     Question Updated
   </p>
   <br>
   <?php } ?> 
     <textarea placeholder="Question" name="Question" id = "questionInput"  rows="6" cols="68"></textarea><br>
     <input type="checkbox" id="integral" name="integral" value="1">
<label for="integral">Integral</label>
     <input type="submit" value="Add Question" name="submitQ">
    Or
    <input type="submit" value="Update Question" name="submitQ2">
      <select name="question_id" class = "question">
    <?php foreach($questions as $question){?>
  <option class = "question" value="<?php echo $question['Q_ID'];?>"><?php echo substr($question['QUESTION'],0,36); if(strlen($question['QUESTION']) > 36){echo "...";}?></option>  
    <?php } ?>
          </select>
  </form> 
   <form method="post" action="index.php#questionInput">
           <h1>
   Delete Question
  </h1>
       <?php if($q_deleted == true){ ?>
    <p>
     Question Removed
   </p> 
     <br>
    <?php } ?>
  
      <select name="question_id" class = "question">
    <?php foreach($questions as $question){?>
  <option class = "question" value="<?php echo $question['Q_ID'];?>"><?php echo substr($question['QUESTION'],0,36); if(strlen($question['QUESTION']) > 36){echo "...";}?></option>  
    <?php } ?>
          </select>
    <input type="submit" value="Delete Question" name="submitQ3">
  </form>
  </div> 
  <br>

  </body>