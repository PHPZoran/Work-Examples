<?php
require 'login.php';

require_once("data-config.php");
// This is cool, I'm using ?? now.

$candidateID = $_GET['candidate_number'] ??  0;

// this is the alternative to having no results. If the isSummary in GET is anything other than 0, then it'll display summary.
$isSummary = $_GET['isSummary'] ?? 0;

//Fetch All Candidates. Select the one the POST indicates. 
// $query = mysqli_prepare($con,"SELECT a_id, username FROM int_applicant where deleted = 0 and interview_status = 'complete' LIMIT ?,1");
// Okay, I'll delete the person I need to kill.
// we don't need a deleted check anymore, I was going to drop the column entirely, since it's based off forum login/credentials.
// If we want to delete them we should just remove them from the table.
// So Mord? Okay, he'll still find a way to add himself back in.
// Wait why did you put both queries in there!! It needs to be an else! >:(
// I think that was you, not me. :P

$candidateUsername = "";
if($candidateID){
  $getUsernameSQL = mysqli_prepare($con, "SELECT username FROM int_applicant WHERE a_id=?");
  mysqli_stmt_bind_param($getUsernameSQL, "s", $candidateID);
  mysqli_stmt_execute($getUsernameSQL);

  $result = mysqli_stmt_get_result($getUsernameSQL);

  if($row = mysqli_fetch_array($result))
    $candidateUsername = $row['username'];
}
  if(isset($_POST['yes'])){
    $decision = 'Yes';
    $vote = "INSERT INTO int_votes (related_a_id,related_dm_id,dm_vote) VALUES (?,?,?) ON DUPLICATE KEY UPDATE dm_vote = ?";
    $stmt = $con->prepare($vote) or die($con->error);
  $stmt->bind_param('ssss', $candidateUsername,$userName,$decision,$decision) or die($con->error);	
  $stmt->execute() or die($con->error);
  }
  if(isset($_POST['no'])){
    $decision = 'No';
    $vote = "INSERT INTO int_votes (related_a_id,related_dm_id,dm_vote) VALUES (?,?,?) ON DUPLICATE KEY UPDATE dm_vote = ?";
    $stmt = $con->prepare($vote) or die($con->error);
  $stmt->bind_param('ssss', $candidateUsername,$userName,$decision,$decision) or die($con->error);	
  $stmt->execute() or die($con->error);
  }

  if(!$candidateID){
    $query = mysqli_prepare($con, "SELECT a_id, username, pseudonym FROM int_applicant WHERE interview_status = 'completed' LIMIT 1");
  }
  else{
    $query = mysqli_prepare($con, "SELECT a_id, username, pseudonym FROM int_applicant WHERE a_id=?");
    mysqli_stmt_bind_param($query, "s",$candidateID);
  }
  /*
  // if(empty($_POST['candidate_number'])){
  $// number = 0;
  }
  else// {
  $// number = // $_POST['candidate_number'];
  }
  mys*/
  mysqli_stmt_execute($query);
  $results = mysqli_stmt_get_result($query);         
  $candidate = mysqli_fetch_assoc($results); 

  $candidateID = $candidate['a_id'];

// If no candidate is specified, retrieves the first from the table, else get the specified candidate ID.
  $query = mysqli_prepare($con, "SELECT a_id, username, pseudonym FROM int_applicant WHERE interview_status = 'completed' and a_id < ? ORDER BY a_id DESC LIMIT 1"); 
  mysqli_stmt_bind_param($query, "s",$candidateID);
  mysqli_stmt_execute($query);
  $results = mysqli_stmt_get_result($query);


  $previousApplicantID = "";

  if($row = mysqli_fetch_array($results)){
    $previousApplicantID = $row['a_id'];
  }
  mysqli_stmt_close($query);

  $query = mysqli_prepare($con, "SELECT a_id, username, pseudonym FROM int_applicant WHERE interview_status = 'completed' and a_id > ? ORDER BY a_id ASC LIMIT 1"); 
  mysqli_stmt_bind_param($query, "s",$candidateID);
  mysqli_stmt_execute($query);
  $results = mysqli_stmt_get_result($query);

  $nextApplicantID = "";

  if($row = mysqli_fetch_array($results)){
    $nextApplicantID = $row['a_id'];
  }
  mysqli_stmt_close($query);  


