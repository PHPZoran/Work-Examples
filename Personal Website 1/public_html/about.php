<?php require_once("../data-config.php");?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" name="viewport" content="width=device-width, initial-scale=1.0">
  <title>About</title>
<link rel="stylesheet" type="text/css" href="css/Skitia.css">
<script src="javascript/Skitia.js"></script>
 <link href="https://fonts.googleapis.com/css?family=Cinzel+Decorative|Lato&display=swap" rel="stylesheet">

</head>
<body id = "aboutBackground">
  
  
  
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
<div id = "aboutContainer">
   <h1>
  About 
  </h1> 
  <div id = "aboutRight">
    <h2>
      About me
    </h2>
      <p>
        I first played Baldur's Gate almost twelve years ago. Having always loved the series, I wanted to give my hand to the many great mods before me at my own works.
   As you can see, my preference are NPC Mods, where I can develop an original character from start to finish and incorporate it into the great story of Baldur's Gate.

    </p>
    <h2>
       Other Details about Me
    </h2>
  
 
    
     <ul>
    <li>Favorite Characters: Aerie, Mazzy.</li>
   <br>
    <li>Favorite Class: Archer, Swashbuckler, Cavalier. </li>
      <br>
    <li>Favorite Baldur's Gate NPC Mod: Aura. </li> 
    </ul>
    
   
         <h2>
     Technical Site and Credits: </h2>

   <ul>
  <li>Mod Voices: Most of my Mods use sounds from NWN, except Recorder who uses sounds from Fire Emblem's Azura. </li>
   <br>
     <li>Portraits: Most of my mods use images from NWN, except Recorder who uses a portrait painted by the very talented Nicole Cadet. You can see her other work  <a href = "https://www.nicolecadet.com/">here</a>   </li>
   <br>
     <li>Requirements: All Mods require either BG:EE, BG:EE with SoD, or BG:EE(2). The old versions will not work.  </li>
    <br>
     <li>Influence: A lot of technical influence derives from AionZ (See their work here: ) who has made excellent mods of their own. You can learn more about them <a href = "https://artisans-corner.com/">here</a>.</li>
       <li>The Branwen mod by was also an excellent tutorial, which you can find <a href = "http://forums.pocketplane.net/index.php?topic=28623.0">here</a>.</li> 
  <li>Lastly, many characters were based or influenced by characters sourcing from the great NWN PW world Arelith, which you can learn about <a href = "http://www.nwnarelith.com/">here</a>.   </li>
   <br>  </li>
        <li>Availability to Help with Other Mods: I don't mind sharing a bit of time in proofreading or quick testing. Just reach out on the Beamdog Forums or via my Contact form.</li>
    </ul>
  </div>
  </div>
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
             <a href="#" target = "_top">About </a> |
      <a href="https://forums.beamdog.com/profile/Skitia" target = "_top">Beamdog Forums </a> |
    <a href = "contact.php" target = "_top" >Contact </a> 
          </div>
  </footer>

</body>
</html>