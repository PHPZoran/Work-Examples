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
if(isset($_POST['question']) && is_numeric($_POST['question'])){
 $number = $_POST['question'];

}
elseif(!empty($_GET['question']) && is_numeric($_GET['question']) && $_GET['question'] > 1){
  $number = $_GET['question'];

} 

else{
  $number = 1;
} 
$previous = $number -1;

$q_query = "Select Q_ID, QUESTION, Deleted FROM int_question where Q_ID = ? LIMIT 1";
$stmt = $con->prepare($q_query);
$stmt->bind_param('s', $number);	
$stmt->execute();
$search = $stmt->get_result();
$question = mysqli_fetch_array($search);
if($question['Q_ID'] == ''){

  header('Location: dm_intsummary.php');
} 
elseif($question['Deleted'] == 1){
  $number++;
  header('Location: dm_interviews.php?question=' . $number);
}



$a_query = "SELECT A_ID, username FROM int_applicant where deleted = 0 order by A_ID asc";
// $applicantQueryPrepared = mysqli_prepare($con, $a_query);
// mysqli_stmt_execute($applicantQueryPrepared);

// $result = mysqli_stmt_get_result($applicantQueryPrepared);

// while($row = mysqli_stmt_get_result($banSqlprepare)){
//   var_dump($row);
// }

$stmt = $con->prepare($a_query);
$stmt->execute();
$result = $stmt->get_result();

