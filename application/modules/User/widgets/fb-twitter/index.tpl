<style type="text/css">
    #button_wrapper{
	background:#efefef url('/fc/custom/images/fb_tw.png') no-repeat center right;margin-top:20px;padding-left:7px;padding-top:5px;
	-webkit-border-radius: 6px;
	-moz-border-radius: 6px;
	border-radius: 6px;
	-moz-box-shadow: 2px 2px 3px #888 inset;
        -webkit-box-shadow: 2px 2px 3px #888 inset;
        box-shadow: 2px 2px 3px #888 inset;
	height:42px;
    }
    
    #fb{margin-top:5px}
    #twtr{margin-top:-27px;height:22px}
</style>

<div id="button_wrapper">


<?php

	echo $this->fb_tw_buttons;
	
?>

</div>
