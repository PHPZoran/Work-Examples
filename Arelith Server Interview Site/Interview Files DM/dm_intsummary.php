<?php 
require 'login.php';

require_once("data-config.php");

if(isset($_POST['conclude'])){
 
//Set interview to conclude.
$query = "UPDATE int_status set status = 'closed' where id = 1";
$con->query($query);
//Get all applicants and their yes/no vote sum.
$query = "Select Related_A_ID, SUM(CASE WHEN DM_Vote = 'Yes' THEN 1 ELSE 0 END) as 'yes', COUNT(DM_Vote) as 'total' from int_votes group by  related_a_id";
$applicants = $con->query($query);
foreach($applicants as $a){
  if($a['yes']/$a['total'] > .60){
    $update = "UPDATE int_applicant set interview_status = 'accepted' where username = ?";
    $stmt = $con->prepare($update);
  	$stmt->bind_param('s', $a['Related_A_ID']);	
	$stmt->execute();
  }
  else{
    $update = "UPDATE int_applicant set interview_status = 'denied' where username = ?";
    $stmt = $con->prepare($update);
  	$stmt->bind_param('s', $a['Related_A_ID']);	
	$stmt->execute();
  }
}

}
//Get status
$query = "SELECT status from int_status where id = 1";
$stmt = $con->prepare($query);
$stmt->execute();
$status = $stmt->get_result();
$s = mysqli_fetch_assoc($status);
if($s['status'] != 'closed'){
  $int_status = 'completed';
}
else{
  $int_status = 'accepted';
}
//Get Applicants.
$a_query = "SELECT A_ID, username, pseudonym FROM int_applicant where deleted = 0 and interview_status = ? order by A_ID asc";
	$stmt = $con->prepare($a_query);
  $stmt->bind_param('s', $int_status);	
	$stmt->execute();
	$result = $stmt->get_result();
$p_query = "SELECT SUM(Vote_Int) as sum
FROM int_qadm where r_a_id = ?";
$v_query = "Select SUM(CASE WHEN DM_Vote = 'Yes' THEN 1 ELSE 0 END) as 'yes', SUM(CASE WHEN DM_Vote = 'No' THEN 1 ELSE 0 END) as 'no' from int_votes where Related_A_ID = ?";
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Summary</title>
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
    <h2>
      Summary
    </h2>
    <?php if($s['status'] != 'closed'){ ?>
    <h3>Status: Open.</h3> 
    <br>
    <p style="width: 450px;text-align:center;margin:0 auto;">When Everyone has voted, the head DM or admin will conclude the interview. Players will receive a notification if they were accepted or not, with the cut-off being 60% of their votes being yes.</p>
    <br>
    <form method="get" action="dm_intcandidate.php" style="display:inline-block">
    <input type="submit" value="Go to Candidate:" name="goCandidate">
  <select name="candidate_number" class = "candidate">
    <?php foreach($result as $candidate){?>
  <option class = "candidate" value="<?php echo $candidate['A_ID'];?>"><?php echo $candidate['pseudonym']?></option>  
    </form>
    <?php } ?>
    
          </select>
  </form>   
  <?php } else{ ?>
    <h3>Status: Closed.</h3> 
    <br>
    <p style="width: 450px;text-align:center;margin:0 auto;">The interview has concluded. These are the accepted DMs.</p>
    <br> <?php } ?>
  </div>
  <div id="tableContainer">
 <table id='dataTable'>
    <thead>
      <tr>
      <th> Character </th>     
        <th> Total Points </th>  
       <th>Yes Votes</th> 
        <th>No Votes</th>
      </thead>
      <tbody>
        <?php foreach ($result as $r){ 
        	$stmt = $con->prepare($v_query);
  	$stmt->bind_param('s', $r['username']);	
	$stmt->execute();
	$votes = $stmt->get_result();
  $vote =  mysqli_fetch_array($votes);
 
        ?>
   <tr class="">
        <td ><?php echo $r['pseudonym'];?></td> 
     <?php $stmt = $con->prepare($p_query);
	$stmt->bind_param('s', $r['username']);	
	$stmt->execute();
	$grab_total_sum = $stmt->get_result();
   $sum_total = mysqli_fetch_array($grab_total_sum);?> 
        <td><?php echo $sum_total['sum'];?></td>
        <td><?php echo $vote['yes']; ?></td>
		    <td><?php echo $vote['no']; ?></td>
        </tr>
        <?php } ?>
   </tbody>
    </table>
   </div>
   <?php if($s['status'] != 'closed'){ ?>
     <div id = "submitAll">
     <form method = "post" onsubmit="return confirm('Once you click conclude, all votes will be confirmed and the interview will be closed.');">   
   <input type = "submit" value = "Conclude Interview" name = "conclude"></form>
   </div>
  <?php } ?>
</body>
</html>