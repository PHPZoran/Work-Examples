<?php
require 'login.php';
//Get database connection
require_once("data-config.php");
//Get session variables
 session_start();

//Save response function
function save_response($input,$q_id,$con){
 $id = $_SESSION['question'] . '-' . $_SESSION['username'];
$query = "INSERT INTO int_response
  (id, username, question_id, response) 
VALUES 
  (?, ?,?,?)
ON DUPLICATE KEY UPDATE
  response = ?";
 // $result = $con->query($query);
 $stmt = $con->prepare($query);
 $stmt->bind_param('sssss',$id,$_SESSION['username'],$q_id,$input,$input);
$stmt->execute();
  return;  
}

if (isset($_POST['next']) && isset($_SERVER['REQUEST_URI']))
{
//Get time.
$now = time();
//Calculate how many seconds have passed since the countdown.
$timeSince = $now - $_SESSION['time_started'];
//Remaining Seconds

$remainingSeconds = ($_SESSION['countdown'] - $timeSince);
//Only save if greater than zero.
  if($remainingSeconds > 0){
    save_response($_POST['textbox'],$_POST['question_id'],$con);
  }
    //Increment Session, use testing variables for no
     $_SESSION['countdown'] = 180;
     $_SESSION['time_started'] = time();
     $_SESSION['question']++;
    //Destroy Form data to prevent user refresh idiodicy 
    header ('Location: ' . $_SERVER['REQUEST_URI']);
    exit();
}
if(isset($_POST['dInput'])){
save_response($_POST['dInput'],$_POST['question_id'],$con);
return;
}





//Check to see if our countdown session
//variable has been initialized.
if(!isset($_SESSION['countdown'])){
    //Set the countdown to 180 seconds.
    $_SESSION['countdown'] = 180;
    //Store the timestamp of when the countdown began.
  $_SESSION['time_started'] = time();
  
}
if(!isset($_SESSION['question'])){
  $_SESSION['question'] = 0;
}

$now = time();

//Calculate how many seconds have passed since
//the countdown began.
$timeSince = $now - $_SESSION['time_started'];

//Remaining Seconds...this will be pased to our javascript below.
$remainingSeconds = ($_SESSION['countdown'] - $timeSince);
$status = "incomplete";
$query = "SELECT * FROM int_question where sequence IS NOT NULL and deleted = 0 order by sequence";
$results = $con->query($query);
if(mysqli_num_rows($results) > $_SESSION['question']){
$question = [];
$count = mysqli_num_rows($results);
foreach($results as $result){
  $question[] = $result;
}
}
else{
  $query = "UPDATE int_applicant set interview_status = 'completed' where username = ?";
  $stmt = $con->prepare($query);
  $stmt->bind_param('s',$_SESSION['username']);
 $stmt->execute();
    header ('Location: index.php');
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
  <br>
    <div id = "timer">
    
       Remaining Time: <span id = "time-left"></span>
    </div>

   <h1>
Question #<span id = "q#"> <?php echo  $question[$_SESSION['question']]['sequence']; ?></span>    
  </h1> 

<!-- <img src="img/tinkerbell.jpg" alt="DM Queen Titania"> 
    <img src="img/grumpycat.jpg" alt="Empress Grumpycat">  -->
    <h1 id = "qtext">
     <?php //echo  $question[$_SESSION['question']]['question']; 
     echo $question[$_SESSION['question']]['question'];?>
    </h1>
    <br />
      <form method = "post" action = "questions.php" >
        
  
      <textarea rows="7" cols="74" id = "textbox" name = "textbox"></textarea>
    <br />
    <input type = "hidden" id = "question_id" value = "<?php echo $question[$_SESSION['question']]['q_id']; ?>" name = "question_id"> 
    <input type = "submit" id = "next" value = "next" name = "next"> 
      <br>
     </form>
      </div>


</div>
  <!-- <footer><img src="img/Arelith.jpg" alt="Arelith"> </footer> -->
<input type="hidden" id="session_something" style="display:none" value="desired_value">
  
<script>
$('#textbox').keydown(function() {
    var dInput = this.value;
    var question_id = currentTR.find("input[name='question_id']").val();
    $.ajax({
      type: 'POST',
      url:  'questions.php',
      data: {dInput: dInput,question_id:question_id},
      success: function success(data){
      
      },
    })
});
  
// Set the date we're counting down to
var countDownDate = '<?php echo $remainingSeconds;?>';

// Update the count down every 1 second
var x = setInterval(function() {
  var secondsLeft = countDownDate--;
 
    
  // Output the result in an element with id="demo"
  document.getElementById("time-left").innerHTML = secondsLeft;
    
  // If the count down is over, for this page, let them know. For any other situation, set up the next question.
  if (secondsLeft < 0) {
    clearInterval(x);
    document.getElementById("textbox").disabled="true";
    document.getElementById("time-left").innerHTML = "You have no more time for this question. Please hit next.";
  }
}, 1000);
</script>

    


</body>
</html>