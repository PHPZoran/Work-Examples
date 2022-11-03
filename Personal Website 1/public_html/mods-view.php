<?php 
require_once('../data-config.php');
if(!isset($_GET['name'])){
header('Location: mods.php');
}
$name = FILTER_VAR($_GET['name'], FILTER_SANITIZE_STRING);
$sql = "SELECT * FROM characters where name = '$name'";
$results = mysqli_query($con, $sql);
$check= mysqli_num_rows($results); 
if($check == 0){
  header('location: mods.php');
}
foreach ($results as $result){

  $alignment = $result['alignment'];
$summary = $result['summary'];
  
$str = $result['strength']; 
  
$dex = $result['dexterity'];
  
$cons = $result['constitution'];
  
$int = $result['intelligence'];
$wis = $result['wisdom'];
$cha = $result['charisma'];  
$biography = $result['biography'];
$overview = $result['overview'];
$kit = $result['kit'];

$approval = $result['approval'];
$details = $result['details'];
$download1 = $result['download_1'];  
$download2 = $result['download_2'];  
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" name="viewport" content="width=device-width, initial-scale=1.0">
 <link href="https://fonts.googleapis.com/css?family=Cinzel+Decorative|Lato&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.1.0/css/all.css">
  <!--  the chart can't be thrown into the file.  -->
 
  <link rel="stylesheet" type="text/css" href="css/Skitia.css">
  <title>Mods</title>
<style>
  .mod-article{
    background-color:#25255E;
  }
 
  #mods-container{
     background-image: url("images/About.png");
 background-color:rgba(0,0,0,0.2);
    background-blend-mode: overlay;
    background-size:cover;
  
  border-bottom: 2px solid gold;

   
  }
  .mod-view-container{
    margin: 0 auto;
    text-align:center;
  }
 
.mySlides {display: none;
 
  }
  
  .message{
    display: none;
  background-color:grey;
    
  }
#modImage {vertical-align: middle;
  width: 240px;

  border: 4px solid #896748;
    filter:drop-shadow(8px 8px 12px #25255E);
  margin-top:5px;
  }
  .mod-info pre{
    
    padding:15px;
  
    white-space:pre-wrap;
    font-size:16;
     font-family: "Lato", sans-serif;
    line-height:24px;
  }
  .mod-info pre a{
    color:orange;
    text-decoration:none;
  }
  .mod-info pre a:hover{
    color:silver;
  }
  .mod-audio{
    text-align:center;
    
  }
  .mod-audio h1, h2, audio{
    padding-bottom:10px;
  }
 

/* Slideshow container */

  
  #chart_div{
    width:400px;
    margin:-50px auto 0 auto;
   height:280px;
  }


  h1{
     text-align:Center;
      color:gold;
        font-family: 'Cinzel Decorative', cursive;
      font-size:48px;
       padding-top:20px;
     
     text-decoration:underline;
  }
  h2{
      text-align:Center;
      color:gold;
        font-family: 'Cinzel Decorative', cursive;
      font-size:24px;
       padding-top:40px;
     
    
  }

