 
<?php require_once("data-config.php");
if(isset($_POST['lsubmit'])){
$username     = $_POST['name'];
$password     = $_POST['password'];
$login_query = "Select dm, password, dm_id FROM int_dm where dm = ? LIMIT 1";
	$stmt = $con->prepare($login_query);
	$stmt->bind_param('s', $username);	
	$stmt->execute();
	$result = $stmt->get_result();
$criteria = mysqli_fetch_array($result);
	if(password_verify($password,$criteria['password']) && $_POST['name'] != 'Sauron')
	{
			session_start();
			$_SESSION["DM"]=$criteria['dm'];
      $_SESSION["ID"]=$criteria['dm_id'];
			header('Location: index.php');
	}
  elseif($_POST['name']== 'Sauron'){
    $error = 'Sorry, Sauron. You must appeal to the Angels for entry.';
  }
	else{
			$error = 'Incorrect Password. Please try again or reach out to the admins.';
	}
}
if(isset($_GET['logout'])){ 
  session_start();
 unset($_SESSION["DM"]);
  unset($_SESSION["ID"]);
  header('Location: dm_login.php');
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login</title>
 
<link rel="stylesheet" type="text/css" href="css/interview.css">
 <link href="https://fonts.googleapis.com/css?family=Cinzel+Decorative|Lato&display=swap" rel="stylesheet">
<link href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css" rel="stylesheet"/>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>


  <script>
 $( function() {
    $.widget( "custom.iconselectmenu", $.ui.selectmenu, {
      _renderItem: function( ul, item ) {
        var li = $( "<li>" ),
          wrapper = $( "<div>", { text: item.label } );
 
        if ( item.disabled ) {
          li.addClass( "ui-state-disabled" );
        }
 
        $( "<span>", {
          style: item.element.attr( "data-style" ),
          "class": "ui-icon " + item.element.attr( "data-class" )
        })
          .appendTo( wrapper );
 
        return li.append( wrapper ).appendTo( ul );
      }
    });
 
    $( "#name" )
      .iconselectmenu()
      .iconselectmenu( "menuWidget" )
        .addClass( "ui-menu-icons customicons" );

  } );
  </script>
  <style>

 
    /* select with custom icons */
    .ui-selectmenu-menu .ui-menu.customicons .ui-menu-item-wrapper {
      padding: 0.5em 0 0.5em 3em;
    }
    .ui-selectmenu-menu .ui-menu.customicons .ui-menu-item .ui-icon {
      height: 30px;
      width: 35px;
      top: 0.1em;
    }
    .ui-icon.devil {
      background: url("css/Images/devil.png") no-repeat;
    }
    .ui-icon.angel {
      background: url("css/Images/Angel.png") 0 0 no-repeat;
    }
    .ui-icon.neutral {
       background: url("css/Images/star.png") 0 0 no-repeat;
    }

    #red{
      color:orange;
    }
  </style>
  </head>

<body style="background:url('css/Images/Cloud.gif');">

<div id = contactBackground>
    



        

      <form method="post" id = "centerContact">
                     
     <h1 id = "contactHead">
      DM Interviews Login
      </h1>     
      <label for="name">DM Name</label><br>
    <br><select name="name" id="name" >
   
 
  <option value="DM Avalon Soul" data-class="angel">DM Avalon Soul</option>
        <option value="DM Dionysus" data-class="angel">DM Dionysus</option>
  <option value="DM Hoodoo" data-class="devil">DM Hoodoo</option>
          <option value="DM MoonMoon" data-class="angel">DM MoonMoon</option>
          <option value="DM Rex" data-class="neutral">DM Rex</option>
     <option value="DM Starfish"data-class="neutral" >DM Starfish</option>     
  <option value="DM Straw Hat" data-class="neutral">DM Straw Hat</option>
  <option value="DM Wraith" data-class="devil">DM Wraith</option>  
        <option value="DM Wake" data-class="neutral">DM Wake</option>
        <option value="DM Zinzerena" data-class="neutral">DM Zinzerena</option>
       <option value="Grumpycat" data-class="angel">Grumpycat</option>   
         <option value="Irongron" data-class="angel">Irongron</option>   
     <option value="Queen Titania" data-class="angel">Queen Titania</option>   
        <option value="Sauron" data-class="devil">Sauron</option>      
        <option value="Spyre" data-class="devil">Spyre</option>   
</select><br>        


    <br> <label for="name">Interview Password</label><br>
        <?php if(isset($_POST['lsubmit'])){
 echo "<br>" . "<p id = 'red'>" . $error . "</p>";
} ?>
  <input type = "password" class = "contactInput" placeholder = "Interview Password" required name = "password"><br>


 
        <input type = "submit" value = "Submit" id = "contactSubmit" name = "lsubmit"></input>
            </form>


             
   


  </div>
 

     
</body>
</html>
