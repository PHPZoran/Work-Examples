
<!DOCTYPE html>
<html>
<head>
  <title>DM Interviews :: Login</title>
  <!--<meta name="viewport" content="width=device-width, initial-scale=1.0">-->
  <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1">
  <link href="https://support.nwnarelith.com/css/loginhandler.css" type= "text/css" rel="stylesheet" />
  <style>
  @font-face {font-family: Dalelands; src: url('https://support.nwnarelith.com/css/Dalelands%20Uncial.otf');}
  </style>
</head>
<body>
  

<div id="login-form" class ="login-form">
  <div class = "title">
    <h1><p>Arelith <br></h1><h2 id="safarihater"><span style="font-family: Dalelands;color:white">DM Interviews</span></h2></p>
  </div>

  <form action="loginhandler.php" method="POST">
        <p class="login-text">
          <label class = 'login-usernamelabel' for="username"><strong>Username</strong> </label>&nbsp;
          </p>
          <input type="text" name="username" id="username" size="10" title="Username" class="login-username" required="true" placeholder="Username"/>
          <p class ="login-text">
          <label class = 'login-passwordlabel' for="password"><strong>Password</strong></label>&nbsp;
        </p>
          <input type="password" name="password" id="password" size="10" title="Password" class="login-password" required="true" placeholder="Password"/>
          <!--<label for="autologin">Log me on automatically each visit <input type="checkbox" name="autologin" id="autologin" /></label>-->
          <input type="submit" name="login" value="Login" class="login-submit" />
          <!--Hidden type will save refURL so on form submission so it will be saved-->
          <input type="hidden" name="refUrl" value="index.php">
  </form>
  <div class ="error">
    <h4><p><strong></strong></p></h4>
  </div>
</div>
<!--<form action="loginhandler.php?logout=1" method ="GET">
  <input type="submit" name="logout" value="Logout" />
</form>-->
<div class="underlay-photo"></div>
<div class="underlay-black"></div>

<footer>
</footer>
</body>
</html>