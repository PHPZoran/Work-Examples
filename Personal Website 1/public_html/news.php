<?php 
require_once('../data-config.php');
if(isset($_GET['page'])){
$page = ($_GET['page']);
$page = FILTER_VAR($page, FILTER_VALIDATE_INT);
  if(!is_int($page)){
header('Location: news.php');
}
   if($page < 1){
    $page = 1;
  }
}
else{ 
  $page = 1;
    }
$sql = "SELECT header, subheader, date, image, alt FROM news order by date desc";
$result = mysqli_query($con, $sql);
 $count = mysqli_num_rows($result); 
while($row =  mysqli_fetch_array($result, MYSQLI_ASSOC))
{
$rows[] = $row;
}
$pPage = $page - 1;
$nPage = $page + 1;
$min = ($page - 1) * 10; // For now. Th eactual formula should be (page # - 1) * 10
$max = $min + 10;  
if ($max >= $count){
  $limit = $count;
}
else {
  $limit = $max;
}
if ($min >= $count){
header('Location: news.php');
}



 
?>
<!DOCTYPE html>
<html lang="en" >
<head>
<meta charset="UTF-8" name="viewport" content="width=device-width, initial-scale=1.0">
  <title>News</title>
<link rel="stylesheet" type="text/css" href="css/Skitia.css">
   <link href="https://fonts.googleapis.com/css?family=Cinzel+Decorative|Lato&display=swap" rel="stylesheet">
    <script src="javascript/Skitia.js"></script>


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
<div id = "newsPage">
  <div id = "news-container">
  <h1>
    News
  </h1>    
  </div>
    </div>

       <?php
      for ($i = $min; $i < $limit; $i++){
        /* Run some code to prevent the 11th option from displaying. 
       We run 11 and exclude one to prevent the user from navigating to a page with less than one news article.*/
        ?>
<div class="flex-container">
  <a href = "news-view.php?id=<?php echo $i?>"> <img src="<?php echo $rows[$i]['image']; ?>" alt="<?php echo $rows[$i]['alt']?>" style="flex-shrink: 2" ></a>
  <div style="flex-grow: 6" id = "newsInfo">    <h2>
    <a href = "news-view.php?id=<?php echo $i?>"> <?php echo $rows[$i]['header']?></a>
      </h2>
      <h3 class = "newsSubhead">
<?php echo $rows[$i]['subheader']?>
      </h3>
   
  <div class = "newsDate">
     <?php echo $rows[$i]['date']?>
    </div>
  </div>

</div>
              <?php
}
      ?>
      <nav id = "newsNav"> 
        <?php 
         
        ?> 
        <a href="news.php?page=<?php echo $pPage; ?>" id = "newsPre"  <?php if ($page <= 1) { echo 'style =  "display:none"';} ?>  >&laquo; Previous</a>         
 <?php 
        // If the URL = 0
        ?>
<a href="news.php?page=<?php echo $nPage; ?>" id = "newsNext" <?php if ($max >= $count) { echo 'style =  "display:none"';} ?> >Next &raquo;</a>

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
  <a href="#" target = "_top">News</a> |
             <a href="about.php" target = "_top">About </a> |
      <a href="https://forums.beamdog.com/profile/Skitia" target = "_top">Beamdog Forums </a> |
    <a href = "contact.php" target = "_top" >Contact </a> 
          </div>
  </footer>
</body>
</html>