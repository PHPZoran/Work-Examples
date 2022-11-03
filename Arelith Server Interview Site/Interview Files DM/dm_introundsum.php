<?php
require 'login.php';

// session_start();

//  if(!isset($_SESSION["DM"])){
// 	 header('Location: dm_login.php');
//  }
require_once("data-config.php");
// $admin = false;
// if($_SESSION['DM'] == 'Queen Titania' || $_SESSION['DM'] == 'Irongron' || $_SESSION['DM'] == 'Grumpycat' || $_SESSION['DM'] == 'Spyre'){
//   $admin = true;

// }

// Check if dm is initialised in local DB, if not, insert it into table.
$dmExistsSql = mysqli_prepare($con, "SELECT * FROM `int_dm` WHERE `dm` = ?");
mysqli_stmt_bind_param($dmExistsSql, "s", $userName);
mysqli_stmt_execute($dmExistsSql);

$dmResult = mysqli_stmt_get_result($dmExistsSql);

if(!mysqli_num_rows($dmResult)){
  $newDMSql = mysqli_prepare($con, "INSERT INTO int_dm (dm) VALUES (?)");
  mysqli_stmt_bind_param($newDMSql, "s", $userName);
  mysqli_stmt_execute($newDMSql); 
}

if(isset($_POST['question']) && is_numeric($_POST['question'])){
   $number = $_POST['question'];

}
elseif(!empty($_GET['question']) && is_numeric($_GET['question']) && $_GET['question'] > 1){
  $number = $_GET['question'];

} 

else{
  $number = 1;
} 


$q_query = "Select Q_ID, QUESTION, Deleted FROM int_question where Q_ID = ? LIMIT 1";
	$stmt = $con->prepare($q_query);
	$stmt->bind_param('s', $number);	
	$stmt->execute();
	$search = $stmt->get_result();
$question = mysqli_fetch_array($search);
if($question['Q_ID'] == ''){

//  header('Location: dm_intconcluded.php');
} 
elseif($question['Q_ID'] != '' && $question['Deleted'] == 1){
  $number = $number -1;
  header('Location: dm_introundsum.php?question=' . $number);  
}
elseif($question['Deleted'] == 1){
  $number++;
  header('Location: dm_interviews.php?question=' . $number);
}
$a_query = "SELECT a_id, username FROM int_applicant where deleted = 0 order by a_id asc";
	$stmt = $con->prepare($a_query);
	$stmt->execute();

	$result = $stmt->get_result();

  $comment_query = "SELECT Vote_Comment FROM int_qadm where r_a_id = ? and r_q_id = ? and r_dm_id = ?";
$aq_query = "SELECT SUM(CASE WHEN Vote_Text = 'Excellent Answer' THEN 1 ELSE 0 END) as excellent, SUM(CASE WHEN Vote_Text = 'Satisfactory Answer' THEN 1 ELSE 0 END) as satisfactory, SUM(CASE WHEN Vote_Text = 'Unsatisfactory Answer' THEN 1 ELSE 0 END) as unsatisfactory, SUM(CASE WHEN Vote_Text = 'Alarming Answer' THEN 1 ELSE 0 END) as alarming FROM int_qadm where r_a_id = ? and r_q_id = ?";

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Round Summary</title>
<link rel="stylesheet" type="text/css" href="css/interview.css">
 <link href="https://fonts.googleapis.com/css?family=Cinzel+Decorative|Lato&display=swap" rel="stylesheet">  
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
</head>
<body style="background:url('css/Images/Cloud.gif');">
<?php
  include "userinfo.php";
?>
<div id = "interviewContainer">
  <h1>
    DM Interviews
     </h1>
     <button onclick="window.location.href = 'dm_interviews.php?question=<?php echo $number;?>';">
      Back
    </button>
     <button onclick="window.location.href = 'index.php'">
      Main Menu
    </button>
     <button onclick="window.location.href = 'dm_interviews.php?question=<?php echo $number+1;?>';">
      Next Question
    </button>
     <button onclick="window.location.href = 'dm_intsummary.php';" style = "">
      Go to End Page
    </button>   
 
  </div> 
<div id = "interviewQuestion">
  <h1>
      Question #<span><?php echo $question['Q_ID']; ?></span> Summary
  </h1>

  </div> 
