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

	require_once('../databaseconfig.php');

$query = "SELECT name, phone_office, billing_address_state, industry, date_entered, COUNT(*) as count FROM {$db_name}.accounts JOIN accounts_cstm ON id = id_c WHERE phone_office IS NOT NULL and phone_office != '' and deleted != 1 and ignore_duplicate_c = 0 GROUP BY phone_office, billing_address_state
HAVING COUNT(*) > 1 ORDER BY name LIMIT 1000";

$results = $con->query($query);




?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Duplicates</title>
  <!--<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous"> -->
  <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.1.0/css/all.css">
  <style>
* { margin:0; padding:0; }


#dataTable
{
  border-bottom: 20px outset #cce7ff;
  background-color: AliceBlue;
  margin-left: 40px;
  margin-right: 50px;

  margin-bottom: 50px;
  width: 1500px;
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
#Menu {
	max-width: 300px;
  background-color: #1DA8DB;
  color: AliceBlue;
  font-size: 20px;
line-height: 40px;
 box-sizing: border-box;
  cursor: pointer;
}

#Log {
	max-width: 300px;
  background-color: #1DA8DB;
  color: AliceBlue;
  font-size: 20px;
  text-align:center;
padding-left: 20px;
padding-right: 20px;
padding-top: 9px;
padding-bottom: 8px;
  margin-left: -3px;

 box-sizing: border-box;
  cursor: pointer;
}




#red{
	color:red;
	text-align:center;
  font-family: "Times New Roman", Times, serif;

}

body{
	background-color:	#42464F
}
</style>
</head>
<body>
<form action="" method="post">
<input type="button" name="List" id = "Menu" onclick="window.location.href = 'index.php';" value="Main Menu">
  <input type="submit" id = "Log" name="Log" value="Log Out">  <h1>Account Duplicate List</h1>
  </form>

  <div class="container">
  <table id='dataTable'>
    <thead>
      <tr>
      <th> Name </th>
      <th> Phone </th> 
      <th> State </th> 
      <th> Date Entered </th> 
      <th>  Industry </th> 
      <th>  Count </th> 
	  <th id = "Edge"> View/Merge</th>
        </tr>
    </thead>
    <tbody>
      <?php
      foreach($results as $result){
        ?>
        <tr>
        <td id = "AlignLeft"><?php echo $result['name']?></td>
        <td><?php echo $result['phone_office']?></td>
        <td><?php echo $result['billing_address_state']?></td>
        <td><?php echo $result['date_entered']?></td>
        <td id = "AlignLeft"><?php echo $result['industry']?></td>
        <td><?php echo $result['count']?></td>
		 <td id = "Edge"><a href="/database/databaseview.php?phone=<?php echo $result['phone_office']?>&state=<?php echo $result['billing_address_state']?>"><i class="fas fa-edit"></i></a></td>
          <?php
}
      ?>

      </tr>
    </tbody>
    </div>
  </table>

</body>
</html>