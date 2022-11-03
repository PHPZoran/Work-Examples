<?php 
require_once('../data-config.php');


if (isset($_POST['contactSubmit'])){
/* required files */
 require '../phpmailer/PHPMailerAutoload.php';

$inputSubject = $_POST['Subject'];
$inputEmail = $_POST['Email'];
$inputName = $_POST['Name'];
  $inputMessage = $_POST['Message'];
$mail = new PHPMailer;

try {
  $mail->Host='smtp.gmail.com';
$mail->Port=587;
$mail->SMTPAuth=true;
$mail->SMTPSecure='tls';
   $mail->From = "skitiatheskittles@gmail.com";
   $mail->FromName = "Skitia's Stories";
   $mail->addAddress("skitiatheskittles@gmail.com", 'Skitia');
   $mail->Subject = $inputSubject;
  $mail->IsHTML(true);
   $mail->Body = "<p><span style = 'font-weight: bold';>Name</span>: " . $inputName . "</p>" . 
     "<p><span style = 'font-weight: bold';>Email</span>: " . $inputEmail . "</p>" .
     "<p><span style = 'font-weight: bold';>Subject</span>: " . $inputSubject . "</p>" .
     "<p><span style = 'font-weight: bold';>Message</span>: " . $inputMessage . "</p>" . 
     "<p> (Sent via <a href = 'https://skitias-stories.com/' style = 'font-style: italic; text-decoration: underline;'>Skitia's Stories</a>)</p>";
   


/* Username (email address). */
$mail->Username = 'skitiatheskittles@gmail.com';

/* Google account password. */
$mail->Password = 'Vulpesune2!';
   
   /* Finally send the mail. */
   $mail->send();
}
catch (Exception $e)
{
   echo $e->errorMessage();
}
catch (\Exception $e)
{
   echo $e->getMessage();
}
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Contact</title>
  
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
    
<?php
if (!isset($_POST['contactSubmit'])){
  ?>


        

      <form method="post" id = "centerContact">
                     
     <h1 id = "contactHead">
       Contact
      </h1> 
            <p id = "contactText">
        Please enter your contact details with your inquiry.
      </p>
    
        Name
    <br><input type = "text" class = "contactInput" placeholder = "Name (Required)" required name = "Name"> <br>        

        Email
     <br> <input type = "email" class = "contactInput" placeholder = "Email (Required)" required pattern="[^@\s]+@[^@\s]+" name = "Email"> <br>

       Subject
 <br> <input type = "text" class = "contactInput" placeholder = "Subject (Required)" required name = "Subject"><br>

Message<br><textarea placeholder="Message (Required)" name="Message" id = "contactMessage"  rows="5" cols="33" required minlength=20></textarea>
<br>
 
        <input type = "submit" value = "Submit" id = "contactSubmit" name = "contactSubmit"></input>
            </form>
<?php 
}
  else {
    
  ?>

             
         
      <form action = "" id = "centerContact">
                         
     <h1 id = "contactHead">
       Contact
      </h1> 
            <p id = "contactText">
        Your message has been sent! You will receive a reply within 24 hours.
      </p>

        Name
    <br><input type = "text" class = "contactInput" value = "<?php echo $inputName;?>" disabled  > <br>        

        Email
     <br> <input type = "email" class = "contactInput"  value = "<?php echo $inputEmail;?>" disabled> <br>

       Subject
 <br> <input type = "text" class = "contactInput" placeholder = "Required" value = "<?php echo $inputSubject;?>" disabled><br>

        Message<br><textarea name="Message" id = "contactMessage"  rows="5" cols="33" disabled ><?php echo $inputMessage;?></textarea>
<br>
 
        <input type = "submit" value = "Go Back" id = "contactSubmit" name = "contactSubmit" >
            </form>

<?php 
  }
        ?>
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
    <a href = "#" target = "_top" >Contact </a> 
          </div>
  </footer>

     
</body>
</html>