$applicants = array();
// var_dump($result);
while($row = $result->fetch_array()){
  array_push($applicants, $row);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Interviews</title>
  <link rel="stylesheet" type="text/css" href="css/interview.css">
  <link href="https://fonts.googleapis.com/css?family=Cinzel+Decorative|Lato&display=swap" rel="stylesheet">  
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
</head>
<!-- <body style="background:url('css/Images/Cloud.gif');"> -->
<body>
  <?php
  include "userinfo.php";
  ?>
  <div id = "interviewContainer">
    <h1>
      DM Interviews
    </h1>
    <button onclick="window.location.href = 'dm_introundsum.php?question=<?php echo $previous;?>';"  <?php if($number == 1){ echo "disabled";}?>>
      Previous
    </button>
    <button onclick="window.location.href = 'index.php'">
      Main Menu
    </button>
    <button onclick="window.location.href = 'dm_introundsum.php?question=<?php echo $number;?>';">
      Next
    </button>

    
  </div> 
  <div id = "interviewQuestion">
    <h1>
      Question #<span><?php echo $question['Q_ID']; ?></span>
    </h1>
    <p>
      <?php echo $question['QUESTION']; ?>
    </p>
  </div>  
  <div id = 'tableContainer'>
    <table id='dataTable'>
      <thead>
        <tr>
          <th> Character </th>     
          <th> Copied Response (Optional) </th>     
          <th>  Modify/Make Vote </th> 
          <th>  Modify/Make Comment </th> 
          <th>  Commit Actions </th> 
          <?php if($admin == true) { ?>
            <th>Remove</th>
          <?php } ?>
        </tr>
      </thead>
      <tbody id = "tbody">
        <?php 
        // var_dump($applicants);
        $x = 0;
        foreach($applicants as $test){
          // var_dump($test);
        }
          foreach($applicants as $r){ 
            var_dump($test);
            $aq_query = "SELECT * FROM int_qadm where r_a_id = ? and r_q_id = ? and r_dm_id = ?";
            $stmt = $con->prepare($aq_query);
            $stmt->bind_param('sss', $r['a_id'],$number,$_SESSION['ID']);	
            $stmt->execute();
            $answers = $stmt->get_result();
            $answer = mysqli_fetch_array($answers);
            ?>
            <tr class = "<?php if($answer['Vote_Text'] == 'Alarming Answer'){ echo "red";}?>"> 
            
              <td contenteditable="false"> <span class="name" id = "<?php echo $r['a_id'];?>"><?php echo $r['username'];?></span></td>
              <td><?php
              $response_query = "SELECT username, response from int_response where username = ? and question_id = ?";
              $stmt = $con->prepare($response_query);
              $stmt->bind_param('si', $r['username'],$number) or die($stmt->error);	
              $stmt->execute();
              $responses = $stmt->get_result();
              $response = mysqli_fetch_array($responses); 
              ?> 
              <textarea placeholder="Optional: Copy Response Here" name="comment" id = "contactMessage"  rows="3" cols="33"><?php echo $response['response'];?><?php 

            ?></textarea></td>
            <td> 
              <select name="vote">
              
                <option value="Excellent Answer" <?php if($answer['Vote_Text'] == 'Excellent Answer'){ echo 'selected';}?>>Excellent Answer</option>  
                <option value="Satisfactory Answer" selected>Satisfactory Answer</option>  
                <option value="Unsatisfactory Answer" <?php if($answer['Vote_Text'] == 'Unsatisfactory Answer'){ echo 'selected';}?>>Unsatisfactory Answer</option>   
                <option value="Alarming Answer" <?php if($answer['Vote_Text'] == 'Alarming Answer'){ echo 'selected';}?>>Alarming Answer</option>   
              </select></td>
              <td><textarea placeholder="Optional: Type Comment Here" name="comment" id = "contactMessage"  rows="3" cols="33"><?php echo $answer['Vote_Comment'];?></textarea></td>
              <td id = "Edge"><button class = 'button'>
              Commit
            </button></td>
            <?php if($admin == true) { ?>
              <td id = "Edge"><button class = "remove">
                Remove
              </button></td>
          <?php } ?>

        </tr>
      <?php } ?>
    </tbody>
  </div>
</table>
</div>

<div id = "submitAll">
  


  <button id = "commitAll">
    Commit All
  </button>
</div>
<script>
  $(document).ready(function(){
    $(".button").click(function(){
     var currentTR = $(this).closest('tr');
     
     var drop = currentTR.find("select");
     var text_response = currentTR.find("textarea");
     var  text_comment = currentTR.find("textarea").eq(1);
     var button =  $(this);
     var iname = currentTR.find("span.name");  
     var data_vote = drop.val();
     var data_comment = text_comment.val(); 
     var data_response = text_response.val();
     
     var data_dm = '<?php echo $userName ;?>'
     var data_question = '<?php echo $question['Q_ID'] ?>'
     var data_character = iname[0].outerText;

     $.ajax({
      type: "POST",
      url: 'dm_vote.php',
      data: {data_comment:data_comment,data_vote:data_vote,data_dm:data_dm,data_character:data_character,data_question:data_question,data_response:data_response},
      success: function(response)
      {
        button.html(response);
        if(response == 'Vote Comitted'){
          button.css("background-color", "green");
          button.css("color", "AliceBlue");                
        } 
        else{
          button.css("background-color", "#08aae1");
        }
      }

    });
     


     
     if(drop.val() == 'Alarming Answer'){
       currentTR.css("background-color", "red");
     }
     else{
       currentTR.css("background-color", "navy");
     }
     

     
   });
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
    $("#commitAll").click(function(){

      
      var table_count = $('tbody').children().length; 
      for (i = 0; i < table_count; i++) {
       var currentTR = $("tbody tr:eq(" + i + ") td");
       var drop = currentTR.find("select");
       var text_response = currentTR.find("textarea");
       var  text_comment = currentTR.find("textarea").eq(1);
       var button =  $(this);
       var iname = currentTR.find("span.name");  
       var data_vote = drop.val();
       var data_comment = text_comment.val(); 
       var data_response = text_response.val();
       
       var data_dm = '<?php echo $_SESSION['ID'];?>'
       var data_question = '<?php echo $question['Q_ID'] ?>'
       var data_character = iname.attr('id');
       $.ajax({
        type: "POST",
        url: 'dm_vote.php',
        data: {data_comment:data_comment,data_vote:data_vote,data_dm:data_dm,data_character:data_character,data_question:data_question,data_response:data_response},
        success: function(response)
        {
          button.html('Vote Committed');
          
        }

      });
       


       
       if(drop.val() == 'Alarming Answer'){
         currentTR.css("background-color", "#c44252");
       }
       else{
         currentTR.css("background-color", "#454292");
       }
       
     }
     
          /*      $.ajax({
            type: "POST",
            url: 'dm_vote.php',
            data: {data_remove:data_remove,data_character:data_character},
            success: function(response)
            {
          currentTR.remove();             
              } 



            });*/
            
            

            
          });  
  });
</script>
</body>
</html>