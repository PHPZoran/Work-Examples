


<?php
/*

A Few things to note in this script:
I'm using the mysqli_connect method. It doesn't work with every database, if for some reason, far flung in the future, a database switch is decided, and the script is not working, consider switching to PDO constructs
over MYSQLI ones. The reason for its use is the simplicity in filtering these variables (Because names with apostrophes will break the script if not declared as a param), and do we want a drop table Bobby? NO.
We didn't really need the Variable listing, we could have declared each variable with $posts and gets, but that seemed messy. The Variable listing makes it more clean to understand.
Ideally we should switch the previous scripts to the mysqli method as well, so we aren't declaring this database twice.

*/
// Requires the submit button being pressed, else nothing happens. (We'll redirect in a later update).
require_once('../databaseconfig.php');
if(isset($_POST['Log'])){
	session_start();
	unset($_SESSION["User"]);
	header('Location: login.php');
}
if(isset($_POST['submit'])){


	// Connect to Database, change this to just require a config file so we don't have to type this over and over again and can keep changse in one place. 


	// Variable Listing. These all need to be added to their respective tables. 
	$phone = ($_GET['phone']);
	$first = ($_GET['first']);
	$last = ($_GET['last']);
	$salutation = $_POST['salutation'];
	$title = $_POST['title'];
	$street = $_POST['street'];
	$city = $_POST['city'];
	$state = $_POST['state'];
	$zip = $_POST['zip'];
	$country = $_POST['country'];
	$direct = $_POST['direct'];
	$mobile = $_POST['mobile'];
	$assistant = $_POST['assistant'];
	$aphone = $_POST['aphone'];
	$id = $_POST['id'];
	$message = "Merging is complete";
	
// Query to obtain all the duplicate contacts. Presently 10 updates in this query total, +1 id check. 

$query = "UPDATE {$db_name}.contacts 
SET salutation = ?, title = ?, primary_address_street = ?, primary_address_city = ?, primary_address_state = ?, primary_address_postalcode = ?, primary_address_country = ?, phone_other = ?, phone_mobile = ?, assistant = ?, assistant_phone = ?
 where id = ?";

if($stmt = $con->prepare($query))
{
	
	// You neeed to add an s for every ?, and the variables need to be in order of use, or you willl have problems. 
	$stmt->bind_param('ssssssssssss',$salutation, $title, $street, $city, $state, $zip, $country, $direct, $mobile, $assistant, $aphone, $id);
	// Change the messages to be more detailed later. 
	// Uncomment the echoes if debugging. 
if($stmt->execute())
{
	//echo "Success";
}
else 
{
	//echo "Fail";
}
$stmt->close();	
}




// Foreach should start here with a search query done to grab the non-selected sources to get their ID's we are going to loop around. 
$query2 = "SELECT id FROM {$db_name}.contacts WHERE phone_work = '$phone' and first_name = '$first' and last_name = '$last' and id != '$id' and deleted != 1";
$results = $con->query($query2);
foreach($results as $result) {



// Merge the accounts_contacts table, all the contacts are going to change to the sole selected. No soft delete needed for this table. 

$calls = "Update {$db_name}.calls_contacts 
SET contact_id = ?
WHERE contact_id = ?";

if($stmt3 = $con->prepare($calls)){
	// You neeed to add an s for every ?, and the variables need to be in order of use, or you willl have problems. 
$stmt3->bind_param('ss',$id, $result['id']);

if($stmt3->execute())
{
	//echo "Success";
}
else 
{
	//echo "Fail";
}
$stmt3->close();	

}

$app = "Update {$db_name}.atc_appointments_contacts_c
SET atc_appointments_contactscontacts_ida = ?
WHERE atc_appointments_contactscontacts_ida = ?";

if($mergeapp = $con->prepare($app)){
	// You neeed to add an s for every ?, and the variables need to be in order of use, or you willl have problems. 
$mergeapp->bind_param('ss',$id, $result['id']);

if($mergeapp->execute())
{
	//echo "Success";
}
else 
{
	//echo "Fail";
}
$mergeapp->close();	

}

// Soft Delete from accounts
$query4 = "UPDATE {$db_name}.accounts_contacts
SET deleted = 1
WHERE id = ?";

if($stmt4 = $con->prepare($query4)){
	// You neeed to add an s for every ?, and the variables need to be in order of use, or you will have problems. 
$stmt4->bind_param('s', $result['id']);

if($stmt4->execute())
{
	//echo "Success";
}
else 
{
	//echo "Fail";
}
$stmt4->close();	

}

// Soft Delete from accounts
$query5 = "UPDATE {$db_name}.contacts
SET deleted = 1
WHERE id = ?";

if($stmt5 = $con->prepare($query5)){
	// You neeed to add an s for every ?, and the variables need to be in order of use, or you willl have problems. 
$stmt5->bind_param('s', $result['id']);

if($stmt5->execute())
{
	//echo "Success";
}
else 
{
	//echo "Fail";
}
$stmt5->close();	

}


}

// Echo some sort of Done or Success Message.
//echo "Merging is complete.";

}
elseif(isset($_POST['ignore'])) {
	// Grab id variable.
	$id = $_POST['id']; 
	// Update accounts_cstm table so this duplicate is hidden. 
$query = "UPDATE {$db_name}.contacts_cstm
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
	header('Location: ContactList.php');
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
  margin-left:80px;
 
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
<input type="button" name="List" id = "Back" onclick="window.location.href = 'ContactList.php';" value="Back to List">
<input type="button" name="Menu" id = "Menu" onclick="window.location.href = 'index.php';" value="Main Menu">
<input type="submit" name="Log" id = "Log" value="Log Out">
</form>
</body>
</html>
 
	