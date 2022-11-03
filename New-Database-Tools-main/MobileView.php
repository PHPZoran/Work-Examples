<?php
session_start();
if(isset($_POST['Log'])){
	unset($_SESSION["User"]);
	header('Location: login.php');
}
 if(isset($_SESSION["User"])){
	// echo "Welcome, " . $_SESSION["User"];
 }
 else 
 {
	 header('Location: login.php');
 }
// Connect to database. 
	require_once('../databaseconfig.php');
// Query for weekly numbers if button is pressed. 
if (isset($_POST['weekly_numbers'])){
$query = "SELECT activities.id as record_id, count(user_name) as mobile_update_count, 
CONCAT(first_name,' ',last_name) as sdr, user_name as ambition_id, activities.date_entered as 'date' FROM {$db_name}.activities 
JOIN {$db_name}.users on activities.created_by = users.id 
where parent_type = 'Contacts' and activity_type = 'update' and activities.date_entered > NOW() - INTERVAL 1 WEEK 
and data LIKE '%phone_mobile%' group by user_name order by mobile_update_count desc";
}
elseif(isset($_POST['since_friday'])){ // Else query for numbers since last Friday if button pressed. 
$query = "SELECT activities.id as record_id, count(user_name) as mobile_update_count, 
CONCAT(first_name,' ',last_name) as sdr, user_name as ambition_id, activities.date_entered as 'date' FROM {$db_name}.activities 
JOIN {$db_name}.users on activities.created_by = users.id 
where parent_type = 'Contacts' and activity_type = 'update' and activities.date_entered > NOW() - INTERVAL WEEKDAY(NOW()) + 3 DAY 
and data LIKE '%phone_mobile%' group by user_name order by mobile_update_count desc";	
}
else{ // Else give the default daily. 
$query = "SELECT activities.id as record_id, count(user_name) as mobile_update_count, 
CONCAT(first_name,' ',last_name) as sdr, user_name as ambition_id, activities.date_entered as 'date' FROM {$db_name}.activities 
JOIN {$db_name}.users on activities.created_by = users.id 
where parent_type = 'Contacts' and activity_type = 'update' and activities.date_entered > timestamp(current_date) 
and data LIKE '%phone_mobile%' group by user_name order by mobile_update_count desc";	
}
$results = $con->query($query);




?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Contact Duplicate List</title>
  
  <!--<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous"> -->
  <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.1.0/css/all.css">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>  
  <style>
  #dataTable
{
  border-bottom: 20px outset #cce7ff;
  background-color: AliceBlue;
  margin:auto;
  margin-bottom: 50px;
  width: 500px;
  border-collapse: seperate;
  border-spacing: 0px;
}
th{
	border-top: 10px inset  #cce7ff;
	border-bottom:10px inset #cce7ff;
	border-left: 10px inset #cce7ff;
	
	color: #1DA8DB;
	font-size: 20px;
}

table>thead {
  background-color: AliceBlue;
;
}
tr, td { padding: 0; }
td{
	
	border-bottom: 10px inset #cce7ff;
	border-left: 10px inset #cce7ff;
table>tbody {
  background-color: AliceBlue;
}
   font-size: 16px;
   text-align: center;
}


.content {
	max-width: 1200px;
	background-color:AliceBlue;
	border:outset 15px #b3dbff;
	  


}
#AlignLeft{
	
	text-align: left;
	text-indent: 15px;
}



h1 {
  color: #4FB5E6  ;
  margin-bottom:20x;
  font-size: 40px;
  text-align: center;
 
} 

#Edge {
	border-right: 10px solid #8898A8;
}
  #header {
	 display: block;
	  width: 800px;
	  margin-bottom: 25px;
	  float:left;
  }
    #logo {
	 display: block;
	margin-left: auto;
	margin-right: auto;
	  width: 800px;
	  margin-bottom: 25px;
  }
    #ClearAlert {
		text-align: center;
		font-size:20px;
		color: #1DA8DB  ;
	margin: auto;
	  width: 500px;
	  background-color: white;
	  border:outset 15px #b3dbff;
	  height: 150px;
  }
  body{
	  margin: 0;
	  padding: 0;
	  background-color:#42464F;
  }
  input[type=button], input[type=submit], input[type=reset] {
  background-color: #1DA8DB;
  color: AliceBlue;
 box-sizing: border-box;
  cursor: pointer;
}
.clear{
	clear:both;
}
.Menu {
	width: 165px;
  font-size: 20px;
line-height: 40px;
margin-left: -5px;
}
</style>
</head>
<body>
<div id = "header">
<form action="" method="post">
<input type="button" name="List" class = "Menu" onclick="window.location.href = 'index.php';" value="Main Menu">
<?php if (isset($_POST['weekly_numbers'])){ ?>
<input type="submit" name="since_friday" class = "Menu" value="# Since Friday">
<input type="submit" name="daily_numbers" class = "Menu" value="Daily Numbers">
<?php } elseif (isset($_POST['since_friday'])){ ?>
<input type="submit" name="weekly_numbers" class = "Menu" value="Weekly Numbers">
<input type="submit" name="daily_numbers" class = "Menu" value="Daily Numbers">

<?php } 
  else{ ?>
<input type="submit" name="since_friday" class = "Menu" value="# Since Friday">
<input type="submit" name="weekly_numbers" class = "Menu" value="Weekly Numbers">
<?php } ?>
  <input type="submit" class = "Menu" name="Log" value="Log Out"></form>
</div>
<div class = "clear"></div>
<div id = "logo">
<?php if (isset($_POST['weekly_numbers'])){ ?>
<h1>Mobile Weekly Update Count</h1>
<?php } elseif (isset($_POST['since_friday'])){ ?>
<h1>Mobile Update Count Since Friday</h1>
<?php } 
  else{ ?>
  <h1>Mobile Daily Update Count</h1>
  <?php } ?>
</div>
<div class = "clear"></div>

  <div class="container">
  <table id='dataTable'>
    <thead>
      <tr>


      <th> SDR </th> 
      <th id = "Edge">Mobile Update Count</th> 

        </tr>
    </thead>
    <tbody>
      <?php
      foreach($results as $result){
        ?>
        <tr>

		 <td ><?php echo $result['sdr']?></td>
        <td id = "Edge"><?php echo $result['mobile_update_count']?></td>


         <?php
}
      ?>

      </tr>
    </tbody>
    </div>
  </table>

</body>
</html>