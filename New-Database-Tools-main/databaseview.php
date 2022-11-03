<?php 



// We will only pull accounts that have a defined phone and state. (There are about 433 accounts without a state, these would have to be manipulated manually).  
session_start();
 if(isset($_SESSION["User"])){
	// echo "Welcome, " . $_SESSION["User"];
 }
 else 
 {
	 header('Location: login.php');
 }
	  $phone = ($_GET['phone']);
	  $state = ($_GET['state']);
	  if(empty($phone) || empty($state))
	  {
		  // Return to previous location. 
		  header('Location: database.php');
		  die;
	  }
	  else {

// Connect to Database, change this to just require a config file so we don't have to type this over and over again and can keep changse in one place. 
require_once('../databaseconfig.php');
// Query to obtain all the duplicate accounts. 
$query = "SELECT id, name, phone_office, website, date_modified, billing_address_street, billing_address_state, billing_address_postalcode, billing_address_country, industry, annual_revenue, billing_address_city, employees, ownership, ct_storage_c, ct_security_c, ct_networking_c, ct_hardware_c FROM {$db_name}.accounts JOIN accounts_cstm ON id_c = id WHERE phone_office = '$phone' AND billing_address_state = '$state' and accounts.deleted != 1 and ignore_duplicate_c = 0";

$results = $con->query($query);





if(isset($_POST['SelectRow'])){
	$check = $_POST['id'];
}

	  }
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Duplicate Details</title>
  <style>
* { margin:0; padding:0; }


.table
{
  border-bottom: 20px outset #cce7ff;
  background-color: AliceBlue;
  margin-left: 40px;
  margin-right: 50px;

  margin-bottom: 50px;
  height:250px;
  width: 2500px;
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
padding-left: 10px;
padding-right: 10px;
 box-sizing: border-box;
  cursor: pointer;
}
#Back {
	max-width: 300px;
  background-color: #1DA8DB;
  color: AliceBlue;
  font-size: 20px;
line-height: 40px;
padding-left: 30px;
padding-right: 30px;
 box-sizing: border-box;
  cursor: pointer;
  margin-left: -4.5px;
}
#Merge {
	max-width: 3000px;
  background-color: #1DA8DB;
  color: AliceBlue;
  font-size: 20px;
  text-align:center;
padding-left: 20px;
padding-right: 20px;
padding-top: 9px;
padding-bottom: 8px;
margin-left: -5px;
position:relative; bottom:113px;
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
  margin-left: -5px;

 box-sizing: border-box;
  cursor: pointer;
}


p 
{
	color: #1DA8DB;
	text-align: center; 
	font-size: 20px;
}

#red{
	color:red;
	text-align:center;
  font-family: "Times New Roman", Times, serif;

}

#ignore{
		max-width: 300px;
  background-color: #1DA8DB;
  color: AliceBlue;
  font-size: 20px;
  text-align:center;
padding-left: 20px;
padding-right: 20px;
padding-top: 9px;
padding-bottom: 8px;
  margin-left: 343px;
position:relative; bottom:113px;
 box-sizing: border-box;
  cursor: pointer;
}

