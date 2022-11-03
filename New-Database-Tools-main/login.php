<?php 
session_start();
 if(isset($_SESSION["User"])){
	header('Location: index.php');
 }
?>
<!DOCTYPE html>
<html>
<head>
<title>Database Login</title>
<style>
body 
{
	background-color: #42464F;
	
	  
}
.content {
  width: 350px;
  margin: auto;
	background-color:AliceBlue;
	border:outset 15px #b3dbff;
	margin-top: 25px;


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

input[type=button], input[type=submit], input[type=reset] {
  background-color: #1DA8DB;
  color: AliceBlue;
  padding-right: 65px;
  padding-left: 65px;
  margin-bottom: 10px;
  margin-top: 10px;
  font-size: 40px;
 margin: auto;
line-height: 80px;
 box-sizing: border-box;
  cursor: pointer;
  margin-left: 50px;
}
input[type=text], input[type=password]{
	 margin: auto;
	padding-left: 40px;
	 padding-right: 35px;
	 text-align: center;
	 margin-left: 50px;
}


#image{
	  max-width: 500px;
  margin: auto;
}

#red{
	color:red;
	text-align:center;
  font-family: "Times New Roman", Times, serif;

}

</style>
</head>
<body>

    <div id = "image"><img src = "Logo.png" width="500" height="100%"></div>

<?php 
if(isset($_POST['Enter'])){
	require_once('../databaseconfig.php');
$Email = strtolower($_POST['username']);
$password = $_POST['current-password'];
$found = 0;
$sql = "SELECT username, email, password FROM sugarcrm_prod.databaseuser WHERE email=?";
$stmt = $con->prepare($sql);
$stmt->bind_param('s', $Email);
$stmt->execute();
$results = $stmt->get_result();
foreach($results as $result){
	$hash = $result['password'];
	if(password_verify($password,$hash))
	{
			session_start();
			$_SESSION["User"]=$result['username'];
			header('Location: index.php');
	}
	else{
			echo "<p id ='red'>The password you entered is incorrect. Please try again.</p>";
			$found++;
	}


}
    if ($found == 0){
	echo "<p id ='red'>The email you entered does not match the one on file. Please try again.</p>";
	}
}
?>
	<div class="content">
   <h1> Database Log In </h1>
<form action="" method="post">
<div class = "Login"> <input type="text" name="username" placeholder="Enter your email address"></div>
<div class = "Login"> <input type="password" name="current-password" placeholder="Enter your password"></div>

<div><input type="submit" name="Enter" value="Log In" ></div>
</form>
</div>

</body>
</html>


