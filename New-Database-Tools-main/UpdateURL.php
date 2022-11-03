<?php 
session_start();

 if(isset($_SESSION["User"])){
	// echo "Welcome, " . $_SESSION["User"];
 }
 else 
 {
	echo "UNAUTHORIZED USER!, WILL FORCE TO LOGIN PAGE ON PROD";
 }
require_once('../databaseconfig.php');
// File is saved and retrieved as this name. File should only ever have one name that is not modifiable by the user.
define("File_Name","url.csv");

function save_file(){
$target_dir = getcwd() . "/";
$target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);	
$csvFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
// Check file size
if ($_FILES["fileToUpload"]["size"] > 12500000) {
	$error = "The file was too large. Try to upload a smaller file.";
    return 0; // File was too large
}
// Allow certain file formats
if($csvFileType != "csv") {
	$error = "The file was not in the proper .csv format. Please reupload the file as a regular csv.";
    return 2; // File was not csv 
}
    if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
       rename($target_file,$target_dir . File_Name);
       $error = "The file ". basename( $_FILES["fileToUpload"]["name"]). " has been uploaded.";
        return true;
    } else {
        $error =  "Sorry, there was a problem with the file upload.";
      return 3;
      
    }


}
// This function reads the saved file.
function read_file()
{

$arr=array();
$row = -1;
if (($handle = fopen(File_Name, "r")) !== FALSE) {

    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {

        $num = count($data);
        $row++;
        for ($c = 0; $c < $num; $c++) {
            $arr[$row][$c]= $data[$c];
			
			

        }
    }




    fclose($handle);
	
}	
return $arr;
}

// Main Function
if(isset($_POST['submitFR']) && save_file() == true){
	$error = "File loaded";
	$list = read_file();
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Contact Import &amp; Dupe Check</title>
  <style>
html, body, div, span, applet, object, iframe,
h1, h2, h3, h4, h5, h6, p, blockquote, pre,
a, abbr, acronym, address, big, cite, code,
del, dfn, em, img, ins, kbd, q, s, samp,
small, strike, strong, sub, sup, tt, var,
b, u, i, center,
dl, dt, dd, ol, ul, li,
fieldset, form, label, legend,
table, caption, tbody, tfoot, thead, tr, th, td,
article, aside, canvas, details, embed, 
figure, figcaption, footer, header, hgroup, 
menu, nav, output, ruby, section, summary,
time, mark, audio, video {
	margin: 0;
	padding: 0;
	border: 0;
	font-size: 100%;
	font: inherit;
	vertical-align: baseline;
      list-style-type: disc;
  
}
/* HTML5 display-role reset for older browsers */
article, aside, details, figcaption, figure, 
footer, header, hgroup, menu, nav, section {
	display: block;
}
  
body {
	line-height: 1;

  
}
   
  }

blockquote, q {
	quotes: none;
}
blockquote:before, blockquote:after,
q:before, q:after {
	content: '';
	content: none;
}
  
  #mainhead{
	  text-align:center;
	 
	  width: 350px;
	  margin: 0 auto;
	  margin-top: 200px;
	  margin-bottom: 0px;
	  font-size:48px;
  }
  #mainhead h1{
	border-bottom:outset 2px black;  
  }
  #file-importer{
	  margin-top:50px;
	  margin-left:auto;
	  margin-right:auto;
	/*  background: -webkit-linear-gradient(top, Skyblue,Navy);*/
	  text-align:center;
	  width:21%;
  }
.custom-file-input::-webkit-file-upload-button {
  visibility: hidden;
}
.custom-file-input::before {
	text-align:center;
	
  content: 'Select file';
  display: inline-block;
  background: -webkit-linear-gradient(top, #C09162,#E8C19A);
  border: 2px solid #603D22;
  border-radius: 3px;
  padding: 6px 9px;
  outline: none;
  white-space: nowrap;
  -webkit-user-select: none;
  cursor: pointer;
  text-shadow: 1px 1px #C09162;
  font-weight: 700;
  font-size: 10pt;
}

.custom-file-input:hover::before {
  border-color: black;
}
.custom-file-input:active::before {
  background: -webkit-linear-gradient(top, #e3e3e3, #f9f9f9);
}
#update{
	width:250px;
	background: -webkit-linear-gradient(left, #C09162,#E8C19A);
	  border: 2px solid #603D22;
  border-radius: 3px;
  padding: 5px 8px;
  cursor: pointer;
}
#import{
	width:250px;
	
	  border: 2px solid #603D22;
  border-radius: 3px;
  padding: 5px 8px;
 cursor: pointer;
}
table{
margin: 0 auto;
	background: -webkit-linear-gradient(top, #C09162,#E8C19A);
	  border-bottom: 2px black;
margin-top:50px;
  width: 500px;
  max-width: 600px;
  font-size: 20px;
}
th{
		border-top: 2px solid  black;
	border-bottom:2px solid black;
	border-left: 2px solid black;
	border-right: 2px solid black;
	
	font-size: 20px;
	height:25px;
	min-width: 300px;
}

table>thead {

;
}
tr, td { padding: 0; }
td{
		
	border-bottom:2px solid black;
	border-left: 2px solid black;	
	border-right: 2px solid black;
}
table>tbody {
text-align: center;
   font-size: 16px;
  
}
#map_key{
	float:right;
	width:500px;
	background: -webkit-linear-gradient(top, #C09162,#E8C19A);
}
#mapping{
	float:left;
	margin-left:25%;
	width:800px;
	background: -webkit-linear-gradient(top, #C09162,#E8C19A);
}
  </style>
</head>
<body style="background:url(Cloud.gif)">
<div id = "mainhead">
<h1>ISS URL Update</h1>

</div>
<br>

<div id = "file-importer">
<form method="post" enctype="multipart/form-data" style = "font-size:24px;">
      <input type="file" class="custom-file-input" name="fileToUpload" id = "fileToUpload">
    <input type="submit" value="Read<?php if(isset($_POST['submitFR'])){ echo " Another"; }?> File" name="submitFR" id = "update">
	  
	 <br>
	 <?php if(isset($_POST['submitFR'])){ ?>

</form>
<form method="post" enctype="multipart/form-data" action = "UpdateUrlRun.php" style = "font-size:24px;">
	 
	 <input type="submit" value="Update URL" name="update_url" id = "update"> <?php } ?>
</form>	 
</div>
<table>
   <thead>
      <tr>
	  <?php for ($size = 0; $size < sizeof($list[0]); $size++){
		  echo "<th>" . $list[0][$size] . "</th>";
	  }
	?>
        </tr>
    </thead>
	<tbody>
	<?php for($total_size = 1; $total_size < sizeof($list); $total_size++){
		//sizeof($list)
	echo "<tr>";
		  for ($size = 0; $size < sizeof($list[$total_size]); $size++){
		  echo "<td>" . $list[$total_size][$size] . "</td>";
	  }
	  echo "</tr>";
	}
	?>
      
	</tbody>
	</table>

</body>
</html>



