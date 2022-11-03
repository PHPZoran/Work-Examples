<?php
require 'login.php';

// session_start();

//  if(!isset($_SESSION["DM"])){
// 	 header('Location: dm_login.php');
//  }
require_once("data-config.php");

// if($_SESSION['DM'] == 'Queen Titania' || $_SESSION['DM'] == 'Irongron' || $_SESSION['DM'] == 'Grumpycat' || $_SESSION['DM'] == 'Spyre'){
//   $admin = true;

// }
$a_query = "SELECT A_ID, Applicant FROM int_a where deleted = 0 order by A_ID asc";
	$stmt = $con->prepare($a_query);
	$stmt->execute();
	$result = $stmt->get_result();

$v_query = "SELECT SUM(CASE WHEN dm_vote = 'Yes' THEN 1 ELSE 0 END) as yes, SUM(CASE WHEN dm_vote = 'No' THEN 1 ELSE 0 END) as no, SUM(CASE WHEN dm_vote = 'No' or dm_vote = 'Yes' THEN 1 ELSE 0 END) as total FROM int_v where related_a_id = ?"
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Conclusion</title>
<link rel="stylesheet" type="text/css" href="css/interview.css">
 <link href="https://fonts.googleapis.com/css?family=Cinzel+Decorative|Lato&display=swap" rel="stylesheet">  
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
      Conclusion
    </h2>
    <br>
    <button onclick="window.location.href = 'dm_login.php?logout=true';">
      Logout 
    </button>
        <button onclick="window.location.href = 'dm_intsummary.php';">
     Back to Summary
    </button>
  </div>
  <div id="tableContainer">
 <table id='dataTable'>
    <thead>
      <tr>
      <th> Character </th>     
        <th>Yes Votes</th>
        <th>No Votes</th> 
        <th>Total Votes</th> 
      </tr>
   </thead>
      <tbody>
        <?php foreach ($result as $r){ 
        	$stmt = $con->prepare($v_query);
  	$stmt->bind_param('s', $r['A_ID']);	
	$stmt->execute();
	$tallies = $stmt->get_result();
  $t =  mysqli_fetch_array($tallies);
        ?>
   <tr class="<?php if($t['yes'] > $t['no']){echo "yes";} else{echo 'no';} ?>">
        <td ><span class="name" id = "<?php echo $r['A_ID'];?>"><?php echo $r['Applicant'];?></span></td> 
     <td><?php echo $t['yes']; ?></td>
      <td><?php echo $t['no']; ?></td>
      <td><?php echo $t['total']; ?></td>
        </tr> <?php } ?>
</body>
  
</html>