<?php 
// Database Connection

// File is saved and retrieved as this name. File should only ever have one name that is not modifiable by the user.
define("File_Name","fuzzy.csv");
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
This set of actions is executed when Execute Search is pressed, only available after the file is read and any desired filters are given.
It uses the class Approximate_Search in approximate-search.php 
*/
if(isset($_POST['submitS'])){ 
//Both approximate-search and databaseconfig are needed.
	require_once('approximate-search.php');
	require_once('../databaseconfig.php');
//Read the file 	
	$list = read_file();
// The data array is the data in the to-be downloaded file. 	
	$data = array(); 
//Set up phone and website only if they were set.	
	if(isset($_POST['phone'])){
		$phone = $_POST['phone'];
	}
	if(isset($_POST['website'])){
		$website = $_POST['website'];
	}
	//Loop through the file headers
	for($i = 0;$i<sizeof($list[0]);$i++){
		if($list[0][$i] == $_POST['TName']){
		//Set account id column to equal the selected user mapping
		$column = $i;
		}
	}	
// Query for Accounts
$search = 'name, id, billing_address_state as state, billing_address_country as country';
//Set up phone and website headers if user desired them. 
if(isset($_POST['phone'])){
$search = $search . ', phone_office as phone';	
}
if(isset($_POST['website'])){
$search = $search . ', website';	
}
// Filter will always at least not included deleted accounts.
$filter = 'where deleted = 0';
//Prepare the locale filter if anything was selected.
switch ($_POST['map_locale']) {
  case "USA":
    $filter = $filter . " and billing_address_country IN ('United States','US','USA','Puerto Rico')";
    break;
  case "Canada":
    $filter = $filter . " and billing_address_country IN ('Canada','CA')";
    break;
  case "UK":
    $filter = $filter . " and billing_address_country IN ('British Virgin Islands','UK','United Kingdom','England')";
    break;
  case "Brazil":
   $filter = $filter . " and billing_address_country IN ('BR','Brazil')";
    break;	
  default:
    //User selected Anywhere, therefore we do nothing.
} 
//Set up state filter, but only if there is inputted data. Filter removes comma and uses and/or's to get the full list.
if(!empty($_POST['TState'])){ 
$filter = $filter . " and billing_address_state = '";	
$listStateInput = $_POST['TState'];
$listStateInput = str_replace(',',"' or billing_address_state = '",$listStateInput);
$filter .= $listStateInput;
$filter .= "'";
}
//Set up SQL with completed filter and search.
$sql = "SELECT $search FROM {$db_name}.accounts $filter";	
//Get the results. 
$results = $con->query($sql);

//Loop for every account in the submitted file. 
for($j = 1;$j < sizeof($list);$j++){
//Prepare the account's row for excel sheet. At a mininum, account name will be there again even if there are no results. 	
$result_row = array($list[$j][$column]);	
//Set up findings for the smaller accounts.
$findings = 0;
//Do an exact search for any tiny accounts first.
if(strlen($list[$j][$column]) <= $_POST['exact_match']){
foreach($results as $result)
{
//Set up patterns. This will *try* to get a match where it may not be exact due to some characters, but this is no Fuzzy Search.
$patterns = array('/Inc./','/llc/','/ltd/','/ Inc/','/The /','/Industry Group/','/ Industry/','/ Corporation/','/ Financial/','/ International, Inc./');
$str = preg_replace($patterns, '', $result['name']);	
//If we find something, let's add all of the data into the account row for the excel sheet.
	if($result['name'] == $list[$j][$column] || $str == $list[$j][$column]){   
		$findings++; //Increment Findings if we find something so we don't go into the FuzzySearch after this.
		array_push($result_row,$result['name'],$result['id'],$result['state'],$result['country']);
		//If needed, add phone/website into the data.
		if(isset($_POST['phone'])){
			array_push($result_row,$result['phone']);	
		}
		if(isset($_POST['website'])){
			array_push($result_row,$result['website']);	
		}
		

		}		  
	}
}	
//This executes if the account character length is greater than the input match, or we didn't find anything in the exact query for the smaller one. 
if(strlen($list[$j][$column]) > $_POST['exact_match'] || $findings == 0){
//Remove this characters from the account so we can get better matching. 
$patterns = array('/Inc./','/llc/','/ltd/','/ Inc/','/The /','/Industry Group/','/ Industry/','/ Corporation/','/ Financial/');
$str = preg_replace($patterns, '', $list[$j][$column]);
//Start new Fuzzy Search 
$search = new Approximate_Search($str,$_POST['Treshold']);
// Prep Match Limit
$match_limit = 0;	
//Loop through all of the accounts in DB 		  
foreach($results as $result)
{
	//Only continue with the following if match limit isn't hit.
	if($match_limit < $_POST['matches']){
	//Execute around the found account.
        $matches = $search->search( $result['name'] );
		//Build the data row for all of the found matches.
        while( list($i,) = each($matches)){
		array_push($result_row,$result['name'],$result['id'],$result['state'],$result['country']);
		//Include phone/website if input was desired.
		if(isset($_POST['phone'])){
			array_push($result_row,$result['phone']);	
		}
		if(isset($_POST['website'])){
			array_push($result_row,$result['website']);	
		}
		$match_limit++;

		}		  
	}
}
}
//Append everything into the data file.
array_push($data,$result_row);
}
//Set up headers 
$headers =  array('Client Account Name', 'CRM Account Name', 'Account ID', 'Billing Address State', 'Billing Address Country');

if(isset($_POST['phone'])){
array_push($headers,'Phone');	
}
if(isset($_POST['website'])){
array_push($headers,'Website');	
}
//This adds any additional matches. Therefore, k starts at 2, or 2nd match and above.
for($k = 2;$k <= $_POST['matches'];$k++)
{
array_push($headers,'Client Account Name ' . $k, 'Account ID ' . $k,  'Billing Address State ' . $k, 'Billing Address Country ' . $k);
if(isset($_POST['phone'])){
array_push($headers,'Phone ' . $k);	
}
if(isset($_POST['website'])){
array_push($headers,'Website ' . $k);	
}
}


 // output headers so that the file is downloaded rather than displayed
header('Content-type: text/csv');
header('Content-Disposition: attachment; filename="FuzzySearch.csv"');
 
// do not cache the file
header('Pragma: no-cache');
header('Expires: 0');
 
// create a file pointer connected to the output stream
$file = fopen('php://output', 'w');
 
//If website is checked, push it into there. 
//array_push($test,'website');
// Repeat for phone 
// send the column headers
fputcsv($file, $headers);
 
// output each row of the data
foreach ($data as $row)
{
fputcsv($file, $row);
}
exit();

}
if(isset($_POST['submitFR'])){
	$file_error = save_file();
	
if($file_error == 0){	
		$list = read_file();

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
	  background: -webkit-linear-gradient(left, rgba(196,205,186,1),rgba(162,172,22,0.8),rgba(215,222,65,0.2));
	  text-align:center;
	  width:350px;
  }
.custom-file-input::-webkit-file-upload-button {
  visibility: hidden;
}
.custom-file-input:hover{
	cursor:pointer;
  background-color:rgba(196,205,186,0.4);
}
.custom-file-input::before {
	text-align:center;	
  content: 'Select file';
  display: inline-block;
  background-color:rgba(196,205,186,0.8);
  border: 2px solid rgb(41,33,25);
  border-radius: 30px;
  padding: 5px 8px;
  outline: none;
  white-space: nowrap;
  -webkit-user-select: none;
  cursor: pointer;
  font-size: 10pt;
}
.custom-file-input:hover::before {
  border-color: rgb(41,33,25);
}
.custom-file-input:active::before {
   background-color:rgba(196,205,186,0.8);
}
.upload{
	cursor:pointer;
	width:330px;
	 background-color:rgba(196,205,186,0.8);
	  border: 2px solid rgb(41,33,25);
  border-radius: 30px;
  margin-top:10px;
  padding: 5px 8px;
}
.upload:hover {
  background-color:rgba(196,205,186,0.4);
}
#mainHead{
	width:350px;
	background: -webkit-linear-gradient(left, rgba(196,205,186,1),rgba(162,172,22,0.8),rgba(215,222,65,0.2));
	color:rgb(14,7,8);
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
#treshold{
	width:25%;
}
  </style>
