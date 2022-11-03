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
// Connect to database. 
	require_once('../databaseconfig.php');
// Query for duplicate contacts. Note use of [$db_name], as I do not believe it will always know what schema to use. 
$query = "SELECT first_name, last_name, phone_work, title, COUNT(*) as count FROM {$db_name}.contacts JOIN contacts_cstm ON id = id_c WHERE phone_work IS NOT NULL and phone_work != '' and deleted = 0 and ignore_duplicate_c = 0 GROUP BY phone_work, first_name, last_name
HAVING COUNT(*) > 1 ORDER BY last_name LIMIT 1000";

$results = $con->query($query);




?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Contact Duplicate List</title>
  <!--<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous"> -->
  <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.1.0/css/all.css">
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
  float:left;
}

#Log {
	max-width: 300px;
  background-color: #1DA8DB;
  color: AliceBlue;
  font-size: 20px;
line-height: 40px;
 box-sizing: border-box;
  cursor: pointer;
  float:left;
}




#red{
	color:red;
	text-align:center;
  font-family: "Times New Roman", Times, serif;

}

body{
	background-color:	#42464F
}
input[type=button], input[type=submit], input[type=reset] {
  background-color: #1DA8DB;
  color: AliceBlue;
  padding-right: 65px;
  padding-left: 65px;
  margin-bottom: 10px;
  margin-top: 10px;
  font-size: 40px;
  margin-left: -15px;
line-height: 80px;
 box-sizing: border-box;
  cursor: pointer;
}
#form{
	width = 100%;
	background-color = blue;
}
</style>
</head>
<body>
<form action="" method="post" id = "form">
<input type="button" name="List" id = "Menu" onclick="window.location.href = 'index.php';" value="Main Menu">
  <input type="submit" id = "Log" name="Log" value="Log Out">  <h1>Contact Duplicate List</h1>
  </form>

  <div class="container">
  <table id='dataTable'>
    <thead>
      <tr>
      <th> First Name </th>
      <th> Last Name </th> 
      <th> Phone </th> 
      <th>  Title </th> 
      <th>  Count </th> 
	  <th id = "Edge"> View/Merge</th>
        </tr>
    </thead>
    <tbody>
      <?php
      foreach($results as $result){
        ?>
        <tr>
        <td id = "AlignLeft"><?php echo $result['first_name']?></td>
        <td><?php echo $result['last_name']?></td>
        <td><?php echo $result['phone_work']?></td>
        <td id = "AlignLeft"><?php echo $result['title']?></td>
        <td><?php echo $result['count']?></td>
		 <td id = "Edge"><a href="/database/ContactsView.php?phone=<?php echo $result['phone_work']?>&first=<?php echo $result['first_name']?>&last=<?php echo $result['last_name']?>"><i class="fas fa-edit"></i></a></td>
          <?php
}
      ?>

      </tr>
    </tbody>
    </div>
  </table>

</body>
</html>