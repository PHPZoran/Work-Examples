<?php 
require 'login.php';

require_once("data-config.php");

// Check if admin is initialised in local DB, if not, insert it into table.
$adminExistsSql = mysqli_prepare($con, "SELECT * FROM `int_admins` WHERE `username` = ?");
mysqli_stmt_bind_param($adminExistsSql, "s", $userName);
mysqli_stmt_execute($adminExistsSql);

$adminResult = mysqli_stmt_get_result($adminExistsSql);

if(!mysqli_num_rows($adminResult)){
  $newAdminSql = mysqli_prepare($con, "INSERT INTO int_admins (username) VALUES (?)");
  mysqli_stmt_bind_param($newAdminSql, "s", $userName);
  mysqli_stmt_execute($newAdminSql); 
}

$allAdminsSQL = mysqli_prepare($con, "SELECT id, username FROM int_admins");
mysqli_stmt_execute($allAdminsSQL);

$result = mysqli_stmt_get_result($allAdminsSQL);

$allAdmins = array();

while($row = mysqli_fetch_array($result))
  array_push($allAdmins, $row);

mysqli_stmt_close($allAdminsSQL);
//Get Admin ID function
function get_admin_id($con,$userName){
  $getAdminSQL = mysqli_prepare($con, "SELECT id FROM int_admins where username = ?");
  mysqli_stmt_bind_param($getAdminSQL, "s", $userName);
  mysqli_stmt_execute($getAdminSQL); 
  $adminResult = mysqli_stmt_get_result($getAdminSQL);
  $admin = mysqli_fetch_assoc($adminResult);    
  return $admin['id'];
}

if(isset($_GET['action'])){
  //Get admin id
 $admin_id = get_admin_id($con,$userName);
  $au_id = $admin_id . '-' . $_GET['key'];

  $vote = intval($_GET['action']);
  $vote_query = mysqli_prepare($con,"INSERT INTO int_adminvotes
    (au_id, admin_id, user_id, vSUM(CASE WHEN vote = 1 THEN 1 ELSE 0 END)UES yes_vote (SUM(CASE WHEN vote = 0 THEN 1 ELSE 0 END) as no_vote, ?, ?,?,?)
  ON DUPLICATE KEY UPDATE
    vote = ?");
   mysqli_stmt_bind_param($vote_query, "sssss", $au_id, $admin_id,$_GET['key'],$vote,$vote);
   mysqli_stmt_execute($vote_query); 
}

   //Save Commenyes_vote sa<e$app['no_vote']pressed.
if(isset($_POST['save'])){ 
  //Get admin id
  $admin_id = get_admin_id($con,$userName); 
  $au_id = $admin_id . '-' . $_POST['a_id'];
  $comment_query = mysqli_prepare($con,"INSERT INTO int_adminvotes
    (au_id, admin_id, user_id, comments) 
  VALUES 
    (?, ?,?,?)
  ON DUPLICATE KEY UPDATE
    comments = ?");
   mysqli_stmt_bind_param($comment_query, "sssss", $au_id, $admin_id,$_POST['a_id'],$_POST['comment'],$_POST['comment']);
   mysqli_stmt_execute($comment_query); 
}
if(isset($_POST['conclude'])){
 $query = "SELECT SUM(CASE WHEN vote = 1 THEN 1 ELSE 0 END) as yes_vote, SUM(CASE WHEN vote = 0 THEN 1 ELSE 0 END) as no_vote, a_id FROM `int_adminvotes` JOIN int_applicant on user_id = a_id where int_applicant.deleted = 0 group by a_id";
 $applicants = mysqli_query($con, $query);
 foreach($applicants as $app){
   
   if($app['yes_vote'] < $app['no_vote']){
    $query = "UPDATE int_applicant set interview_status = 'denied' where a_id = '{$app['a_id']}' and int_applicant.deleted = 0";
    mysqli_query($con, $query);
   }
   else{
    $query = "UPDATE int_applicant set interview_status = 'first_cut' where a_id = '{$app['a_id']}' and int_applicant.deleted = 0";
    mysqli_query($con, $query);
   }
 }
 $query = "UPDATE int_status set status = 'round1.5' where id = 1";
 mysqli_query($con, $query);
}
if(isset($_POST['restart'])){
   $query = "UPDATE int_status set status = 'round1' where id = 1";
  mysqli_query($con, $query);
 } 
 //Get interview state
$query = "SELECT status  as state, first_round_end_date from int_status where id = 1";
$state_results = $con->query($query);
$state_result = mysqli_fetch_assoc($state_results);
$sql = "SELECT * FROM int_applicant order by Username asc";
$results = mysqli_query($con, $sql);

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>DM Applications</title>
  
   <link href="https://fonts.googleapis.com/css?family=Cinzel+Decorative|Lato&display=swap" rel="stylesheet">
   <script src="https://kit.fontawesome.com/28f90f102d.js" crossorigin="anonymous"></script>

  <style>
    body{
      background-color:grey;
    }
 .yes{
   color:#66FF00;
 }
 .no{
   color:red;
 }
.upload{
	cursor:pointer;
	width:350px;
	 background-color:rgba(175,236,246,0.8);
	  border: 2px solid #603D22;
  border-radius: 3px;
  margin-top:10px;
  padding: 5px 8px;
}
.upload:hover {
  background-color:rgba(240,248,255,0.4)
}
#mainHead{
	width:350px;
	background: -webkit-linear-gradient(left, rgba(240,248,255,1),rgba(240,248,255,0.8),rgba(8,170,255,0.2));
margin:0 auto;
padding:10px;
}
#mainHead h1,h2{
	text-align:center;
}
#import{
	width:250px;
	background: -webkit-linear-gradient(left, AliceBlue,Skyblue);
	  border: 2px solid #603D22;
  border-radius: 3px;
  padding: 5px 8px;
}
table, th, td {
  border: 1px solid black;
}

