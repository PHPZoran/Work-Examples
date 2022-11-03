<?php 
require_once('../data-config.php');
$sql = "SELECT * FROM characters";
$results = mysqli_query($con, $sql);
$count = mysqli_num_rows($results); 
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" name="viewport" content="width=device-width, initial-scale=1.0">
 <link href="https://fonts.googleapis.com/css?family=Cinzel+Decorative|Lato&display=swap" rel="stylesheet">

  <link rel="stylesheet" type="text/css" href="css/Skitia.css">
  <title>Mods</title>
<style>
  #mods-container{

     background-image: url("images/About.png");
 background-color:rgba(0,0,0,0.2);
    background-blend-mode: overlay;
    background-size:cover;
  background-repeat: repeat;
   

   
  }
  .right{
 
  
    width:25%;
    background-color:Green;
  }
    .left{
   float:right;
   width:25%;
      margin-top:50px;
      text-align:left;
      position:relative;
      margin-right:25%;
  }
.mySlides {display: none;
 
  }
  
  .message{
    display: none;
  background-color:#25255E;
 border-top: 1px solid aliceblue;
    margin-top: 10px;
    
  }
img {vertical-align: middle;
  }

/* Slideshow container */
.slideshow-container {
  max-width: 300px;
  border:1px solid Aliceblue;
  position: relative;
  margin: 0 auto;
}

/* Next & previous buttons */
.prev, .next {
  cursor: pointer;
  position: absolute;
  top: 50%;
  width: auto;
  padding: 16px;
  margin-top: -22px;
  color: white;
  font-weight: bold;
  font-size: 18px;
  transition: 0.6s ease;
  border-radius: 0 3px 3px 0;
  user-select: none;
}

/* Position the "next button" to the right */
.next {
  right: 0;
  border-radius: 3px 0 0 3px;
}

/* On hover, add a black background color with a little bit see-through */
.prev:hover, .next:hover {
  background-color: rgba(0,0,0,0.8);
}

/* Caption text */
.top-text {
  color: gold;
  font-size: 32px;
  padding: 8px 12px;
  position: absolute;
  top: 8px;
  width: 100%;
  text-align: center;
  font-family: 'Cinzel Decorative', cursive;
}
  .alignment-text{
       color: gold;
  font-size: 24px;
  padding: 8px 12px;
  position: absolute;
  bottom: 32px;
  width: 100%;
  text-align: center;
    font-family: 'Cinzel Decorative', cursive 
  }
  .bot-text{
     color: gold;
  font-size: 24px;
  padding: 8px 0px;
  position: absolute;
  bottom: 8px;
  width: 100%;
  text-align: center;
    font-family: 'Cinzel Decorative', cursive;
  }

/* Number text (1/3 etc) */


/* The dots/bullets/indicators */
.dot {
  cursor: pointer;
  height: 15px;
  width: 15px;
  margin: 50px 10px;
  background-color: silver;
  border-radius: 50%;
  display: inline-block;
  transition: background-color 0.6s ease;
}

.active, .dot:hover {
  background-color: gold;
}

/* Fading animation */
.fade {
  -webkit-animation-name: fade;
  -webkit-animation-duration: 1.5s;
  animation-name: fade;
  animation-duration: 1.5s;
}

@-webkit-keyframes fade {
  from {opacity: .4} 
  to {opacity: 1}
}

@keyframes fade {
  from {opacity: .4} 
  to {opacity: 1}
}
  #mods-container h1{
     text-align:Center;
      color:gold;
        font-family: 'Cinzel Decorative', cursive;
      font-size:48px;
       padding-top:40px;
      padding-bottom:40px;
     text-decoration:underline;
  }
  .mySlides::before {
    content: "";
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    /* Adjust the color values to achieve desired darkness */
    background: linear-gradient(to bottom, rgba(0, 0, 0, 0.75), rgba(0,0,0,0.1),rgba(0, 0, 0,0.75));
}
  .mod-quickinfo{
    padding: 16px 64px;
    font-size:18px;
     white-space: pre-wrap; 
    text-align:center
  }
 
