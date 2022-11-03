<?php 
/*
LIST BUILDER 
-This Tool loads in a file with account ids, takes those account ids and adds them to a list, and then allows the user to build the list from the added account ids.
Step 1: Select File: The user clicks the Select File button. This button is only visible when no submit functions have been pressed, or their file had an error. (See the numbered returns and the comments above them in function save_file.)
Step 2: Read File: This uses the save_file and read_file functions, saving the file in the database directory in sugar. Note that build.csv will always be the saved file name.
Step 3: Search Target List (See (isset($_POST['submitTS'])): The user types in a few characters that the target list has. It can be the entire target list file name, query is a LIKE "%TEXT%" query, but less characters is a safer search. It will return an error if nothing is found.
Step 4: Build Accounts: The user selects the desired target lists and clicks build accounts, firing off the if(isset($_POST['submitBA'])) condition.
Ste[ 5: Build Contacts. The user need not re-select the target lists, as they are defaulted. They just need to click build contacts, firing off the if(isset($_POST['submitBC'])) condition.
*/
// Database Connection
require_once('../databaseconfig.php');
// File is saved and retrieved as this name. File should only ever have one name that is not modifiable by the user.
define("File_Name","build.csv");
// Saves the imported file.
/*
Function Save_File: This function saves the file that the user uploads. It MUST be a regular CSV File (None of the special options that excel provides tos ave it as.). The size can be increased if needed, but a limit of some kind is still recommended.
Presently the file is saved in the database folder directory. If the file doesn't upload succesfully, it won't proceed to the next step, instead displaying one of the error messages, in comments within the function.
*/
function save_file(){
	//Get Directory
$target_dir = getcwd() . "/";
//Set up File Name
$target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);	
//Set up Extension
$csvFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
// Check file size
if ($_FILES["fileToUpload"]["size"] > 1250000) {
	//echo "The file was too large. Try to upload a smaller file.";
    return 1; // file was too large
}
// Allow certain file formats
if($csvFileType != "csv") {
	//echo "The file was not in the proper .csv format. Please reupload the file as a regular csv.";
    return 2; // File was not csv 
}
    if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
       rename($target_file,$target_dir . File_Name);
    //   echo "The file ". basename( $_FILES["fileToUpload"]["name"]). " has been uploaded.";
        return 0;
    } else {
       //echo "Sorry, there was a problem with the file upload.";
      return 3;      
    }
}
/*
Function read_file: This function reads the file. It is used in the first few steps, though not in contact building. 
*/
function read_file()
{
//Set up array
$arr=array();
//Start pointer before zero, so at -1.
$row = -1;
if (($handle = fopen(File_Name, "r")) !== FALSE) {
//Loop until we have read the entire file.
    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
        $num = count($data);
        $row++;
        for ($c = 0; $c < $num; $c++) {
            $arr[$row][$c]= $data[$c];
        }
    }
//Close File
    fclose($handle);	
}	
//Return the file array
return $arr;
}
/*
Function target_list_id: This function retrieves the target_list_id from the name of the target list. It is used during account and contact building. It ignores deleted lists, so only one result is returned and needed.
*/
function target_list_id($prospect_list_name,$con){
//Query for the id from the list name.
		$list_name_query = "SELECT id FROM {$db_name}.prospect_lists where name = ? and deleted = 0";
//Prepare database connection
		$stmt = $con->prepare($list_name_query);
//Bind the parameter		
		$stmt->bind_param(s, $prospect_list_name);	
//Execute Query		
		$stmt->execute();
//Get the result		
		$result = $stmt->get_result();
//There should only be one result, so get the first one.		
		$target_id = mysqli_fetch_assoc($result);
//Return the target id		
		return $target_id;
}
/*
Function list_build: This function builds the target lists contacts or accounts, depending what is put into the function. The id is created from the prospect_list and contact/account id. This will prevent the same contact/account from being on the list twice,
though will not prevent it from being on two different lists, so users should still take care not to run the same file through twice unless a check is added to prevent it.
*/
function list_build($target_list_id,$related_id,$con,$type){
	//Create prospect_list_prospect id
	$plp_id = substr($related_id,0,8) . "-" . substr($target_list_id,0,4) . "-" . substr($target_list_id,4,4) . "-" . "12f45cb4e5cd";
	//Prepare query, add in variables, and execute.
	$list_insert = "INSERT {$db_name}.prospect_lists_prospects (id, prospect_list_id, related_id, related_type, date_modified, deleted) VALUES (?,?,?,?,'2050-12-31 23:59:59',0)";	
	$stmt = $con->prepare($list_insert);
	$stmt->bind_param(ssss,$plp_id,$target_list_id,$related_id,$type);	
	$result = $stmt->execute();
	//Return Results
	return $result;
}
// Runs when File is first uploaded
if(isset($_POST['submitFR'])){
	$file_error = save_file();
if($file_error == 0){	
	$list = read_file();
}	
}
//Takes in user's list search parameter
if(isset($_POST['submitTS'])){
//Set up like search to be contained anywhere within the name.	
	$search = "%" . $_POST['TSearch'] . "%";
//Prepare query, bind variables, and execute, then get result.	
	$query = "SELECT name FROM {$db_name}.prospect_lists where name LIKE ? and deleted = 0";
	if($stmt = $con->prepare($query)){
		$stmt->bind_param(s, $search);	
		$stmt->execute();
		$results = $stmt->get_result();
		//Make sure that we have aresult.
		if(mysqli_num_rows($results)==0){ 
		//Return an error, preventing access to the next step.
		$search_error = true;
		}		
	}
}
//User selected target lists 
if(isset($_POST['submitTL'])){
$column_list = read_file();
}
//User submitted to build accounts, executes account building
if(isset($_POST['submitBA'])){
    // Retrieving each selected option 
    $list_count = count($_POST['prospect_lists']);
	//Read the file		
	$user_file = read_file();
	//Loop through the file headers
	for($i = 0;$i<sizeof($user_file[0]);$i++){
		if($user_file[0][$i] == $_POST['map_id']){
		//Set account id column to equal the selected user mapping
		$column = $i;
		}
	}
	//Remove the first row.
	unset($user_file[0]);
	//Re-index array
	$user_file = array_values($user_file);
	//Randomize the accounts
	shuffle($user_file);
	//Get the number to be shared for each account.
	$divide = ceil(sizeof($user_file)/$list_count);
	//Set account row to start at 0, and we'll do nothing when it equals or exceeds size of user_file array
	$account_row = 0;
	//Set the type of data we are adding to the list.
	$type = "Accounts";
	//Loop through each target list X number of times.
	for($i = 0;$i<count($_POST['prospect_lists']);$i++){	
		//Target List 1->Get ID 
	$target_id = target_list_id($_POST['prospect_lists'][$i],$con);	
		//Loop through its share of accounts 
		for($j = 0;$j<$divide;$j++){
			if($account_row < sizeof($user_file)){
				//Build Accounts into list
				$result = list_build($target_id['id'],$user_file[$account_row][$column],$con,$type);
			//Increment account_row
			$account_row++;			
			}
		}					
	}			
} 
//Contact Building 
if(isset($_POST['submitBC'])){
	//Set the type of data we are adding to the list. 
	$type = "Contacts";
	//Run for each target list 
	for($i = 0;$i<count($_POST['prospect_lists']);$i++){
	//Get Target list ID
		$target_id = target_list_id($_POST['prospect_lists'][$i],$con);	
		//Query Prospect List Prospects, bind variables, execute, and get result. 
		$query = "SELECT related_id FROM {$db_name}.prospect_lists_prospects where prospect_list_id = ? and date_modified = '2050-12-31 23:59:59' and deleted = 0 and related_type = 'Accounts'";
		$stmt = $con->prepare($query);
		$stmt->bind_param(s, $target_id['id']);	
		$stmt->execute();
		$account_results = $stmt->get_result();
		//For each account, get the ontacts, then add them into the list.
		foreach($account_results as $aresult)
		{
			$contact_query = "SELECT contact_id FROM {$db_name}.accounts_contacts where account_id = ? and deleted = 0";
			$stmt = $con->prepare($contact_query);
			$stmt->bind_param(s, $aresult['related_id']);	
			$stmt->execute();
			$contacts = $stmt->get_result();
			foreach($contacts as $contact){
			$result = list_build($target_id['id'],$contact['contact_id'],$con,$type);
			}		
		}
	}
} 
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>List Builder</title>
  <style>
  .file-importer{
	  margin-top:50px;
	  margin-left:auto;
	  margin-right:auto;
	  background-color:rgba(85,190,220,0.8);
	  text-align:center;
	  width:350px;
  }
