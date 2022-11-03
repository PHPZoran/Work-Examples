function openDownload() {
 
  document.getElementById("viewD").style.width = "100%";
  document.getElementById("leftDownload").style.display = "block";
  document.getElementById("rightDownload").style.display = "block";
  
}
  function closeDownload() {

  
  document.getElementById("viewD").style.width = "0%";
   document.getElementById("leftDownload").style.display = "none";
  document.getElementById("rightDownload").style.display = "none";
} 
//Get the button
var mybutton = document.getElementById("scrollBtn");



// When the user scrolls down 20px from the top of the document, show the button
window.onscroll = function() {scrollFunction()};

function scrollFunction() {
 
  if (document.body.scrollTop > 40 || document.documentElement.scrollTop > 40) {
    mybutton.style.display = "block";
  } else {
    mybutton.style.display = "none";
  }
}

// When the user clicks on the button, scroll to the top of the document
function topFunction() {
  document.body.scrollTop = 0;
  document.documentElement.scrollTop = 0;
}
function openNav() {
  document.getElementById("mobileNav").style.width = "100%";
}

function closeNav() {
  document.getElementById("mobileNav").style.width = "0%";
}