body{
	background-color:	#42464F
}
</style>
</head>
<body>
<form action="" method="post">
<input type="button" name="List" id = "Menu" onclick="window.location.href = 'index.php';" value="Main Menu">
<input type="button" name="List" id = "Back" onclick="window.location.href = 'database.php';" value="Back">
<input type="submit" id = "Log" name="Log" value="Log Out">  

  <h1>Duplicate Details</h1>
  </form> 
  <p>Select the desired options for each category, then press Merge, or Ignore on the selected ID to clear it from the list.</p>
  	<form action="merge.php?phone=<?php echo $phone.'&state='.$state;?>" method="post"> 
	
	<input type="submit" name="ignore" id="ignore" value="Ignore" onclick="return confirm('Are you sure you wish to clear this duplicate?');">
  <input type="submit" name="submit" id="Merge" value="Merge" onclick="return confirm('Are you sure you wish to merge?');">
  <input type="submit" name="SelectRow" id="Merge" value="Select Row" formaction="databaseview.php?phone=<?php echo $phone?>&state=<?php echo $state?>">
  <div class="container">
  <table class='table'>
    <thead>
      <tr>
	  <th>  Source ID </th>
	    <th>  Account Name </th> 
	   <th> Office Phone </th> 
	    <th> Website </th> 
		<th> Last Update </th>
		 <th> Street </th> 
		  <th> City </th> 
		   <th> State </th> 
		    <th> Zip </th> 
      <th> Country </th>
	  <th> Industry </th>
      <th> Employees </th> 
	  <th>  Revenue</th> 
	  <th>  Ownership</th> 
	   <th>  Storage </th> 
	   <th>  Security </th> 
	   <th>  Networking </th> 
	   <th>  Hardware/OS </th> 


        </tr>
    </thead>
    <tbody>

      <?php
	  
	  // Set up the fields for selection. A function didn't work for displaying "checked", so keep it in its style below.
      foreach($results as $result){
        ?>

        <tr>
		<td><input type="radio" name="id" <?php if(isset($_POST['SelectRow'])){if ($result['id'] == $check) { echo "checked";}} else { echo "checked";}?> value="<?php echo $result['id']?>"><?php echo $result['id']?></td>
        <td><input type="radio" name="name"<?php if(isset($_POST['SelectRow'])){if ($result['id'] == $check) { echo "checked";}} else { echo "checked";}?> value="<?php echo $result['name']?>"><?php echo $result['name']?></td>
		<td><?php echo $result['phone_office']?></td>
        <td><input type="radio" name="website" <?php if(isset($_POST['SelectRow'])){if ($result['id'] == $check) { echo "checked";}} else { echo "checked";}?> value="<?php echo $result['website']?>"><?php echo $result['website']?></td>
		<td><?php echo $result['date_modified']?></td>
        <td><input type="radio" name="street"<?php if(isset($_POST['SelectRow'])){if ($result['id'] == $check) { echo "checked";}} else { echo "checked";}?> value="<?php echo $result['billing_address_street']?>"><?php echo $result['billing_address_street']?></td>
        <td><input type="radio" name="city" <?php if(isset($_POST['SelectRow'])){if ($result['id'] == $check) { echo "checked";}} else { echo "checked";}?> value="<?php echo $result['billing_address_city']?>"><?php echo $result['billing_address_city']?></td>

		<td><?php echo $result['billing_address_state']?></td>
        <td><input type="radio" name="zip" <?php if(isset($_POST['SelectRow'])){if ($result['id'] == $check) { echo "checked";}} else { echo "checked";}?> value="<?php echo $result['billing_address_postalcode']?>"><?php echo $result['billing_address_postalcode']?></td>
		<td><input type="radio" name="country" <?php if(isset($_POST['SelectRow'])){if ($result['id'] == $check) { echo "checked";}} else { echo "checked";}?> value="<?php echo $result['billing_address_country']?>"><?php echo $result['billing_address_country']?></td>
		<td><input type="radio" name="industry" <?php if(isset($_POST['SelectRow'])){if ($result['id'] == $check) { echo "checked";}} else { echo "checked";}?> value="<?php echo $result['industry']?>"><?php echo $result['industry']?></td>
		<td><input type="radio" name="employees" <?php if(isset($_POST['SelectRow'])){if ($result['id'] == $check) { echo "checked";}} else { echo "checked";}?> value="<?php echo $result['employees']?>"><?php echo $result['employees']?></td>
		<td><input type="radio" name="revenue" <?php if(isset($_POST['SelectRow'])){if ($result['id'] == $check) { echo "checked";}} else { echo "checked";}?> value="<?php echo $result['annual_revenue']?>"><?php echo $result['annual_revenue']?></td>	
		<td><input type="radio" name="ownership" <?php if(isset($_POST['SelectRow'])){if ($result['id'] == $check) { echo "checked";}} else { echo "checked";}?> value="<?php echo $result['ownership']?>"><?php echo $result['ownership']?></td>		
		<td><input type="radio" name="storage" <?php if(isset($_POST['SelectRow'])){if ($result['id'] == $check) { echo "checked";}} else { echo "checked";}?> value="<?php echo $result['ct_storage_c']?>"><?php echo $result['ct_storage_c']?></td>	
		<td><input type="radio" name="security" <?php if(isset($_POST['SelectRow'])){if ($result['id'] == $check) { echo "checked";}} else { echo "checked";}?> value="<?php echo $result['ct_security_c']?>"><?php echo $result['ct_security_c']?></td>	
		<td><input type="radio" name="networking" <?php if(isset($_POST['SelectRow'])){if ($result['id'] == $check) { echo "checked";}} else { echo "checked";}?> value="<?php echo $result['ct_networking_c']?>"><?php echo $result['ct_networking_c']?></td>	
		<td><input type="radio" name="hardware" <?php if(isset($_POST['SelectRow'])){if ($result['id'] == $check) { echo "checked";}} else { echo "checked";}?> value="<?php echo $result['ct_hardware_c']?>"><?php echo $result['ct_hardware_c']?></td>	

	  	  <?php 
	  }
	  ?>
  <?php

?>
      </tr>
    </tbody>
    </div>
  </table>
  </form>
  

</body>
</html>