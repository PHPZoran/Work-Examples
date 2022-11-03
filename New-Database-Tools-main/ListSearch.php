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
 
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>List Search</title>
  <!--<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous"> -->
  <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.1.0/css/all.css">
  <style>
  #header {
	 display: block;
	  width: 500px;
	  margin-bottom: 25px;
	  float:left;
  }
    #logo {
	 display: block;
	margin-left: auto;
	margin-right: auto;
	  width: 500px;
	  margin-bottom: 25px;
  }
    #search {
		text-align: center;
		font-size:20px;
		color: #1DA8DB  ;
	margin: auto;
	  width: 500px;
	  background-color: white;
	  border:outset 15px #b3dbff;
	  height: 150px;
  }
  body{
	  margin: 0;
	  padding: 0;
	  background-color:#42464F;
  }
  input[type=button], input[type=submit], input[type=reset] {
  background-color: #1DA8DB;
  color: AliceBlue;
 box-sizing: border-box;
  cursor: pointer;
}
.clear{
	clear:both;
}
#Menu {
	width: 150px;
  font-size: 20px;
line-height: 40px;
margin-left: -5px;
}
  </style>
</head>
<body>
<div id = "header">
<form action="" method="post">
<input type="button" name="List" id = "Menu" onclick="window.location.href = 'index.php';" value="Main Menu">
  <input type="submit" id = "Menu" name="Log" value="Log Out"></form>
</div>
<div class = "clear"></div>
<div id = "logo">
<img src = "Logo.png" width="500" height="100%">
</div>
<div class = "clear"></div>
<div id = "search">
<h1>Search List by ID or Name</h1>
<form action="ListResults.php" method="post">
<input type="text" name="search" placeholder="Enter ID or Name">
<input type="submit" name="submit" value="Search" >
 </form>
</div>
</body>
</html>