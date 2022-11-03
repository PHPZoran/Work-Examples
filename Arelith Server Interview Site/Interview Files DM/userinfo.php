<?php
echo
"    <div class=\"userinfo\">

        <strong>User:</strong>" . $userName . "<strong>Role:</strong>" . $group . "   <a href=\"index.php\">        
        <button class=\"logout\"><span>Home</span></button></a>     
        <form action=\"loginhandler.php\" method=\"GET\">
          <button class=\"logout\" type=\"submit\" name=\"logout\" value=\"1\"><span>Logout</span></button>
        </form>
       
    </div>";
?>