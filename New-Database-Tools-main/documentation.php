<?php 
session_start();
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
  <title>Documentation</title>
<style>
body { margin:0; padding:0; }
#Menu {
	max-width: 300px;
  background-color: #1DA8DB;
  color: AliceBlue;
  font-size: 20px;
line-height: 40px;
 box-sizing: border-box;
  cursor: pointer;
  padding-left: 10px;
padding-right: 10px;
margin-left: 1px;
}

#Log {
	max-width: 300px;
  background-color: #1DA8DB;
  color: AliceBlue;
  font-size: 20px;
  text-align:center;
padding-left: 22px;
padding-right: 22px;
padding-top: 9px;
padding-bottom: 8px;

  margin-left: -3px;
 box-sizing: border-box;
  cursor: pointer;
}
body{
	background-color:#42464F;
	color: #4FB5E6  ;
}
.content {
  max-width: 500px;

	background-color:AliceBlue;
	border:outset 15px #b3dbff;
	
 color: #4FB5E6  ;

}


h1 {
  color: #4FB5E6  ;
  margin-left: 105px;
  font-size: 40px;
 
} 



</style>
  </head>
<body>
<form action="" method="post">
<input type="button" name="List" id = "Menu" onclick="window.location.href = 'index.php';" value="Main Menu">
  <input type="submit" id = "Log" name="Log" value="Log Out">  
  </form>
<h1>Database Documentation</h1>

<h2>Logging In:<h2>
<p>There are currently three users registered for the Custom Deduplication Tool.
<ol>
<li>Developer (steven.heath@isaless.com)</li>
<li>Database (database.isaless.com)</li>
<li>IT (it.isaless.com)</li>
</ol>
The passwords are hashed in the database for security. If a password is forgotten, execute the following script in any php compiler:
<br><code>$hash = password_hash('PlaceDesiredPasswordInThisString',PASSWORD_DEFAULT);
echo $hash;</code><br>
Then copy the result and in the mysql database run the following query: <br>
<code>UPDATE sugarcrm_prod.databaseuser SET password = 'COPYHASHHERE' where email = 'EmailAssociatedWithPasswordHere' LIMIT 1;</code>
</p>

<h2>How to use the Account Deduplication Tool:</h2>
<p>After logging in, press the Account Duplicates List button or the Contacts Duplicate List Button. 
Due to the heavy number of contacts, the Contacts Page will take about 23 seconds to load. It is strongly recommended to keep it open once loaded in its own tab, and open up duplicates in a new tab. 
<div><img src = "Sample1.JPG"></div>
Clicking on this button will present a table with 1000 found duplicates ordered by name. It will take a second to load.
<div><img src = "Sample2.jpg"></div>
From here you can click the view/merge button to get more details. 
</p>
<p>
In the below example, a view/merge click shows 4 duplicates. 
<div><img src = "Sample3.jpg"></div>
Three of these is duplicates, but the fourth is not. We would first use Ignore on the non-duplicate, then go back and merge the three other duplicates by pressing the respective buttons.
By default, the last record is used as source to minimize clicking, and you can change to a different field as needed.
</p>
<p>
After merge or ignore is pressed, you will be directed to a merging/ignoring is complete page, where you can return to the list, return to the main screen, or log out.
</p>
<div><img src = "Sample4.jpg"></div>

<h2>Removing All Hot Call Tags from a Target List.</h2>
<div>
<p> To remove all hot call tags from a list:</p>
<ol type = "1">
<li>Click List Search from the Main Menu.</li>
<li>Type the ID or the name of the Target List.</li>
<li>Click Clear. If you get an error message, check your spelling. If more than one come up (Duplicate name), select the one you are wishing to clear.</li>
<li>You will be presented with a success message and can return to the menu or do another search.</li>
</ol>
</div>

<p>If you would like any additional features added to this area, please let the developer know.</p>

</body>
</html>