@media screen and (min-width: 768px) {
  
  #mods-container{
      height:540px;
      position:relative;
    overflow:hidden;
    width:100%;
  }
  
    
 .mod-split {
  height: 100%;
  width: 50%;
  position: absolute;
  z-index: 1;
  height:485px;
 
  padding-top: 20px;
}
  
  .mod-view-container{
  left:0;
    
  }
   .mod-view-container h1{
    position:absolute;  
      top:0%;
     left:55%;
     
    }
    .mod-view-container img{
      position:absolute;
      top:15%;
      right:13%;
    }
  #chart-container{
    right:0;
    
  }
  #chart-container h2{
     position:absolute;
       top: 10%;
  left: 52%;
  transform: translate(-50%, -50%);
    white-space: nowrap;
  
  }
  #chart_div{
    position: absolute;
  top: 48%;
  left: 52%;
  transform: translate(-50%, -50%);
  text-align: center;
    
  }
  .quick-action{
     position: absolute;
  top: 78%;
  left: 51%;
  transform: translate(-50%, -50%);
  text-align: center;
    text-decoration:none;
  }
  .quick-button{
    display:block;
    margin:0 auto 12px auto;
    width:200px;
    text-decoration:none;
  
  }
  .quick-action a{
    text-decoration:none !important;
  }
  }
  @media screen and (min-width: 996px) {
    .mod-info pre{
    
    padding:15px 10%;
      margin: 0 200px;
    }
    .mod-view-container h1{
  

    left:60%;
     
    }
    .mod-view-container img{

      right:20%;
     
    }
    #chart-container h2{
      left:35%;
   
    }
  #chart_div{

  left: 35%;   
  }
  .quick-action{

  top: 78%;
  left: 20%;
  transform: translate(-50%, -50%);
  text-align:Center;
   
  }
  .quick-button{
   
    margin:0 25% 5% 25%;
    width:500px;
    
  overflow:hidden;
  }
   
     @media screen and (min-width: 1200px) {
         #chart-container h2{
      left:35%;
           top:20%;
    }
  #chart_div{
 top:52%;
  left: 25%;

 
    
  }
 .quick-action{
 
  top: 80%;
  left: 40%;
  transform: translate(-50%, -50%);
  width:500px;
   
  }
       
       .quick-button{
          margin:0 25% 2.5% 25%;
    display:inline;
    
    width:700
    
  
  }
  }
    @media screen and (min-width: 1500px){
 .quick-action{

  top: 80%;
  left: 35%;
  transform: translate(-50%, -50%);
  
   
  }
       .mod-view-container h1{
  

    left:58%;
     text-align:center;
    }
    }
  
  </style>
     <script type="text/javascript">

    </script>
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
  
  <div id="viewD" class="downloadOverlay">
  <a href="javascript:void(0)" class="overlayCloseBtn" onclick="closeDownload()">&times;</a>
  <div class="mobileOverlay-content">
    <div id = "leftDownload" >
     <img src = "images/SoD.png">
      <br>
   <a download href = "<?php echo $download1;?>"><button class = "quick-button" id = "downloadButton">
 Download BG:EE/SoD
     </button></a>
    </div>
 
    <div id = "rightDownload">
     <img src = "images/ToB.png">
      <br>
    <a download href = "<?php echo $download2;?>"><button class = "quick-button" id = "downloadButton">
 Download BG2:EE
</button></a>
    </div>
  </div>
  </div>
  <div id = "mods-container">
 
   

 
<div class="mod-view-container mod-split">
<h1>
  <?php echo $name; ?>
  </h1>


  <img id = "modImage" src="images/<?php echo $name; ?>.jpg" alt = "<?php echo $name; ?>" >
 
  



</div>

     
   
     <div id = "chart-container" class = "mod-split">
     <h2>
  <?php echo $alignment; ?>
  </h2>
        <div id="chart_div"></div>

      <div class = "quick-action">
        <?php if( $download2 != '' && $download1 != ''){

  
 ?>
                            <button  class = "quick-button" onclick="openDownload()"  id = "downloadButton">
 Download
</button>
 <?php } elseif ( $download1 != ''){
   
 ?>
                              <a download href = "<?php echo $download1; ?>"><button  class = "quick-button" >
 Download
                                </button>      </a>
        
        <?php } else {
   
 ?>
         <button  class = "quick-button" disabled id = "disabled">
In Progress
</button>
        <?php } ?>
                                <button class = "quick-button" id = "modSecondButton" onclick="window.open('https://forums.beamdog.com/discussion/74701/v1-3-vienxay-a-elven-shadowdancer-mage-npc-for-bg-ee-sod#latestm','_blank');">
 Support Forum
</button>
         

          </div>
       </div>

   