</head>
<body style="background:url(Forest.gif);background-size:100%;">
<!-- Displayed Instruction -->
<div id = "mainHead">
<h1>Fuzzy Search</h1>
<h2>Instructions:</h2>
<ol>
<li>Select File</li> 
<li>Read File</li>
<li>Set Desired Filters</li> 
<li>Execute Search</li>
<li>Save or Open Created File</li>
<li>Finish</li>
</ol>
</div>
<!-- This actually rarely displays, due to how the file download works. But keeping it here for legacy reasons. -->
<?php 
if(!isset($_POST['submitFR']) || $file_error > 0){ ?>
<div class = "file-importer">
<?php if(isset($_POST['submitS'])){ ?>
<h2>Search Complete: File Created</h2>
<?php }?>
<!-- Load and Read File Form -->
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
<?php 
}
?>
<?php 
if(isset($_POST['submitFR']) && $file_error == 0){ ?>
<div class = "file-importer">


<!-- Fuzzy Search Form -->
<form method="post"  style = "font-size:24px;">
 <label for="TName">Map Account Name:</label>
<select name="TName">
	  <?php for ($size = 0; $size < sizeof($list[0]); $size++){
		  echo "<option value='" . $list[0][$size] ."'>".$list[0][$size]."<br>";
	  } ?>
</select> <br>
 <label for="Treshold">Levenshtein Treshold:</label>
<select name="Treshold">
<option value="1">0</option>
	<option value="1">1</option>
  <option value="2">2</option>
  <option value="3">3</option>
  <option value="4">4</option>
   	<option value="5">5</option> 
</select> <br>
 <label for="matches">Max Matches</label>
<select name="matches">
	<option value="1">1</option>
  <option value="2">2</option>
  <option value="3">3</option>
  <option value="4">4</option>
   	<option value="5">5</option> 
</select>
<br>
 <label for="exact_match">Exact Match</label>
<select name="exact_match">
	<option value="2">2 Character Accounts</option>
  <option value="3">3 Character Accounts</option>
  <option value="4">4 Character Accounts</option>
  <option value="5">5 Character Accounts</option>
</select>
<br>		
   <input type="checkbox" name="phone" value="Phone">
  <label for="phone"> Include Phone Column</label>
   <br>
	
    <input type="checkbox" name="website" value="Website">
	  <label for="phone"> Include Website Column</label><br>
<label for="map_id">Filter Country</label>
<select name="map_locale">
	<option value="USA">USA</option>
  <option value="Canada">Canada</option>
  <option value="UK">UK</option>
  <option value="Brazil">Brazil</option>
   	<option value="Anywhere">Anywhere</option> 
</select>
<br>	
 <label for="TState">Filter by State:</label>
  <input type="text" placeholder="Separate with Comma" name = "TState">
 <br>
   <input type="submit" value="Execute Search" name="submitS" class = "upload">

</form>
<form method="post" style = "font-size:24px;">
    <input type="submit" value="Finish" name="goBack" class = "upload">
</form>
</div>	
<?php 
}
?>