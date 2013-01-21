
<style type="text/css">
	.layout_fc_daily {
            margin-top:0px;
		padding: 0px;
		background-color: #fff;
		margin-bottom: 10px;
                -webkit-border-radius: 6px;
                -moz-border-radius: 6px;
                border-radius: 6px;
                height:auto;
                
	}

        .fc_daily_form{
            
            background:#fff;
            padding:2%;
            width:100%;
            color:#990000;
            }
            
         .fc_daily_form table{
            
             border-spacing:2px;
                 width:96%;
         }  
         
         .fc_daily_form tr{
             
             margin-bottom:10px;

         }  
         
         
         .fc_daily_form tr:nth-child(even){
             
             background:#ccc;
             margin-bottom:10px;

         }  
         
         .fc_daily_form tr:nth-child(odd){
             
             background:#efefef;
             margin-bottom:10px;

         } 
         
         
         
         td.first, td.second{
             
             padding:7px;
             
         }
	 #click4info:hover{cursor:pointer}
         
         #betValInfo{display:none;font-size:11px;padding:7px;margin-bottom:10px;width:auto;margin-right:50px;background:#efefef}
         
         td.first{ width:79% }
         
         td.second{ width:20% }
         
         #send_data{ margin-top:5px;margin-bottom:10px;width:15%;margin-left:80% }

</style>
<script type="text/javascript" src="/fc/custom/js/jquery.js"></script>
<script type="text/javascript">

//jQuery.noConflict();
 
 jQuery(document).ready(function(){
	 
	 //alert('All gut?');
	 
	  /*
	 if(jQuery('.inner')){
	 
		jQuery('input.radio[type="radio"]').attr('disabled','disabled');
	 
	 }  else {
	        jQuery('input.radio[type="radio"]').removeAttr('disabled');       
	 }
	 */
	 
	 jQuery('#click4info').click(function(){
	 
	        jQuery('#betValInfo').fadeToggle();
		return false;
	 });
	 
	 jQuery('#send_data').click(function(){
                 		 
		 var choicesArray = [];
		 var fdata = jQuery('#formData').val();
		 var hidVal = jQuery('#hidVal').val();
		 
		 var checkedValue = jQuery('input.radio[type="radio"]:checked');
                 //alert(checkedValue.length);

		checkedValue.each(function(){
		        
			choicesArray.push(jQuery(this).val());
		
		});
		
		//alert(choicesArray);
		//alert(fdata);
		//alert(hidVal);
		 
		 if(choicesArray != ''){
		 jQuery.ajax({	 
		 
			url:       'http://www.flutterclub.com/fc/compHandle.php',
                        type:     'POST',
                        dataType:'text',
			data: {  
			          choicesSubmit: choicesArray, 
			          hiddenValue:    hidVal,
                                  fdata:           fdata
				},
			//traditional: true,
			success: function(response){
			   alert(response);
			},
			error: function(){
			   alert('Could not submit, sorry!');
			}

		 
		 });
		 } else { alert('Sorry, you need to make choice before submission.'); }  
		 
		 return false;
		 
		 });

	 });

</script>

<p id="log_box"></p>

<form class="fc_daily_form" method="POST">
    <?php
    
/*  SETTING UP TIME COUNTER FOR THE COMPETITION ------ BEGIN  */    
    
    $timestamp = time();
            //echo $timestamp;
            //exit("Sample time taken.");
            $file = "/var/www/fc_live/fc/tstamp.txt";
            $fopen = fopen($file, "r");
            $fread = fread($fopen, filesize($file));
            $tmstamp_ahead = $fread + 3600 * 24 * 3;
	    //$tmstamp_ahead = $fread + 600;
            
            if($tmstamp_ahead <= $timestamp){
	    
	        echo "<strong>Competition suspended.</strong> (wait for results)<br/>";
		return false;
                //exit("<strong>Competition ended. </strong><br/><br/>");
            } else { echo "<strong>Competiton continues.</strong> <br/><br/>"; }
            
            fclose($fopen);
	    
   /*  SETTING UP TIME COUNTER FOR THE COMPETITION -------- END  */     

/*  CHECKING IF RECORD EXISTS ------ BEGIN  */  

    $user_id = Engine_Api::_()->user()->getViewer()->getIdentity();
    
    $pre_dbc = mysql_connect('localhost','fc_admin','_d]M,zZ92u]0') or die('Could not connect to server');
         
         //mysql_select_db('fc_demo');
         $pre_check = "SELECT count(id) FROM fc_live.engine4_user_daily WHERE user_id=$user_id";
         //mysql_query ONLY allows one query at a time!!!!
         
         $pre_select = mysql_query($pre_check, $pre_dbc);
         $pre_check_result = mysql_result($pre_select,0,0);
	 
	 if( $pre_check_result != 0 ){
	 
		$display_check = "SELECT bet_value FROM fc_live.engine4_user_daily WHERE user_id=$user_id";
         
         
               $display_select = mysql_query($display_check, $pre_dbc);
               //$display_check_result = mysql_fetch_array($display_select);
	       echo '<a id="click4info"><strong>You already submitted choices - click to view them </strong><em> (all in respective order)</em></a><br/><br/>';
	       echo '<p id="betValInfo">';
	       while($row = mysql_fetch_array($display_select)){
			
			echo  '---' . $row['bet_value'] . '---';
		}
	        echo '</p>';
		
	 }
    /*  CHECKING IF RECORD EXISTS ------ END  */     
    
    $template = "/var/www/fc_live/fc/application/modules/User/widgets/comp-daily/current.txt";
     $fileOpen = fopen($template, "r");
                
     $fileContents = fread($fileOpen, filesize($template));
            
       $formData = explode("|", $fileContents);
       
       //print_r($formData);
             
       $howMany = count($formData);
       
       // echo $howMany;

       echo "<table>";
           
           for($i = 0; $i < $howMany; $i++){
         
             $cells = explode(",", $formData[$i]);
	     
	     $cell_count = count($cells);
	     //echo $cell_count;
             
      echo '<tr>';
      echo '<td class="first"><strong>' . $cells[0] . '</strong></td>';
                
      echo '<td class="second">' 
       
          . $cells[1] . '<input type="radio" class="radio" name="' . $i . '" value="' . $cells[1]. '" /><br/>' 
          . $cells[2] . '<input type="radio" class="radio" name="' . $i . '" value="' . $cells[2] . '"/><br/>' 
          . $cells[3] . '<input type="radio" class="radio" name="' . $i . '" value="' . $cells[3] . '"/>';
	  
       echo '</td>';
      echo '</tr>';
             
          }
     
      echo '</table>';

      fclose($fileOpen) or die("Sorry");
      
      //$user_id = Engine_Api::_()->user()->getViewer()->getIdentity();
      echo '<input type="hidden" id="hidVal" name="userId" value=' . $user_id . ' />';
     echo '<input type="hidden" id="formData" name="fdata" value=' . $howMany . ' />';

      

    ?>
 <button id="send_data" name="submit">OK</button>
</form>