// echo "testing";
// var_dump($candidate);
// if(empty($candidate)){
//   if(!isset($_POST['previous'])){
//   header("Location: dm_intsummary.php");
//   }
//   else{
//     header("Location: index.php");
//   }
//   exit();
// } 
//TODO If we get no result, we should go to the conclusion page.
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Interviews</title>
  <link rel="stylesheet" type="text/css" href="css/interview.css">
  <link href="https://fonts.googleapis.com/css?family=Cinzel+Decorative|Lato&display=swap" rel="stylesheet">  
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
  <script src="https://kit.fontawesome.com/28f90f102d.js" crossorigin="anonymous"></script>
</head>
<!-- I miss you cloud <body style="background:url('css/Images/Cloud.gif');"> -->
<body>
  <?php
  include "userinfo.php";
  ?>
  <div id = "interviewContainer">
    <h1>
      DM Interviews
    </h1>
    <form method = "get">
      <input type = "hidden" value = "<?php echo $previousApplicantID; ?>" name="candidate_number">
      <input type = "submit" value = "Previous Candidate" name = "previous" <?php if(!$previousApplicantID){ echo "disabled";}?>>
    </form>

    <form method = "get">
      <input type = "hidden" value = "<?php echo $nextApplicantID;?>" name = "candidate_number">
      <input type = "submit" value = "Next Candidate" name = "next" <?php if(!$nextApplicantID){ echo "disabled"; } ?>>
    </form>
    <form action = "dm_intsummary.php">
      <input type = "submit" value = "Go To Summary" name = "next">
    </form>
</form>
    <!-- TODO: Make CSS do this margin instead of two BR's -->
    <br>
    <br>
    <!-- TODO: Display psuedoname here instead -->
    <h2> 
      <?php echo $candidate['pseudonym']; ?>
