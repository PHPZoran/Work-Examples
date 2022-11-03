<?php require_once("../data-config.php"); ?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" name="viewport" content="width=device-width, initial-scale=1.0">
 <link href="https://fonts.googleapis.com/css?family=Cinzel+Decorative|Lato&display=swap" rel="stylesheet">
  <script src="javascript/Skitia.js"></script>
  <link rel="stylesheet" type="text/css" href="css/Skitia.css">
  <title>Skitia's Stories</title>
<style>
 
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

  <header id = "homePicture">
   <div id = "homeTopLeft">
     <h1 class = "homeHeader">§<span class = logoUnderline>kitia's</span> §<span class = logoUnderline>tories</span></h1> 
     <h2 class = "homeSubheader">Mods for Baldur's Gate</h2>

     
<form action="mods.php">
    <button id = "homeButton">
     Details Here
    </button>
          </form>
         </div> 
      </header>
<article id = "homeContainer">
  <h1>
    New Friends. New Adventures.
  </h1>
  <p>
    Enter the rich world of Baldur's Gate once again with five new NPC mods to download to join your adventurers. 
    Explore a variety of personalities, from an elitist elf shadowdancing mage to a dashing, pint-sized halfling barbarian.
    <br>Each mod includes:
    <ul>
        <li> Original character with their own quest, background, and interactions with original NPCs.      </li>
       <li> Original Kits, spells, or item tailored to the character's background.      </li>
      <li> Content for BG:EE and SoD, with BG2:EE content coming soon.</li>
       <li> Crossmod content.</li>
       <li> Original music.</li>
       <li> Friendships and Romance.</li>
      
  </ul>
    
    
  </p>
  </article>

  <div class = "clearFloats"></div>
    

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
     
     <h1 id = "footHead"><a href="#" target = "_top">§<span class = "logoUnderline">kitia's</span> §<span class = "logoUnderline">tories</span>   </a></h1>
     
 
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