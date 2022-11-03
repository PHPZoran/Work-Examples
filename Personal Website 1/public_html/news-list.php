<?php 
session_start();
require_once('../data-config.php');
if(isset($_POST['Log'])){
$_SESSION = array();

// If it's desired to kill the session, also delete the session cookie.
// Note: This will destroy the session, and not just the session data!
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Finally, destroy the session.
session_destroy();
 
}

 if(!isset($_SESSION["User"])){
	header('Location: news-login.php');
 }

if(isset($_POST['create'])){

$header = filter_var($_POST['header'],FILTER_SANITIZE_STRING);
$sub = filter_var($_POST['subheader'],FILTER_SANITIZE_STRING);
$text = filter_var($_POST['message'],FILTER_SANITIZE_STRING);
$alt = filter_var($_POST['alt'],FILTER_SANITIZE_STRING); 
// Date is already sanitized by the input parameters of input date_type.  
$date = $_POST['date'];
switch ($alt) {
    case "BGEE":
        $img = "images/BGEE.jpg";
        break;     
    case "SoD":
        $img = "images/SoD.jpg";
        break;     
    case "SoA":
        $img = "images/SoA.jpg";
        break;     
    case "ToB":
        $img = "images/ToB.jpg";
        break;     
    case "Emily":
        $img = "images/Emily.jpg";
        break;    
    case "Helga":
        $img = "images/Helga.jpg";
        break;
    case "Kale":
        $img = "images/Kale.jpg";
        break;
    case "Vienxay":
        $img = "images/Vienxay.jpg";
        break;
    // Recorder is the default image used.
    default:
        $img = "images/Recorder.jpg";
}  
 
$csql = "INSERT INTO news (header, subheader, image, alt, body, date) VALUES ('$header','$sub','$img','$alt','$text','$date')";
if ($con->query($csql) === TRUE) {
    $error = "News entry created succesfully";
} else {
    $error = "Unable to create record";
}
  
}
if(isset($_POST['delete']))
{

if(!isset($_GET['id'])){
header('Location: news.php');
}
 
// And Page will not load if you try to put something other than a number here.
$id = FILTER_VAR($_GET['id'], FILTER_VALIDATE_INT);
if(!is_int($id)){
header('Location: news.php');
}  
 
$dsql = "DELETE FROM news where number = $id";  
if ($con->query($dsql) === TRUE) {
    
    $error = "News entry deleted succesfully";
} else {
   
    $error = "Unable to delete record";
}  
}
$sql = "SELECT number, header, image, date from news order by date desc";
$results = $con->query($sql);


?>

<!DOCTYPE html>
<html lang="en">
  <head>
<meta charset="UTF-8" name="viewport" content="width=device-width, initial-scale=1.0">
  <title>News Menu</title>
<link rel="stylesheet" type="text/css" href="css/Skitia.css">
   <link href="https://fonts.googleapis.com/css?family=Cinzel+Decorative|Lato&display=swap" rel="stylesheet">
<script src="https://kit.fontawesome.com/44c2c62673.js" crossorigin="anonymous"></script>
    <script src="javascript/Skitia.js"></script>
  <style>
    #createOverlay {
  height: 100%;
  width: 0;
  position: fixed;
  z-index: 1;
  top: 0;
  left: 0;
  background-color: rgb(84,158,197);
  background-color: rgba(0,0,0,0.8);
  overflow-x: hidden;
  transition: 0.5s;
 
}

#createOverlay-content {
   font-family: 'Cinzel Decorative', cursive;
  color:blue;
  position: relative;
  top: 5%;
 
  width: 100%;
  text-align: center;
  margin-top: 30px;
white-space:nowrap;
  overflow:hidden;
}

#createOverlay a {
  padding: 8px;
  text-decoration: none;
  font-size: 36px;
  color: gold;
  display: block;
  transition: 0.3s;
}

#createOverlay a:hover, #createOverlay a:focus {
  color: #f1f1f1;
  text-decoration:underline;
}

