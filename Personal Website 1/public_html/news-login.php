<?php 
session_start();

 if(isset($_SESSION["User"])){
	header('Location: news-list.php');
 }
$error = 0;
if(isset($_POST['login'])){
	require_once('../data-config.php');
$username = strtolower($_POST['username']);
$password = $_POST['password'];
$sql = "SELECT user, password FROM users WHERE user = '$username' LIMIT 1";
$results = $con->query($sql);
if(mysqli_num_rows($results) != 0){
foreach($results as $result){
	$hash = $result['password'];
	if(password_verify($password,$hash))
	{
			session_start();
			$_SESSION["User"]=$result['user'];
			header('Location: news-list.php');
	}
  else{
    $error = 1;
  }

}

}
  else{
    $error = 2;
  }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" name="viewport" content="width=device-width, initial-scale=1.0">
  <title>News Login</title>
  
<link rel="stylesheet" type="text/css" href="css/Skitia.css">
<script src="javascript/Skitia.js"></script>
 <link href="https://fonts.googleapis.com/css?family=Cinzel+Decorative|Lato&display=swap" rel="stylesheet">

</head>

  
<nav id = "mainNav">
   <a href="index.php" target = "_top"> <span id = "logoLabel">§<span class = "logoUnderline">kitia's</span> §<span class = "logoUnderline">tories</span></a></span>
   <span id = "mobileBar" onclick="openNav()">&#9776;</span>
  <a href="mods.php" target = "_top"> <span class = "navLink">Mods</a></span> 
   <a href="news.php" target = "_top"> <span class = "navLink">News</a></span> 
   <a href="about.php" target = "_top"> <span class = "navLink">About</a></span>
   <a href="https://forums.beamdog.com/profile/Skitia" target = "_top"> <span class = "navLink">Beamdog Forums</a></span>
    <a href="contact.php" target = "_top"> <span class = "navLink" id = "navContact">Contact</a></span> 

  </nav>

<div id="mobileNav" class="mobileOverlay">
  <a href="javascript:void(0)" class="overlayCloseBtn" onclick="closeNav()">&times;</a>
  <div class="mobileOverlay-content">
   <a href="index.php" target = "_top">Home</a>
    <a href="mods.php" target = "_top">Mods</a>
     <a href="news.php" target = "_top"> News</a>
     <a href="about.php" target = "_top"> About</a>
     <a href="https://forums.beamdog.com/profile/Skitia" target = "_top">Beamdog Forums</a>
   <a href="contact.php" target = "_top"> Contact</a>
  </div>
  </div>
<div id = contactBackground>
  
      <form method="post" id = "centerContact">
                     
     <h1 id = "contactHead">
       News Login
      </h1> 
            <p id = "contactText">
       Login for News Articles
      </p>
    
        Username 
    <br><input type = "text" class = "contactInput" placeholder = "Username" required name = "username"> <br>        

        Password 
     <br> <input type = "password" class = "contactInput" placeholder = "Password" required name = "password"> <br>
        <?php if ($error == 1){ ?>
<p class = "login-error">The password you entered is incorrect. Please try again.</p>
        <?php } ?>
                <?php if ($error == 2){ ?>
<p class = "login-error">That username is not on file. Please type the correct username.</p>
        <?php } ?>
        <input type = "submit" value = "Submit" id = "contactSubmit" name = "login"></input>
            </form>


  </div>
  <footer>
   <h1 id = "modFootHead">
         <a href="mods.php" target = "_top">Mods</a>
     </h1>
     <div class =  "modFoot">
  <?php 
     $c = 1;
       $cql = "SELECT name FROM characters";
$others = mysqli_query($con, $cql);
 $count = mysqli_num_rows($others); 
     foreach($others as $other){
  if ($c < $count) { $c++;
?>
     <a href = "mods-view.php?name=<?php echo $other['name'];?>"><?php echo $other['name'];?></a> |
<?php } else{    ?>
     <a href = "mods-view.php?name=<?php echo $other['name'];?>"><?php echo $other['name'];?></a> 
<?php }} ?>
     </div>
<br>
     
     <h1 id = "footHead"><a href="index.php" target = "_top">§<span class = "logoUnderline">kitia's</span> §<span class = "logoUnderline">tories</span>   </a></h1>
     
 
    <div id = "footContent">
           <a href="mods.php" target = "_top">Mods </a> |
  <a href="news.php" target = "_top">News</a> |
             <a href="about.php" target = "_top">About </a> |
      <a href="https://forums.beamdog.com/profile/Skitia" target = "_top">Beamdog Forums </a> |
    <a href = "contact.php" target = "_top" >Contact </a> 
          </div>
  </footer>

     
</body>
</html>