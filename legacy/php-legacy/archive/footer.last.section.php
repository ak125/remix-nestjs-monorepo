heelooo


<script>
function openNav() {
    document.getElementById("mySidenav").style.width = "250px";
    document.getElementById("mainPageContent").style.marginLeft = "180px";
    document.getElementById("mainPageContentCover").style.display= "block";
}

function closeNav() {
    document.getElementById("mySidenav").style.width = "0";
    document.getElementById("mainPageContent").style.marginLeft= "0";
    document.getElementById("mainPageContentCover").style.display= "none";
}
$(document).keypress(
    function(event){
     if (event.which == '13') {
        event.preventDefault();
      }


});
</script>