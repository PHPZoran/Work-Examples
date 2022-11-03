<?php 
session_start();
if(isset($_POST['Log'])){
	unset($_SESSION["User"]);
	header('Location: login.php');
}
 if(isset($_SESSION["User"])){
 }
 else {
	 header('Location: login.php');
 }
 ?>
<!DOCTYPE html>
	 
<html>
<head>
<link rel="shortcut icon" href="/favicon.ico" type="image/x-icon">
<link rel="icon" href="/favicon.ico" type="image/x-icon">
<title>Database Main Menu</title>
<style>
body {
background-color:#42464F;
}
.content {
	max-width: 1200px;
	background-color:AliceBlue;
	border:outset 15px #b3dbff;
	  


}



h1 {
  color: #4FB5E6  ;
   margin: auto;
  font-size: 40px;
 text-align:center;
} 
.Login{
	color: #1DA8DB  ;
	 margin: auto;
	margin-bottom: 10px;
	
}



.Selections {
	display: block;
	width: 300px;
  background-color: #1DA8DB;
  color: AliceBlue;

  margin-bottom: 10px;
  margin-top: 10px;
  font-size: 24px;
  margin: auto;
line-height: 50px;
 box-sizing: border-box;
  cursor: pointer;
}


#image{
	text-align:center;
}

#red{
	color:red;
	text-align:center;
  font-family: "Times New Roman", Times, serif;

}
</style>
</head>
<body>

   <div id = "image"><img src = "Logo.png" width="300" height="100%"></div>
  <h1>Welcome to the Database Menu Page, <?php echo $_SESSION["User"];?>.</h1>
  
<form action="" method="post">
<input type="button" class = "Selections"  onclick="window.location.href = 'database.php';" value="Account Duplicates List"></div>
<input type="button" class = "Selections"  onclick="window.location.href = 'ContactList.php';" value="Contact Duplicates List"></div>
<input type="button" class = "Selections"  onclick="window.location.href = 'fuzzySearch.php';" value="Fuzzy Search"></div>
<input type="button" class = "Selections"  onclick="window.location.href = 'listBuild.php';" value="List Builder"></div>
<input type="button" class = "Selections"  onclick="window.location.href = 'ListSearch.php';" value="List Search"></div>
<input type="button" class = "Selections" onclick="window.location.href = 'MobileView.php';" value="Mobile Update Count"></div>
<?php  if($_SESSION["User"] == "Developer"){
?>
<input type="button" class = "Selections" name="List" onclick="window.location.href = 'RestoreList.php';" value="Restore List"></div>
<input type="button" class = "Selections" name="List" onclick="window.location.href = 'TestScript.php';" value="Developer Testing"></div>
<input type="button" class = "Selections" onclick="window.location.href = 'UpdateURL.php';" value="Update URL"></div>
<?php 
 }?>
<input type="button" class = "Selections" onclick="window.location.href = 'documentation.php';" value="View Documentation"></div>
<input type="submit" class = "Selections" name="Log" value="Log Out"></div>
</form>


</body>
</html>

