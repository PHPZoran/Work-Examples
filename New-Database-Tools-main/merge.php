<?php
/*

A Few things to note in this script:
I'm using the mysqli_connect method. It doesn't work with every database, if for some reason, far flung in the future, a database switch is decided, and the script is not working, consider switching to PDO constructs
over MYSQLI ones. The reason for its use is the simplicity in filtering these variables (Because names with apostrophes will break the script if not declared as a param), and do we want a drop table Bobby? NO.
We didn't really need the Variable listing, we could have declared each variable with $posts and gets, but that seemed messy. The Variable listing makes it more clean to understand.


*/
// Requires the submit button being pressed, else nothing happens. (We'll redirect in a later update).
require_once('../databaseconfig.php');
if(isset($_POST['Log'])){
	session_start();
	unset($_SESSION["User"]);
	header('Location: login.php');
}





if(isset($_POST['submit'])){

	// Eventually get this with an include, but for testing purposes:
	// Connect to Database, change this to just require a config file so we don't have to type this over and over again and can keep changse in one place. 


	// Variable Listing. These all need to be added to their respective tables. 
	$id = $_POST['id'];
	$name = $_POST['name'];
	$phone = $_GET['phone'];
	$state = $_GET['state'];
	$website = $_POST['website'];
	$street = $_POST['street'];
	$city = $_POST['city'];	
	$zip = $_POST['zip'];
	$country = $_POST['country'];
	$industry = $_POST['industry'];
	$employees = $_POST['employees'];
	$ownership = $_POST['ownership'];
	$revenue = $_POST['revenue'];
	// Accounts_CSTM table, update in seperate query. 
	$timezone = $_POST['zone'];
	$storage = $_POST['storage'];
	$networking = $_POST['networking'];
	$hardware = $_POST['hardware'];
    $message = "Merging is complete";
// Query to obtain all the duplicate accounts. Presently 10 updates in this query total, +1 id check. 

$query = "UPDATE {$db_name}.accounts 
SET name = ?, website = ?, billing_address_street = ?, billing_address_city = ?, billing_address_postalcode = ?, billing_address_country = ?, industry = ?, employees = ?, ownership = ?, annual_revenue = ?
 where id = ?";
if($stmt = $con->prepare($query))
{
	// You neeed to add an s for every ?, and the variables need to be in order of use, or you willl have problems. 
	$stmt->bind_param('sssssssssss',$name, $website, $street, $city, $zip, $country, $industry, $employees, $ownership, $revenue, $id);
	// Change the messages to be more detailed later. 
	// Uncomment the echoes if debugging. 
if($stmt->execute())
{
	//echo "Victory";
}
else 
{
	//echo "True Fail";
}
$stmt->close();	
}
// NEXT Update. Update the Account.cstm table. 
$query2 = "UPDATE {$db_name}.accounts_cstm 
SET time_zone_c = ?, ct_storage_c = ?, ct_networking_c = ?, ct_hardware_c = ?
 where id_c = ?";

if($stmt2 = $con->prepare($query2)){

$stmt2->bind_param('sssss',$timezone, $storage, $networking, $hardware, $id);
	// You neeed to add an s for every ?, and the variables need to be in order of use, or you willl have problems. 
	//$stmt2->bind_param('sssss',$timezone, $storage, $networking, $hardware, $id);
	// Change the messages to be more detailed later. 
	
if($stmt2->execute())
{
	//echo "Victory";
}
else 
{
	//echo "True Fail";
}
$stmt2->close();	

}
// Foreach should start here with a search query done to grab the non-selected sources to get their ID's we are going to loop around. 
$query3 = "SELECT id FROM {$db_name}.accounts WHERE phone_office = '$phone' and billing_address_state = '$state' and id != '$id' and deleted != 1";
$results = $con->query($query3);
foreach($results as $result) {



// Merge the accounts_contacts table, all the contacts are going to go into the other account now. Because of this you do not need to set this delete to 0.
$query4 = "UPDATE {$db_name}.accounts_contacts 
SET account_id = ?
WHERE account_id = ?";

if($stmt4 = $con->prepare($query4)){
	// You neeed to add an s for every ?, and the variables need to be in order of use, or you willl have problems. 
$stmt4->bind_param('ss',$id, $result['id']);

if($stmt4->execute())
{
	//echo "Victory";
}
else 
{
	//echo "True Fail";
}
$stmt4->close();	

}
// Merge the accounts_atc_appointments tables. 
$query5 = "UPDATE {$db_name}.accounts_atc_appointments_1_c
SET accounts_atc_appointments_1accounts_ida = ?
WHERE accounts_atc_appointments_1accounts_ida = ?";

if($stmt5 = $con->prepare($query5)){
	// You neeed to add an s for every ?, and the variables need to be in order of use, or you willl have problems. 
$stmt5->bind_param('ss',$id, $result['id']);

if($stmt5->execute())
{
	//echo "Victory";
}
else 
{
	//echo "True Fail";
}
$stmt5->close();	

}

// Merge Opportunities
$query6 = "UPDATE {$db_name}.accounts_opportunities
SET account_id = ?
WHERE account_id = ?";

if($stmt6 = $con->prepare($query6)){
	// You neeed to add an s for every ?, and the variables need to be in order of use, or you willl have problems. 
$stmt6->bind_param('ss',$id, $result['id']);

if($stmt6->execute())
{
	//echo "Victory";
}
else 
{
	//echo "True Fail";
}
$stmt6->close();	

}

// Merge Account_Prospectlists
$query7 = "UPDATE {$db_name}.accounts_prospectlists_1_c
SET accounts_prospectlists_1accounts_ida = ?
WHERE accounts_prospectlists_1accounts_ida = ?";

if($stmt7 = $con->prepare($query7)){
	// You neeed to add an s for every ?, and the variables need to be in order of use, or you willl have problems. 
$stmt7->bind_param('ss',$id, $result['id']);

if($stmt7->execute())
{
	//echo "Victory";
}
else 
{
	//echo "True Fail";
}
$stmt7->close();	

}

// Merge Prospectlists_Prospects
$query8 = "UPDATE {$db_name}.prospect_lists_prospects
SET deleted = 1
WHERE related_id = ?";

if($stmt8 = $con->prepare($query8)){
	// You neeed to add an s for every ?, and the variables need to be in order of use, or you willl have problems. 
$stmt8->bind_param('s',$result['id']);

if($stmt8->execute())
{
	//echo "Victory";
}
else 
{
	//echo "True Fail";
}
$stmt8->close();	

}


// Soft Delete from accounts
$query9 = "UPDATE {$db_name}.accounts
SET deleted = 1
WHERE id = ?";

if($stmt9 = $con->prepare($query9)){
	// You neeed to add an s for every ?, and the variables need to be in order of use, or you willl have problems. 
$stmt9->bind_param('s', $result['id']);

if($stmt9->execute())
{
	//echo "Victory";
}
else 
{
	//echo "True Fail";
}
$stmt9->close();	

}

}

// Echo some sort of Done or Success Message.
//echo "Merging is complete.";

}
elseif(isset($_POST['ignore'])) {
	// Grab id variable.
	$id = $_POST['id']; 
	// Update accounts_cstm table so this duplicate is hidden. 
$query = "UPDATE {$db_name}.accounts_cstm
SET ignore_duplicate_c = 1
 where id_c = ?";
 // Prepare query

if($stmt = $con->prepare($query))
{
 // Bind the variable into the statement. 

	$stmt->bind_param('s',$id);

// Execute, uncomment if debug messages need to be seen. 
if($stmt->execute())
{
	$message = "This ID will be ignored going forward.";
}
else 
{
	//echo "True Fail";
}
$stmt->close();		
	
}

}
else 
{
	header('Location: database.php');
	die;
}	

