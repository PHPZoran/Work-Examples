<?php 
require_once('../data-config.php');
if(isset($_GET['id'])){
$id = ($_GET['id']);
$id = FILTER_VAR($id, FILTER_VALIDATE_INT);
  if(!is_int($id)){
header('Location: news.php');
}
 
}
$sql = "SELECT * FROM news order by date desc";
$result = mysqli_query($con, $sql);
 $count = mysqli_num_rows($result); 
while($row =  mysqli_fetch_array($result, MYSQLI_ASSOC))
{
$rows[] = $row;
}
$pID = $id - 1;
$nID = $id + 1;

if ($id >= $count || $id < 0){
header('Location: news.php');
}


    $header = $rows[$id]['header'];
  $subheader = $rows[$id]['subheader'];
 $img = $rows[$id]['image'];
   $date = $rows[$id]['date'];
   $alt = $rows[$id]['alt'];
   $text = $rows[$id]['body'];
   


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
  
  #news-v-header{
     color:gold;
   

         background-image: url("images/NewsImage.jpg");
   
    background-color:rgba(0,0,0,0.4);
    background-blend-mode: overlay;
 
    padding-top:10px;
     text-align:center;
    border-bottom:1px solid gold;
  }
  #news-v-header img{
      filter:drop-shadow(8px 8px 64px silver);
    
    margin-bottom:25px;
    margin-top:25px;
  }
  #news-v-header h1{
       font-family: 'Cinzel Decorative', cursive;
     
       font-size:32px;
       margin-top:2%;
  } 
  #news-v-header h2{
      font-size:24px;
      margin-bottom:2%;
  }
  #news-v-header h3{
    font-size:14px;
    margin-bottom:2%;
  }
  #news-v-body {
    background-color:navy;
    text-align:center;
     color:AliceBlue;
    margin-left:2%;
    margin-right:2%;
    font-size:18px;
    margin-bottom:2%;
  
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
      <div id = "news-v-header">
    <h1>
      <?php echo $header; ?>
    </h1>
        <img src = " <?php echo $img; ?>">
        <h2>
            <?php echo $subheader; ?>
        </h2>
        <h3>
          <?php echo $date; ?>
        </h3>
    </div>
    <pre id = "news-v-body">
    
         <?php echo $text; ?>
      </pre>

    
      <nav id = "newsNav"> 
        <?php 
         
        ?> 
        <a href="news-view.php?id=<?php echo $pID; ?>" id = "newsPre"  <?php if ($id == 0) { echo 'style =  "display:none"';} ?>  >&laquo; Previous</a>         
 <?php 
        // If the URL = 0
        ?>
<a href="news-view.php?id=<?php echo $nID; ?>" id = "newsNext" <?php if ($id == $count - 1) { echo 'style =  "display:none"';} ?> >Next &raquo;</a>

 </nav>
    <div class = "clearFloats">
      
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