<div id="tableContainer">
 <table id='dataTable'>
    <thead>
      <tr>
      <th> Character </th>     
        <th> Response </th> 
                    <?php if($admin == true) { ?>
       	 <th>
      Remove
       </th>  
        <?php } ?>
        <th>Excellent Votes</th>
        <th>Satisfactory Votes</th>
        <th>Unsatisfactory Votes</th>
        <th>Alarming Votes</th>
      
     
      <?php 
      
  //     $d_query = "SELECT dm_id, dm FROM int_dm where deleted = 0 order by dm_id asc";
  //       	$stmt = $con->prepare($d_query);
       
	// $stmt->execute();
	// $dms = $stmt->get_result(); 

  $DMSQuery = "SELECT dm_id, dm FROM int_dm";
  $DMSQueryPrep = mysqli_prepare($con, $DMSQuery) or die(mysqli_error($con));
  mysqli_stmt_execute($DMSQueryPrep);
  
  $DMSResult = mysqli_stmt_get_result($DMSQueryPrep);
  $dms = array();

  while($row = mysqli_fetch_array($DMSResult)){
    array_push($dms, $row);
  }

  foreach($dms as $dm){ 
        
        ?>
         <th> <?php echo $dm['dm']?>'s Comments</th>    
        <?php } ?>
         <th>Question Points</th>
        <th>Total Points</th>
        </tr>
    </thead>
    <tbody>
  <?php foreach($result as $r){

  	$stmt = $con->prepare($aq_query);
	$stmt->bind_param('ss', $r['username'],$number);	
	$stmt->execute();
	$answers = $stmt->get_result();
   $answer = mysqli_fetch_array($answers);
      ?>
        <tr>
         <td contenteditable="false"> <span class="name" id = "<?php echo $r['A_ID'];?>"><?php echo $r['username'];?></span></td>
           <td><?php
             $response_query = "SELECT response from int_response where username = ? and question_id = ?";
             	$stmt = $con->prepare($response_query);
	$stmt->bind_param('si', $r['username'],$number);	
	$stmt->execute();
	$responses = $stmt->get_result();
$response = mysqli_fetch_array($responses); 
             ?> <textarea  rows="3" cols="33" disabled> <?php echo $response['response'];?></textarea></td>
            <?php if($admin == true) { ?>
       	 <td><button class = "remove">
      Remove
       </button></td>
   <?php } 
                   
              ?>
           <td><?php echo $answer['excellent'];?></td>
           <td><?php echo $answer['satisfactory'];?></td>
          <td><?php echo $answer['unsatisfactory'];?></td>
          <td><?php echo $answer['alarming'];?></td>
      
  <?php

            
      foreach($dms as $dm){
                   
  	$stmt = $con->prepare($comment_query);
	$stmt->bind_param('sss', $r['username'], $number, $dm['dm']);	
	$stmt->execute();
	$answers = $stmt->get_result();
   $answer = mysqli_fetch_array($answers);
               if($answer['Vote_Comment'] != ''){ ?> 
         
          <td><textarea  rows="3" cols="33" disabled> <?php echo $answer['Vote_Comment'];?></textarea></td> 
          <?php } else{ ?>
    
         
          <td>No Comment</td> 
       
    <?php } }
  $sum = "SELECT count(Vote_Int) as sum
FROM int_qadm where r_a_id = ? and r_q_id = ?";
          $stmt = $con->prepare($sum);
	$stmt->bind_param('ss', $r['A_ID'],$number);	
	$stmt->execute();
	$grab_sum = $stmt->get_result();
   $question_total = mysqli_fetch_array($grab_sum);?> 
          <td><?php echo $question_total['sum'];?></td> <?php   
  $total_sum = "SELECT count(Vote_Int) as sum
FROM int_qadm where r_a_id = ?";
          $stmt = $con->prepare($total_sum);
	$stmt->bind_param('s', $r['A_ID']);	
	$stmt->execute();
	$grab_total_sum = $stmt->get_result();
   $sum_total = mysqli_fetch_array($grab_total_sum);?> 
          <td><?php echo $sum_total['sum'];?></td>
      </tr>
   <?php } ?>
      
    </tbody>

  </table>  
      </div>
  <br>
</body>
  <script>$(document).ready(function(){

    $(".remove").click(function(){

  
         var currentTR = $(this).closest('tr');
        var iname = currentTR.find("span.name"); 
        var data_character = iname.attr('id');
         var data_remove = true;
                $.ajax({
            type: "POST",
            url: 'dm_vote.php',
            data: {data_remove:data_remove,data_character:data_character},
            success: function(response)
            {
          currentTR.remove();             
              } 



       });
  
        

  
  });
});</script>
</html>