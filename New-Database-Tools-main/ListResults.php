<?php

require_once('../databaseconfig.php');
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
 
 if(isset($_POST['clear'])){
	$input = $_POST['id'];
	$sql = "UPDATE {$db_name}.atc_hot_callbacks_contacts_1_c 
	JOIN prospect_lists_prospects ON related_id = atc_hot_callbacks_contacts_1contacts_idb 
	SET atc_hot_callbacks_contacts_1_c.deleted = 1 WHERE prospect_list_id = ?";
	$stmt = $con->prepare($sql);
	$stmt->bind_param('s', $input);
	$stmt->execute();
	$stmt->close();
	$cleared = 1;
 }
	 
 if(isset($_POST['submit'])){
	 $found = 0;
	 $input = strtolower($_POST['search']);
	 $sql = "SELECT id, name FROM {$db_name}.prospect_lists WHERE id = ? and deleted != 1";
	 $stmt = $con->prepare($sql);
	$stmt->bind_param('s', $input);
	$stmt->execute();
	$results = $stmt->get_result();
	foreach($results as $result)
	{
		$found++;
	}
	if ($found == 0)
	{
		$inputlike = "%" . $input . "%";
	$sql = "SELECT id, name FROM {$db_name}.prospect_lists WHERE name LIKE ? and deleted != 1";
	$stmt = $con->prepare($sql);
	$stmt->bind_param('s', $inputlike);
	$stmt->execute();
	$results = $stmt->get_result();
	
	foreach($results as $result)
	{
		$found++;
	}
	}
	
 }
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>List Results</title>
  <!--<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous"> -->
  <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.1.0/css/all.css">
  <style>
  #dataTable
{
  border-bottom: 20px outset #cce7ff;
  background-color: AliceBlue;
  margin:auto;
  margin-bottom: 50px;
  width: 1000px;
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
}
table>tbody {
  background-color: AliceBlue;
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
	  width: 500px;
	  margin-bottom: 25px;
	  float:left;
  }
    #logo {
	 display: block;
	margin-left: auto;
	margin-right: auto;
	  width: 500px;
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
#Menu {
	width: 150px;
  font-size: 20px;
line-height: 40px;
margin-left: -5px;
}
  </style>
</head>
<body>
<div id = "header">
<form action="" method="post">
<input type="button" name="List" id = "Menu" onclick="window.location.href = 'ListSearch.php';" value="New Search">
<input type="button" name="List" id = "Menu" onclick="window.location.href = 'index.php';" value="Main Menu">
  <input type="submit" id = "Menu" name="Log" value="Log Out"></form>
</div>
<div class = "clear"></div>
<div id = "logo">
<img src = "Logo.png" width="500" height="100%">
</div>
<div class = "clear"></div>
  <div class="container">
	        <?php
      if($found > 0){
		  
        ?>
  <table id='dataTable'>
    <thead>
      <tr>
      <th> ID </th>
      <th> Name </th> 
      <th id = "Edge"> Clear All Hot Callbacks </th> 
        </tr>
    </thead>

    <tbody>
	        <?php
      foreach($results as $result){
		  
        ?>
        <tr>
		<form action="" method="post">
        <td id = "AlignLeft"><input type="radio" name="id" checked value = <?php echo $result['id'];?> ><?php echo $result['id'];?></td>
        <td><?php echo $result['name'];?></td>
		 <td id = "Edge"><input type="submit" name="clear" value="Clear" ></td>
		 
          <?php
	  }}
      ?>
 </form>
      </tr>
    </tbody>
    </div>
  </table>
</div>
          <?php
     if ($cleared == 1){
		  
        ?>
		<div id = "ClearAlert">
		<p>Any Hot Callback Flame Tags have been cleared.</p>
				<form action="" method="post">
		<input type="button" name="List" id = "Menu" onclick="window.location.href = 'ListSearch.php';" value="New Search">
<input type="button" name="List" id = "Menu" onclick="window.location.href = 'index.php';" value="Main Menu">

  <input type="submit" id = "Menu" name="Log" value="Log Out">
</form>
		</div>
		          <?php
}
  if ($cleared == 0 && $found == 0){
      ?>
  		<div id = "ClearAlert">
		<p>No results have been found.</p>
				<form action="" method="post">
		<input type="button" name="List" id = "Menu" onclick="window.location.href = 'ListSearch.php';" value="New Search">
<input type="button" name="List" id = "Menu" onclick="window.location.href = 'index.php';" value="Main Menu">

  <input type="submit" id = "Menu" name="Log" value="Log Out">
</form>
		</div>
		          <?php
}
      ?>
</body>
</html>