<?php 



// We will only pull contacts that have a defined phone, first name, and last name. 
session_start();
 if(isset($_SESSION["User"])){
	// echo "Welcome, " . $_SESSION["User"];
 }
 else 
 {
	 header('Location: login.php');
 }
	  $phone = ($_GET['phone']);
	  $first = ($_GET['first']);
	  $last = ($_GET['last']);
	  if(empty($phone) || empty($first) || empty($last))
	  {
		  // Return to previous location. 
		  
		  header('Location: ../database/ContactList.php');
		  die;
	  }
	  else {

// Connect to Database
require_once('../databaseconfig.php');
// Query to obtain all the duplicate contacts 
$query = "SELECT contacts.id as cid, salutation, first_name, last_name, title, phone_other, phone_work, phone_mobile, name, contacts.date_modified as date_modified, primary_address_street, primary_address_city, primary_address_state, primary_address_postalcode, primary_address_country, assistant, assistant_phone FROM {$db_name}.contacts JOIN contacts_cstm ON id = id_c JOIN accounts_contacts ON contacts.id = contact_id JOIN accounts on account_id = accounts.id  WHERE phone_work = ? AND first_name = ? and last_name = ? and contacts.deleted = 0 and ignore_duplicate_c = 0";
$stmt = $con->prepare($query);
$stmt->bind_param('sss', $phone, $first, $last);
$stmt->execute();
$results = $stmt->get_result();

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
.Merge {
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
<input type="button" name="List" id = "Back" onclick="window.location.href = 'ContactList.php';" value="Back">
  <input type="submit" id = "Log" name="Log" value="Log Out">  <h1>Duplicate Details</h1>
  </form>
  <p> Select the desired options for each category, then press Merge, or Ignore on the selected ID to clear it from the list.</p>
  	<form action="ContactsMerge.php?phone=<?php echo $phone;?>&first=<?php echo $first;?>&last=<?php echo $last;?>" method="post">
	<input type="submit" name="ignore" id="ignore" value="Ignore" onclick="return confirm('Are you sure you wish to ignore this duplicate?')";>
  <input type="submit" name="submit" class="Merge" value="Merge" onclick="return confirm('Are you sure you wish to merge?')";>
  <input type="submit" name="SelectRow" class="Merge" value="Select Row" formaction="ContactsView.php?phone=<?php echo $phone;?>&first=<?php echo $first;?>&last=<?php echo $last;?>">
  <div class="container">
  <table class='table'>
    <thead>
      <tr>
	  <th>  Source ID </th>
	   <th> Salutation </th> 
	    <th>  First Name </th> 
	   <th> Last Name </th>
		<th> Title </th> 	
		<th> Account </th> 		
		 <th> Direct Phone </th> 
		  <th> Work Phone </th> 
		    <th> Mobile Phone </th> 
			<th>Last Update </th>
      <th> Street </th>
	  <th> City </th>
	  <th> State </th>
      <th> Zip </th> 
      <th>  Country </th> 
	  <th>  Assistant </th> 
	  <th>  Assistant Phone</th> 
        </tr>
    </thead>
    <tbody>

      <?php
	  // Set up the fields for selection.
      foreach($results as $result){
        ?>

        <tr>
		<td><input type="radio" name="id" <?php if(isset($_POST['SelectRow'])){if ($result['cid'] == $check) { echo "checked";}} else { echo "checked";}?> value="<?php echo $result['cid']?>"><?php echo $result['cid']?></td>
        <td><input type="radio" name="salutation" <?php if(isset($_POST['SelectRow'])){if ($result['cid'] == $check) { echo "checked";}} else { echo "checked";}?> value="<?php echo $result['salutation']?>"><?php echo $result['salutation']?></td>
		<td><?php echo $result['first_name']?></td>
		<td><?php echo $result['last_name']?></td>
        <td><input type="radio" name="title" <?php if(isset($_POST['SelectRow'])){if ($result['cid'] == $check) { echo "checked";}} else { echo "checked";}?> value="<?php echo $result['title']?>"><?php echo $result['title']?></td>
		 <td><?php echo $result['name']?></td>
		<td><input type="radio" name="direct" <?php if(isset($_POST['SelectRow'])){if ($result['cid'] == $check) { echo "checked";}} else { echo "checked";}?> value="<?php echo $result['phone_other']?>"><?php echo $result['phone_other']?></td>
		<td><?php echo $result['phone_work']?></td>
		<td><input type="radio" name="mobile" <?php if(isset($_POST['SelectRow'])){if ($result['cid'] == $check) { echo "checked";}} else { echo "checked";}?> value="<?php echo $result['phone_mobile']?>"><?php echo $result['phone_mobile']?></td>
        <td><?php echo $result['date_modified']?></td>
		<td><input type="radio" name="street"<?php if(isset($_POST['SelectRow'])){if ($result['cid'] == $check) { echo "checked";}} else { echo "checked";}?> value="<?php echo $result['primary_address_street']?>"><?php echo $result['primary_address_street']?></td>
        <td><input type="radio" name="city" <?php if(isset($_POST['SelectRow'])){if ($result['cid'] == $check) { echo "checked";}} else { echo "checked";}?> value="<?php echo $result['primary_address_city']?>"><?php echo $result['primary_address_city']?></td>
		<td><input type="radio" name="state" <?php if(isset($_POST['SelectRow'])){if ($result['cid'] == $check) { echo "checked";}} else { echo "checked";}?> value="<?php echo $result['primary_address_state']?>"><?php echo $result['primary_address_state']?></td>
        <td><input type="radio" name="zip" <?php if(isset($_POST['SelectRow'])){if ($result['cid'] == $check) { echo "checked";}} else { echo "checked";}?> value="<?php echo $result['primary_address_postalcode']?>"><?php echo $result['primary_address_postalcode']?></td>
		<td><input type="radio" name="country" <?php if(isset($_POST['SelectRow'])){if ($result['cid'] == $check) { echo "checked";}} else { echo "checked";}?> value="<?php echo $result['primary_address_country']?>"><?php echo $result['primary_address_country']?></td>	
		<td><input type="radio" name="assistant" <?php if(isset($_POST['SelectRow'])){if ($result['cid'] == $check) { echo "checked";}} else { echo "checked";}?> value="<?php echo $result['assistant']?>"><?php echo $result['assistant']?></td>
		<td><input type="radio" name="aphone" <?php if(isset($_POST['SelectRow'])){if ($result['cid'] == $check) { echo "checked";}} else { echo "checked";}?> value="<?php echo $result['assistant_phone']?>"><?php echo $result['assistant_phone']?></td>	    
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