</h2>
<?php
    $query = mysqli_prepare($con,"SELECT q_id, question, sequence, response FROM int_question 
    LEFT JOIN int_response on question_id = q_id 
    where int_question.deleted = 0 and sequence IS NOT NULL and sequence != '' and username = ?");
    mysqli_stmt_bind_param($query, "s",$candidate['username']);
    mysqli_stmt_execute($query);
    $questions = mysqli_stmt_get_result($query);  
    foreach($questions as $question){ ?>
    
<h3>
  <?php echo $question['question']; ?>
</h3>
<br>
<table id='dataTable'>
  <thead>
    <tr>
      <th>Answer</th><th>Vote</th><th>Comment</th><th>Save</th><th>Other's Comments</th>
    </tr>
  </thead>
  <tbody>
    <tr>
      <td><textarea col = "40" row = "50" style="width:250px;" disabled><?php echo $question['response']; ?></textarea></td>
    <!-- TODO: Decide/Commit to voting style. Below is placeholder only. -->
    <?php $mycomment_query = mysqli_prepare($con,"SELECT Vote_Comment,Vote_Int from int_qadm where r_q_id = ? and r_a_id = ? and r_dm_id = ?");
     mysqli_stmt_bind_param($mycomment_query, "sss",$question['q_id'],$candidate['username'],$userName);
     mysqli_stmt_execute($mycomment_query);
     $mycomments = mysqli_stmt_get_result($mycomment_query);  
     $mycomment = mysqli_fetch_assoc($mycomments);?>  
    <td>      
        <select name="vote">              
        <option value="4">Ottimo</option>  
            <option value="3" <?php if($mycomment['Vote_Int'] == 3){ echo "selected"; }?>>Buono</option>  
            <option value="2" <?php if($mycomment['Vote_Int'] == 2){ echo "selected"; }?>>Discreto</option>   
            <option value="1" <?php if($mycomment['Vote_Int'] == 1){ echo "selected"; }?>>Insufficiente</option>   
            <option value="0" <?php if($mycomment['Vote_Int'] == 0){ echo "selected"; }?>>F</option>            
        </select>
      </td>      
      <td>
     
        <textarea name = "dm_comment"><?php echo $mycomment['Vote_Comment'];?></textarea>
      </td>
      <td>
      <button type = "submit" class = "vote" name = "vote" value = "vote"><i class="fa-solid fa-check-to-slot"></i></button>
      </td>
     <td><?php
     $comment_query = mysqli_prepare($con,"SELECT Vote_Comment, R_DM_ID from int_qadm where r_q_id = ? and r_a_id = ? and r_dm_id != ?");
     mysqli_stmt_bind_param($comment_query, "sss",$question['q_id'],$candidate['username'],$userName);
     mysqli_stmt_execute($comment_query);
     $comments = mysqli_stmt_get_result($comment_query);  
     foreach($comments as $comment){
    $commentstring .= $comment['R_DM_ID'] . ': ' . $comment['Vote_Comment'] . "\r\n";
     }?>
    <textarea col = "40" row = "50" style="width:250px;" readonly>
<?php echo $commentstring; ?>
    </textarea>
      <input type = "hidden" class = "question_id" name = 'question_id' value =  "<?php echo $question['q_id'] ?>">
     
     </td>
    </tr>
  </tbody>
</table>
<br>

<?php $commentstring = '';} ?>

<br> 

<button type = "submitall" name = "action" value = "action" class = "action-all">Save All <i class="fa-regular fa-clipboard"></i></button>
<form method = "post">
  <br>
  <h3>Final Vote For DM Team: </h3>
  <br>
  <?php
  // <input type = "hidden" value = "<?php echo $candidate['username'];" name = "candidate_username">
  ?>
  <input type = "hidden" value = "<?php echo $candidate['a_id'];?>" name = "candidate_number">
  <input style="font-family: FontAwesome" value="&#xf164;" type="submit" name = "yes">
  <input style="font-family: FontAwesome" value="&#xf165;" type="submit" name = "no">
</form>
<script>
// goodbye dm_intcandate, when I see you again I hope you're all grown up and finished. I'm going off to the war that is dm interview player page, keep me in your prayers.
   $(document).ready(function(){
    $(".vote").click(function(){
     var currentTR = $(this).closest('tr');    
     var button = $(this);
     var drop = currentTR.find("select");
     var vote = drop.val();
     var comment = currentTR.find("textarea[name='dm_comment']").val();
     var character = '<?php echo $candidate['username']; ?>';
     var dm = '<?php echo $userName ;?>';
     var question_class = currentTR.find("input[name='question_id']");
     var question = question_class.val();
     $.ajax({
      type: "POST",
      url: 'dm_vote.php',
      data: {comment:comment,vote:vote,dm:dm,character:character,question:question},
      success: function(response)
      {      
          console.log(response);
          button.css("background-color", "green");
          button.css("color", "AliceBlue");                
      }

    });
  });
  $(".action-all").click(function(){
    
    $('.vote').each(function(){
  
    var currentTR = $(this).closest('tr');    
     var button = $(this);
     var drop = currentTR.find("select");
     var vote = drop.val();
     
     var comment = currentTR.find("textarea[name='dm_comment']").val();
     var character = '<?php echo $candidate['username']; ?>';
     var dm = '<?php echo $userName ;?>';
     var question_class = currentTR.find("input[name='question_id']");
     var question = question_class.val();
     $.ajax({
      type: "POST",
      url: 'dm_vote.php',
      data: {comment:comment,vote:vote,dm:dm,character:character,question:question},
      success: function(response)
      {      
          console.log(response);
          button.css("background-color", "green");
          button.css("color", "AliceBlue");                
      }

    });

 });
 
  });
});
</script>
    </body>
    </html>