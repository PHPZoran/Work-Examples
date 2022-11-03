	
<?php 
require_once('../data-config.php');

// Page will not load if id is not set.
if(!isset($_GET['id'])){
header('Location: news-list.php');
}
// And Page will not load if you try to put something other than a number here.
$id = FILTER_VAR($_GET['id'], FILTER_VALIDATE_INT);
if(!is_int($id)){
header('Location: news-list.php');
}

if(isset($_POST['edit'])){
 
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
   
    $esql = "UPDATE news set header = '$header', subheader = '$sub', image = '$img', alt = '$alt', body = '$text', date = '$date' where number = '$id'";
if ($con->query($esql) === TRUE) {
  
    $error = "News entry updated succesfully";
} else {
    $error = "Unable to update record";
}
}   
  
  
// Get the highest id number for nav disabling. 
 $sql = "SELECT * FROM news where number = $id";
$results = $con->query($sql);
 $row = mysqli_num_rows($results); 
  // This will go back to news list if a non-existant id is input. 
if ($row == 0){
header('Location: news-list.php');
}
foreach ($results as $result){
 // Let's store what we need in variables. 
 
    $head = $result['header'];
  $sub = $result['subheader'];
 $img = $result['image'];
   $date = $result['date'];
   $alt = $result['alt'];
   $text = $result['body'];
   
}

?>
<!DOCTYPE html>
<html lang="en" >
<head>
<meta charset="UTF-8" name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo $header; ?></title>
<link rel="stylesheet" type="text/css" href="css/Skitia.css">
   <link href="https://fonts.googleapis.com/css?family=Cinzel+Decorative|Lato&display=swap" rel="stylesheet">
    <script src="javascript/Skitia.js"></script>

<style>
  #deleteBack{
    text-align:center;
    
  }
  #deleteBack input{
      margin-top:20px;
width: 10%; 
  color:  gold; 
  background-color: #000040;
  border: solid 2px gold;
  margin-right:15px;
  margin-left:15px;
   box-sizing: border-box;
  cursor: pointer;
   margin-bottom:20px;
   height: 40px;
    font-size: 150%;
    -webkit-appearance:none;
   
  }
  #newsEdit{
          margin-top:20px;
width: 40%; 
  color:  gold; 
  background-color: #000040;
  border: solid 2px gold;
  margin-right:15px;
  margin-left:15px;
   box-sizing: border-box;
  cursor: pointer;
   margin-bottom:20px;
   height: 40px;
    font-size: 150%;
    -webkit-appearance:none;
  }
  #editHead{
        font-family: 'Cinzel Decorative', cursive;
   text-align:center;
    
   margin-bottom:25px;
    text-decoration:underline;
    color: navy;
        font-size: 40px;
  }
  </style>
</head>
  <body id = "newsNavy">
    
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
      <h1 id = "editHead">
                          <?php if(isset($_POST['edit'])){ 
  
  echo $error; }
     else {echo "View Form";} ?>
      </h1> 

   
    
  
        <form action="news-list.php?id=<?php echo $id ?>" method="post" id = "deleteBack">
           <input type="submit" class = "newsButton" name="delete" value="Delete" onclick="return confirm('Are you sure you wish to delete?');">
                <input type="submit" class = "newsButton" name="back" id = "logButton" value="Go Back">
      </form>

     

        
     <div  id = "centerContact">
       
   
       <button class = "newsButton"  onclick="disable()" id = "newsEdit">
        Edit Entry
      </button>
        <form method="post">

   <fieldset disabled="disabled" id = "age">
   
    
        Header  
    <br><input type = "text" class = "contactInput" placeholder = "Header" required name = "header" value = "<?php echo $head; ?>"> <br>        
        Subheader  
    <br><input type = "text" class = "contactInput" placeholder = "Subheader" required name = "subheader" value = "<?php echo $sub; ?>"> <br> 
       Message<br><textarea placeholder="Text (Required)" name="message" id = "contactMessage"  rows="5" cols="33" required ><?php echo $text; ?></textarea>
        <br>

        Image 
        <br><select name="alt" class = "contactInput" required>
      <option value="BGEE" <?php if ($alt == "BGEE"){ echo "selected";}?></optio>>BG:EE</option>   
     <option value="SoD" <?php if ($alt == "SoD"){ echo "selected";}?>>SoD</option>    
   <option value="SoA" <?php if ($alt == "SoA"){ echo "selected";}?>>SoA</option>      
  <option value="ToB" <?php if ($alt == "ToB"){ echo "selected";}?>>ToB</option>     
  <option value="Emily" <?php if ($alt == "Emily"){ echo "selected";}?>>Emily</option>
  <option value="Helga" <?php if ($alt == "Helga"){ echo "selected";}?>>Helga</option>
  <option value="Kale" <?php if ($alt == "Kale"){ echo "selected";}?>>Kale</option>
    <option value="Recorder" <?php if ($alt == "Recorder"){ echo "selected";}?>>Recorder</option>
        <option value="Vienxay" <?php if ($alt == "Vienxay"){ echo "selected";}?>>Vienxay</option>
</select><br>
        Date
     <br> <input type = "date" class = "contactInput"  required name = "date" value = "<?php echo $date; ?>"> <br>
          <input type = "submit" value = "Submit" id = "contactSubmit" name = "edit"></input>      
     </fieldset>
            </form>
   </div>
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
    function disable(){
  document.getElementById("age").disabled = false;
    }
  </script>