</div>
  
  
 
    <button onclick="topFunction()" id="scrollBtn" title="Go to top"><i class="fas fa-angle-double-up"></i></button>
  <article class = "mod-article">
 <div class = "mod-info">
   <h1>
   Biography 
    </h1>
   <pre id = "mod-bio">
   <?php echo $biography;?>
   </pre>
    </div>
  <div class = "mod-info">
   <h1>
   Overview
    </h1>
   <pre>
   <?php echo $overview;?>
   </pre>
    </div>  
      <div class = "mod-info">
   <h1>
   Kit
    </h1>
   <pre>
   <?php echo $kit;?>
   </pre>
    </div>  
      <div class = "mod-info">
   <h1>
   Disposition &amp; Approval
        </h1>
   <pre>
   <?php echo $approval;?>
   </pre>
    </div>  
   <div class = "mod-info">
   <h1>
   Other Details
        </h1>
   <pre>
   <?php echo $details;?>
   </pre>
    </div>     
     <div class = "mod-audio">
   <h1>
   Audio &amp; Music
        </h1>
       <h2>
         Voice Sample
       </h2>
  <audio controls>
  <source src="audio/voice-<?php echo $name;?>.ogg" type="audio/ogg">
  <source src="audio/voice-<?php echo $name;?>.mp3" type="audio/mpeg">
Your browser does not support the audio element.
</audio>
       <h2>
         Music 
       </h2>
        <audio controls>
  <source src="audio/music-<?php echo $name;?>.ogg" type="audio/ogg">
  <source src="audio/music-<?php echo $name;?>.mp3" type="audio/mpeg">
Your browser does not support the audio element.
</audio>
    </div>     
    </article>
   <footer>
       <h1 id = "modFootHead">
         <a href="mods.php" target = "_top">Mods</a>
     </h1>
     <div class =  "modFoot">
  <?php 
     $c = 1;
       $cql = "SELECT * FROM characters where name != '$name'";
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
     <h1 id = "footHead"><a href="index.html" target = "_top">§<span class = "logoUnderline">kitia's</span> §<span class = "logoUnderline">tories</span>   </a></h1>
     
 
    <div id = "footContent">
  <a href="news.php" target = "_top">News</a> |
             <a href="about.php" target = "_top">About </a> |
      <a href="https://forums.beamdog.com/profile/Skitia" target = "_top">Beamdog Forums </a> |
    <a href = "contact.php" target = "_top" >Contact </a> 
    
          </div>

  </footer>
      <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
  <script src="javascript/Skitia.js"></script>
<script>

      // Load the Visualization API and the corechart package.
      google.charts.load('current', {'packages':['corechart']});

      // Set a callback to run when the Google Visualization API is loaded.
      google.charts.setOnLoadCallback(drawChart);

      // Callback that creates and populates a data table,
      // instantiates the pie chart, passes in the data and
      // draws it.
      function drawChart() {
var screenLength = window.outerWidth;
        if (screenLength >= 1200)
          {
            x = 600;
          }
        else {
          x = 400;
        }
        // Create the data table.
        var data = google.visualization.arrayToDataTable([
          ['ability','score', { role: 'style' }],
          ['Strength', <?php echo $str; ?>, '#25255E'],
          ['Dexterity', <?php echo $dex; ?>, '#000040'],
          ['Constitution', <?php echo $cons; ?>, '#25255E'],
          ['Intelligence', <?php echo $int; ?>, '#000040'],
          ['Wisdom', <?php echo $wis; ?>, '#25255E'],
          ['Charisma',<?php echo $cha; ?>, '#000040'],
        ]);

        // Set chart options
        var options = {
                       'width':x,
                       'height':300,
          bar: {
    groupWidth: '100%',
    
                  
},
                animation:{
        duration: 4000,
        easing: 'out',
                      startup: true,
      },
          
          hAxis: {
              viewWindow: {
        min: 5,
        max: 20,},
            textStyle : {
            fontSize: 14,
              color: 'aliceblue',
              fontName: 'Lato',
       
        },
    gridlines: {
        color: 'black',
      count:5
    },
            
},
               vAxis:
          {
            textStyle : {
              fontSize: 12,
                color: 'aliceblue',
              fontName: 'Lato'
            },
          },
          backgroundColor: { fill:'transparent' },
          legend: {position: 'none'}
                      };

        // Instantiate and draw our chart, passing in some options.
        var chart = new google.visualization.BarChart(document.getElementById('chart_div'));
        chart.draw(data, options);
      }
</script>
  </body>
</html>