#map_key{
	float:right;
	width:500px;
	background-color:Aliceblue;
}
#mapping{
	float:left;
	margin-left:25%;
	width:800px;
	background-color:AliceBlue;
}
#dataTable
{
  border-bottom: 20px outset #afecf6;
  background-color: AliceBlue;
  margin-left: 40px;
  margin-right: 50px;

  margin-bottom: 50px;
  width: 2500px;
  border-collapse: seperate;
  border-spacing: 0px;
}
th{
	border-top: 10px inset  #afecf6;
	border-bottom:10px inset #afecf6;
	border-left: 10px inset #afecf6;
	
	color: #1DA8DB;
	font-size: 20px;
}

table>thead {
  background-color: AliceBlue;
;
}
tr, td { padding: 0; max-width: 400px; }
td{
	
	border-bottom: 10px inset #afecf6;
	border-left: 10px inset #afecf6;
}
table>tbody {
  background-color: AliceBlue;
   font-size: 16px;
   text-align: center;
}
#AlignLeft{
	
	text-align: left;
	text-indent: 15px;
}
#Edge {
	border-right: 10px solid #08aae1;
}
#finished{
	color:#dbfe01
}
  </style>
</head>
<body>
<?php
  include "userinfo.php";
?>
  <div id = "mainHead">
    

 <h1 >
  DM Applications </h1> 
 <?php if($state_result['state'] == 'round1'){?>
<form method = "post"  onsubmit="return confirm('Are you sure you want to conclude Round 1? Make sure every admin has voted!');">
<p style="font-size:16px">Use the pencil icon under Actions to submit comments, and the thumb icons to vote. 
When Complete Voting is pressed, round 1 will end, and then Applicants with at least three yes votes will go to round 2. 
Only press after everyone has voted.</p>
<p>You have informed players round 1 will conclude on <?php echo $state_result['first_round_end_date']; ?></p>
<input type = "submit" class = "upload" id = "conclude" name = "conclude" value = "Complete Voting">
</form>
<?php } else { ?>
<form method = "post" onsubmit="return confirm('Are you sure you want to reopen Round 1?');">
<p style="font-size:16px">Round 1 is concluded. If you wish to re-open, click Reopen Round 1. This will allow applicants to be re-evaluated unless they were deleted.</p>
<input type = "submit" class = "upload" id = "restart" name = "restart" value = "Restart Round 1">
</form>
<?php } ?>
<?php if (isset($_POST['save'])){ ?> <h2 class = "yes">Comment Saved</h2> <?php } ?>
<?php if(!empty($_GET['comment'])){
  $app_id = intval($_GET['key']);
  $applicant_query = mysqli_prepare($con, "SELECT username FROM int_applicant where a_id = ?");
  mysqli_stmt_bind_param($applicant_query, "s", $app_id);
  mysqli_stmt_execute($applicant_query); 
  $applicantResult = mysqli_stmt_get_result($applicant_query);
  $applicant = mysqli_fetch_assoc($applicantResult);  ?>

<h2>Comment Form</h2>
<h3>Applicant: <?php echo $applicant['username']; ?></h3>
<form method = "post" action = "dm_applications.php">
<input type="hidden" id="custId" name="a_id" value=<?php echo $app_id ?>>
<textarea rows="7" cols="37" id = "comment" name = "comment"></textarea>
<input type = "submit" class = "upload" id = "save" name = "save" value = "Save Comment">
</form>
<?php } ?>
   </div>  
 

    <div id = "container">
       <br>
     
        <?php
          // include 'getadmins.php';
        ?>
      <table id="dataTable">
    <thead>
      <tr>
      <th>Username</th>
	   <th> CD Key</th>
        <th>DM Reason</th>
		<th> Known PCs </th>
	 
  
    <?php
    
      foreach($allAdmins as $user){
    
        echo "<th> " . $user['username'] . " Comments </th>";
      }
    ?>
     <th> Votes </th>
	
	  <th id = "Edge"> Actions</th>
        </tr>
    </thead>
    <tbody>
      <?php
    
      foreach($results as $result){
        ?>
        <tr>
        <td id = "AlignLeft"><?php echo $result['username']?></td>
        <td><?php echo $result['cdkey']?></td>		

          <td><?php echo $result['dm_reason']?></td>

        <td><a target="_blank" href="https://dm.nwnarelith.com/case-tool.php?key=<?=$result['cdkey']?>">Link</a></td>
          <?php if($result['Cases Against'] != 'None' && $result['Cases Against'] != ''){ ?>
        <td><a href="<?php echo $result['Cases Against']?>"><i class="fa fa-eye" aria-hidden="true"></i></a></td>
          <?php } else{?>
          
          <?php } ?>
          <?php
          foreach($allAdmins as $user){     
            //Get Comments     
            $query = mysqli_prepare($con, "SELECT comments FROM int_adminvotes JOIN int_admins on id = admin_id where username = ? and user_id = ?");
            mysqli_stmt_bind_param($query, "ss", $user['username'],$result['a_id']);
            mysqli_stmt_execute($query);            
            $comments = mysqli_stmt_get_result($query);         
            $comment = mysqli_fetch_assoc($comments);
            echo '<td>' . $comment['comments'] . '</td>'; 
          } 
          //Sum Votes, display in table.
          $sum = mysqli_prepare($con, "SELECT SUM(CASE WHEN vote = 1 THEN 1 ELSE 0 END) as yes, 
          SUM(CASE WHEN vote = 0 THEN 1 ELSE 0 END) as no FROM int_adminvotes where user_id = ?");
          mysqli_stmt_bind_param($sum, "s",$result['a_id']);
          mysqli_stmt_execute($sum);            
          $votes = mysqli_stmt_get_result($sum);         
          $vote = mysqli_fetch_assoc($votes);     
          echo "<td><span class = 'yes'>"  . $vote['yes'] . "</span>-<span class = 'no'>" . $vote['no'] .  "</span></td>";      
          ?>
     
		<td id = "Edge"><a href="dm_applications.php?comment=yes&key=<?php echo $result['a_id']?>">Edit<i class="fas fa-edit"></i></a>
    <a href="dm_applications.php?action=1&key=<?php echo $result['a_id']?>">Yes<i class="fa fa-thumbs-o-up"></i></a>
    <a href="dm_applications.php?action=0&key=<?php echo $result['a_id']?>">No<i class="fa fa-thumbs-o-down"></i></a>
    </td>
                </tr>
      <?php } ?>
    </tbody>
  </table>	


    </div>
</body>
</html>