#createOverlay #createOverlayCloseBtn {
  position: absolute;
  top: 20px;
  right: 45px;
  font-size: 60px;
  text-decoration:none !important;
}
    #news-list-header{
      background-color:#2D577D;
      color:gold;
        text-align:center;
      padding-top:2%;
      padding-bottom:2%;
      
       
    }
    #news-list-header h1{
      font-size:36px;
      font-family: 'Cinzel Decorative', cursive;
      text-decoration:underline;
      padding-bottom:1%;
    }
    .newsButton{
      background-color:navy;
      color:gold;
      border:1px solid gold;
      margin-top:1%;
      height:25px;
      width:10%;
 
    }
    #logButton{
           margin-bottom:1%;
    }
    #news-t-container{
      background-color:#2D577D; 
      margin: 0 auto;
      padding-bottom:3%;
      padding-top:2%;
    }
    #news-t{
      width:80%;
      margin-right:10%;
      margin-left:10%;
      
    }
    #news-t tr{
      border:none;
      background-color:rgb(0,0,30);
    }
    #news-t th{
      
      text-align:center;
      height:50px;
       vertical-align: middle;
      font-weight:bold;
    }
    #news-t td{
      border:none;
      border-top:1px solid black;
      height:50px;
      text-align:center;
       vertical-align: middle;
    }
  </style>    
  </head>
  <body>
    
  
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
    
    <div id="createOverlay">
  <a href="javascript:void(0)" id="createOverlayCloseBtn" onclick="closeCreate()">&times;</a>
  <div id="createOverlay-content">
      <form method="post" id = "centerContact">
                     
     <h1 id = "contactHead">
       Create Form
      </h1> 
     
    
        Header  
    <br><input type = "text" class = "contactInput" placeholder = "Header" required name = "header"> <br>        
        Subheader  
    <br><input type = "text" class = "contactInput" placeholder = "Subheader" required name = "subheader"> <br> 
       Message<br><textarea placeholder="Text (Required)" name="message" id = "contactMessage"  rows="5" cols="33" requiref></textarea>
        <br>

        Image 
        <br><select name="alt" class = "contactInput" required>
      <option value="BGEE">BG:EE</option>   
     <option value="SoD">SoD</option>    
   <option value="SoA">SoA</option>      
  <option value="ToB">ToB</option>
 <option value="Helga">Helga</option>       
  <option value="Emily">Emily</option>
  <option value="Helga">Helga</option>
  <option value="Kale">Kale</option>
    <option value="Recorder" selected>Recorder</option>
        <option value="Vienxay">Vienxay</option>
</select><br>
        Date
     <br> <input type = "date" class = "contactInput"  required name = "date"> <br>
          <input type = "submit" value = "Submit" id = "contactSubmit" name = "create"></input>
            </form>
  </div>
  </div>
    <div id = "news-list-header">
      <h1>
        News Menu
      </h1>
       
      <button class = "newsButton"  onclick="openCreate()">
        Create Entry
      </button>
     <form action="" method="post">
           <input type="submit" class = "newsButton" name="Log" id = "logButton" value="Log Out">
      </form>
      <?php if(isset($_POST['create']) || isset($_POST['delete'])){ ?>
     <p>
       <?php echo $error; ?>
      </p>
      <?php } ?>
    </div>
    <div id = "news-t-container">
      <table id="news-t">
        <thead>
        <tr>
          <th>ID</th>
          <th>Header</th>
          <th>Image</th>
          <th>Date</th>
          <th>View</th>
          </tr>
        </thead>
        <tbody>
        <?php
          foreach($results as $result){
            ?>
          <tr>
          <td> <?php echo $result['number'];?></td>
             <td> <?php echo $result['header'];?></td>
             <td> <img src =  "<?php echo $result['image'];?>" width = "110"</td>
             <td> <?php echo $result['date'];?></td>
             <td><a href = "news-edit.php?id=<?php echo $result['number'] ?>"><i class="fas fa-eye"></i></a></td>
          </tr>
          <?php
          }
          ?>
        </tbody>
      </table>
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
    <script>
    
function openCreate() {
  document.getElementById("createOverlay").style.width = "100%";
}

function closeCreate() {
  document.getElementById("createOverlay").style.width = "0%";
}
    </script>
  </body>
</html>