// Some form of HTML with a log out button, a Return to Accounts Duplicate List, and a Go to Contacts Duplicate List. 
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Merge</title>
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
  margin-bottom:20px;
  font-size: 40px;
  text-align: center;
  position: relative;
  margin-left:200px;
 
} 

#Edge {
	border-right: 10px solid #8898A8;
}
#Menu {
	max-width: 600px;
  background-color: #1DA8DB;
  color: AliceBlue;
  padding-right: 150px;
  padding-left: 150px;
  margin-bottom: 10px;
  margin-top: 10px;
  font-size: 40px;
  margin-left: 550px;
line-height: 80px;
 box-sizing: border-box;
  cursor: pointer;
}
#Back {
	max-width: 600px;
  background-color: #1DA8DB;
  color: AliceBlue;
  padding-right: 145px;
  padding-left: 145px;
  margin-bottom: 10px;
  margin-top: 10px;
  font-size: 40px;
  margin-left: 550px;
line-height: 80px;
 box-sizing: border-box;
  cursor: pointer;
}
#Log {
	max-width: 600px;
  background-color: #1DA8DB;
  color: AliceBlue;
  padding-right: 178px;
  padding-left: 178px;
  margin-bottom: 10px;
  margin-top: 10px;
  font-size: 40px;
  margin-left: 550px;
line-height: 80px;
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

body{
	background-color:	#42464F
}
#image{
	  max-width: 500px;
   margin-left: 550px;
}
</style>
  </head>
<body>
<div id = "image"><img src = "Logo.png" width="500" height="100%"></div>
     <h1><?php echo $message;?></h1>
<form action="" method="post">
<input type="button" name="List" id = "Back" onclick="window.location.href = 'database.php';" value="Back to List">
<input type="button" name="Menu" id = "Menu" onclick="window.location.href = 'index.php';" value="Main Menu">
<input type="submit" name="Log" id = "Log" value="Log Out">
</form>
</body>
</html>
 
	