function idleTimer() {
    var t;
    var clock;
    window.onload = resetTimer;
    window.onmousemove = resetTimer; // catches mouse movements
    window.onmousedown = resetTimer; // catches mouse movements
    window.onclick = resetTimer;     // catches mouse clicks
    window.onscroll = resetTimer;    // catches scrolling
    window.onkeypress = resetTimer;  //catches keyboard actions

    function redirect() {
      var path = window.location.pathname;
      var url = path.substring(path.lastIndexOf('/')+1);
      if(url){
      window.location.href = 'loginhandler.php?logout=1&timeout=1&refUrl='+url;  //Adapt to actual logout script
      }else{
      window.location.href = 'loginhandler.php?logout=1&timeout=1';
    }
    }

   function reload() {
          window.location = self.location.href;  //Reloads the current page
   }

   function resetTimer() {

        clearTimeout(t);
        //t = setTimeout(logout, 5000);  // time is in milliseconds (1000 is 1 second)
        t= setTimeout(redirect, 1500000);  // time is in milliseconds (1000 is 1 second). Set to 25 minutes
         let display = document.querySelector('#timer');
         let duration = 60 * 25;
         var timer = duration, minutes, seconds;
         clearInterval(clock);
         clock = setInterval(function () {
             minutes = parseInt(timer / 60, 10)
             seconds = parseInt(timer % 60, 10);

             minutes = minutes < 10 ? "0" + minutes : minutes;
             seconds = seconds < 10 ? "0" + seconds : seconds;

             display.textContent = minutes + ":" + seconds;

             if (--timer < 0) {
                 timer = duration;
             }
         }, 1000);
        //startTimer(60 * 25, display);
    }

    function startTimer(duration, display) {
        var timer = duration, minutes, seconds;
        setInterval(function () {
            minutes = parseInt(timer / 60, 10)
            seconds = parseInt(timer % 60, 10);

            minutes = minutes < 10 ? "0" + minutes : minutes;
            seconds = seconds < 10 ? "0" + seconds : seconds;

            display.textContent = minutes + ":" + seconds;

            if (--timer < 0) {
                timer = duration;
            }
        }, 1000);
    }
}



idleTimer();