/* On smaller screens, decrease text size */
@media only screen and (max-width: 300px) {
  .prev, .next,.text {font-size: 11px}
}
  @media screen and (min-width: 768px){
    .slideshow-container{
        margin: 0 0 0 5%;
      max-height:800px;
      
    }
    .message{
     width:360px;
      margin-right:5%;
      margin-left:5%;
      height:470px;
      max-height:470px;
     float:right;
      position:relative;
      bottom:615px;
      margin-bottom:-610px;
      border:1px solid aliceblue;
  }
     .quick-action{
    position:absolute;
    bottom:15px;
    
    width:100%;
  }
    
  }
   @media screen and (min-width: 996px){
  .slideshow-container{
        margin: 0 0 0 15%;
      
      
    }
    .message{
     width:380px;
      margin-right:15%;
      margin-left:15%;
   
     
  }
    
     
     
  }
     @media screen and (min-width: 1200px){
  .slideshow-container{
        margin: 0 0 0 20%;
     
      
    }
    .message{
     width:400px;
      margin-right:20%;
      margin-left:20%;
  }
    
     
     
  }
   @media screen and (min-width: 1500px){
     .slideshow-container{
        margin: 0 0 0 25%;
     
      
    }
    .message{
     width:420px;
      margin-right:25%;
      margin-left:25%;
  }
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
  <div id = "mods-container">
 
   <h1>
  Released 
  </h1>

 
<div class="slideshow-container">
<?php foreach ($results as $result){?>
<div class="mySlides fade">
<div class="top-text"><?php echo $result['name']; ?></div>
  <img src="images/<?php echo $result['name']?>.jpg" style="width:100%">
 
<div class="bot-text"><?php echo $result['race'] . ' ' . $result['class']; ?></div>
  
</div>
<?php } ?>

<a class="prev" onclick="plusSlides(-1)">&#10094;</a>
<a class="next" onclick="plusSlides(1)">&#10095;</a>

</div>
<br>

<div style="text-align:center">
<?php for ($i = 1;$i <= $count;$i++){ ?>
  <span class="dot" onclick="currentSlide(<?php echo $i; ?>)"></span> 
<?php } ?>
 
</div>   
  <?php foreach ($results as $result){?>  
  <div class = "message">
    
      <pre class = "mod-quickinfo">
  <?php echo $result['summary'];?>
    </pre> 
    
 <div class = "quick-action">

 <form method = "post" action="mods-view.php?name=<?php echo $result['name'];?>">
    <input type = "submit" class = "quick-button" name = "submit" value = "More Info">
    
          </form>
        <?php if( $result['download_2'] != '' && $result['download_1'] != ''){

  
 ?>
                            <button  class = "quick-button" onclick="openDownload()"  id = "downloadButton">
 Download
</button>
    
  <div id="viewD" class="downloadOverlay">
  <a href="javascript:void(0)" class="overlayCloseBtn" onclick="closeDownload()">&times;</a>
  <div class="mobileOverlay-content">
    <div id = "leftDownload" >
     <img src = "images/SoD.png">
      <br>
   <a download href = "<?php echo $result['download_1']; ?>"><button class = "quick-button" id = "downloadButton">
 Download BG:EE/SoD
     </button></a>
    </div>
 
    <div id = "rightDownload">
     <img src = "images/ToB.png">
      <br>
    <a download href = "<?php echo $result['download_2'];?>"><button class = "quick-button" id = "downloadButton">
 Download BG2:EE
</button></a>
    </div>
  </div>
  </div>
 <?php } elseif ( $result['download_1'] != ''){
   
 ?>
                              <a download href = "<?php echo $result['download_1']; ?>"><button  class = "quick-button" >
 Download
                                </button>      </a>
        
        <?php } else {
   
 ?>
         <button  class = "quick-button" disabled id = "disabled">
In Progress
</button>
        <?php } ?>
      
    </div>
  </div>
    <?php } ?>

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
           <a href="#" target = "_top">Mods </a> |
  <a href="news.php" target = "_top">News</a> |
             <a href="about.php" target = "_top">About </a> |
      <a href="https://forums.beamdog.com/profile/Skitia" target = "_top">Beamdog Forums </a> |
    <a href = "contact.php" target = "_top" >Contact </a> 
          </div>
  </footer>
    <script src="javascript/Skitia.js"></script>
<script>
  
  
var slideIndex = 1;
showSlides(slideIndex);

function plusSlides(n) {
  showSlides(slideIndex += n);
}

function currentSlide(n) {
  showSlides(slideIndex = n);
}

function showSlides(n) {
  var i;
  var slides = document.getElementsByClassName("mySlides");
  var dots = document.getElementsByClassName("dot");
  var message = document.getElementsByClassName("message");
  if (n > slides.length) {slideIndex = 1}    
  if (n < 1) {slideIndex = slides.length}
  for (i = 0; i < slides.length; i++) {
      slides[i].style.display = "none";  
    message[i].style.display = "none";  
  }
  for (i = 0; i < dots.length; i++) {
      dots[i].className = dots[i].className.replace(" active", "");
  }
  slides[slideIndex-1].style.display = "block";  
  message[slideIndex-1].style.display = "block";
  dots[slideIndex-1].className += " active";
}
</script>
  </body>
</html>