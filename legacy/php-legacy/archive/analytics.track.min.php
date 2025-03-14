<script type="text/javascript" async="async">
$(document).ready(function(){$("#quest").keyup(function(){var t=$(this).val();""!=t&&$.ajax({url:"global.header.section.quick.search.php",method:"POST",data:{query:t},success:function(t){$("#questList").fadeIn(),$("#questList").html(t)}})}),$(document).on("click","li",function(){$("#quest").val($(this).text()),$("#questList").fadeOut()})});
</script>
<?php
$conn->close();
?>
<script src="https://www.googletagmanager.com/gtag/js?id=UA-61260799-1" async="async"></script>
<script type="text/javascript" async="async">
function gtag(){dataLayer.push(arguments)}window.dataLayer=window.dataLayer||[],gtag("js",new Date),gtag("config","UA-61260799-1");
</script>