<?php 

require_once('../databaseconfig.php');
// File is saved and retrieved as this name. File should only ever have one name that is not modifiable by the user.
$i = 0;
$j = 0;
$sql = "SELECT account_id FROM {$db_name}.accounts JOIN accounts_contacts on accounts.id = account_id where accounts_contacts.deleted = 1 and accounts.deleted = 0 and accounts_contacts.date_modified != '2020-10-25 13:55:00' group by account_id LIMIT 20000";
$results = $con->query($sql);
foreach($results as $result){


$query = "SELECT contact_id FROM {$db_name}.accounts_contacts where accounts_contacts.deleted = 0 and account_id = '{$result['account_id']}'";
$checks = mysqli_query($con, $query);
if (mysqli_num_rows($checks) == 0){
echo $result['account_id'] . " has no contacts, restoring!" . "<br>";
$select = "SELECT contact_id FROM {$db_name}.accounts_contacts where accounts_contacts.deleted = 1 and account_id = '{$result['account_id']}'";
$contacts = $con->query($select);
foreach($contacts as $contact){
$restore_con = "UPDATE {$db_name}.contacts set deleted = 0 where id = '{$contact['contact_id']}'";	
$results = $con->query($restore_con);
}
$restore = "UPDATE {$db_name}.accounts_contacts set deleted = 0 where accounts_contacts.deleted = 1 and account_id = '{$result['account_id']}'";
$con->query($restore);
$j++;
}
else{
$update = "UPDATE {$db_name}.accounts_contacts set date_modified = '2020-10-25 13:55:00' where account_id = '{$result['account_id']}'";	
$con->query($update);
$i++;
}
} 
echo $i . " accounts had at least 1 contact.";
echo $j . " accounts had to be restored.";
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
<body>

</body>
</html>