.custom-file-input::-webkit-file-upload-button {
  visibility: hidden;
}
.custom-file-input:hover{
	cursor:pointer;
  background-color:rgba(240,248,255,0.4)
}
.custom-file-input::before {
	text-align:center;	
  content: 'Select file';
  display: inline-block;
  background-color:rgba(175,236,246,0.8);
  border: 2px solid #603D22;
  border-radius: 3px;
  padding: 5px 8px;
  outline: none;
  white-space: nowrap;
  -webkit-user-select: none;
  cursor: pointer;
  font-size: 10pt;
}
.custom-file-input:hover::before {
  border-color: black;
}
.custom-file-input:active::before {
   background-color:rgba(175,236,246,0.8);
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
  </style>
</head>
<body style="background:url(Cloud.gif)">
<div id = "mainHead">
<h1>List Builder</h1>
<h2>Instructions:</h2>
<ol>
<li>Select File</li> 
<li>Read File</li>
<li>Search Target List(s)</li> 
<li>Select Target List(s)</li>
<li>Map Account ID</li>
<li>Build Accounts</li>
<li>Build Contacts</li>
</ol>
</div>
<!-- Load and Read File Form -->
	 <?php if((!isset($_POST['submitFR']) || $file_error > 0) && !isset($_POST['submitTS']) && !isset($_POST['submitBA']) && !isset($_POST['submitTL'])){ ?>
<div class = "file-importer">
<?php if(isset($_POST['submitBC'])){?><h2>Contact Building Completed</h2> <?php }?>
<?php if(isset($file_error)){
if($file_error == 1){?><h3>The file was too large. Try to upload a smaller file.</h3> <?php }
elseif($file_error == 2){?><h3>The file was not in the proper .csv format. Please reupload the file as a regular csv.</h3> <?php }
else{?> <h3>Sorry, there was a problem with the file upload.</h3> <?php }} ?>
<form method="post" enctype="multipart/form-data" style = "font-size:24px;">
      <input type="file" required class="custom-file-input" name="fileToUpload" id = "fileToUpload" >
	 <br>
    <input type="submit" value="Read File" name="submitFR" class = "upload">
</form>
<form action="index.php" style = "font-size:24px;">
    <input type="submit" value="Go Back" name="goBack" class = "upload">
</form>
</div>
<!-- Target List Search Form -->
<?php } ?>
	 <?php if((isset($_POST['submitFR']) || isset($search_error))  && $file_error == 0){ ?>
<div class = "file-importer">
<?php if(isset($search_error)){?><h2>No results: Try a different query</h2> <?php }?>
<form method="post" style = "font-size:24px;">
  <input type="text" placeholder="Type text to find your target list(s)" name = "TSearch" required minlength=3>
    <input type="submit" value="Search" name="submitTS" class = "upload">
</form>
<form method="post" style = "font-size:24px;">
    <input type="submit" value="Start Over" name="goBack" class = "upload">
</form>
</div>
<!-- Select List Form -->
<?php } 
if(isset($_POST['submitTS']) && !isset($search_error)){ ?>
<div class = "file-importer">
<form method="post" style = "font-size:24px;">
<label for="prospect_lists">Choose your lists:</label>
<br>
<select name="prospect_lists[]" size="8" multiple required>
<?php foreach($results as $result){
 echo "<option value='".$result['name']."'>".$result['name']."<br>";
} ?>
</select>
<input type="submit" value="Submit Selection" name="submitTL" class = "upload">
</form>
<form method="post" style = "font-size:24px;">
    <input type="submit" value="Start Over" name="goBack" class = "upload">
</form>
</div>
<!-- Build Account Form -->
<?php } 
if(isset($_POST['submitTL']) && !isset($search_error)){ ?>
<div class = "file-importer">
<form method="post" style = "font-size:24px;">
<label for="prospect_lists">Selected Lists:</label>
<br>
<select name="prospect_lists[]" size="8" multiple required>
<?php foreach($_POST['prospect_lists'] as $result){
 echo "<option value='".$result."' selected>".$result."<br>";
} ?>
</select>
<br>
<label for="map_id">Map Account ID</label>
<br>
<select name="map_id">
	  <?php for ($size = 0; $size < sizeof($column_list[0]); $size++){
		  echo "<option value='" . $column_list[0][$size] ."'>".$column_list[0][$size]."<br>";
	  } ?>
</select>
<br>
<input type="submit" value="Build Accounts" name="submitBA" class = "upload">
</form>
</form>
<form method="post" style = "font-size:24px;">
    <input type="submit" value="Start Over" name="submitTS" class = "upload">
</form>
</div>
<!-- Build Contacts Form -->
<?php }
if(isset($_POST['submitBA'])){ ?>
<div class = "file-importer">
<form method="post" style = "font-size:24px;">
<label for="prospect_lists">Accounts Built For:</label>
<br>
<select name="prospect_lists[]" size="8" multiple required>
<?php foreach($_POST['prospect_lists'] as $result){
 echo "<option value='".$result."' selected>".$result."<br>";
} ?>
</select>
    <input type="submit" value="Build Contacts" name="submitBC" class = "upload">
</form>
</div>
<?php } ?>
</body>
</html>