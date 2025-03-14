<script type="text/javascript">
  $(document).ready(function(){  
      $('#quest').keyup(function(){  
           var query = $(this).val();  
           if(query != '')  
           {  
                $.ajax({  
                     url:"global.header.section.quick.search.php",  
                     method:"POST",  
                     data:{query:query},  
                     success:function(data)  
                     {  
                          $('#questList').fadeIn();  
                          $('#questList').html(data);  
                     }  
                });  
           }  
      });  
      $(document).on('click', 'li', function(){  
           $('#quest').val($(this).text());  
           $('#questList').fadeOut();  
      });  
 });
</script>
<?php
$conn->close();
?>
<script async src="https://www.googletagmanager.com/gtag/js?id=UA-61260799-1"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'UA-61260799-1');
</script>