<script type="text/javascript">
    //var form = document.getElementById("form");
    //var confirm = document.getDocumentById("uid");
    //alert("ready?");
     window.onload = function(){
            document.form.submit();
        }
        
    
</script>

<?php

   $user_id = Engine_Api::_()->user()->getViewer()->getIdentity();


//echo $user_id;
   echo '<p style="color:#fff;margin-left:20px">Please wait. The game is loading...</p>';
   
  

   echo '<form name="form" id="form" method="post" action="/fc/custom/blackjack/main.php">';
   echo '<input type="hidden" id="uid" name="userId" value=' . $user_id . ' />';
  
   echo '</form>';
  
?>

