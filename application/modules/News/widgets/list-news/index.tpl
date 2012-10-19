
<script type="text/javascript">
  var pageAction =function(page){
   //   alert(page);
    //$('nextpage').value = page;
    //$('gotoPage').submit();
    document.getElementById('nextpage').value = page;
    document.getElementById('gotoPage').submit();
  }
  
</script>
<div class='layout_middle'>
<form name="gotoPage" id="gotoPage" method="post">
  <input type="hidden" name="nextpage" id="nextpage">
  <span style="display:none;">
    <input type="text" name="category" id="category" value="<?php if(isset($_SESSION['keysearch'])) echo $_SESSION['keysearch']['category'];?>" />
    <input type="text" name="search" id="search" value="<?php if(isset($_SESSION['keysearch'])) echo $_SESSION['keysearch']['keyword'];?>" />
  </span>
	<div style="overflow: hidden; margin-top: -10px;">	    
	  <?php if( $this->paginator->getTotalItemCount() > 0 ): ?>
	    <ul class="blogs_browse">
	      <?php foreach( $this->paginator as $item ): ?>      	
	        <li style="clear: both;padding-top:10px;">          
	          <div class='blogs_browse_info'>
	            <p class='blogs_browse_info_title'>
                
                 <h3><?php echo $this->htmlLink($item->getHref(),$item->title, array('target' => '_parent')) ?></h3>
	              
	            </p>
                
	            <p class='blogs_browse_info_date'>
	              <?php echo $this->translate('Posted');?>	               
	              <?php
                      
                     
                                try{
                                if (is_numeric($item->pubDate)){
                                    $shortTime = explode(",",date("F j, Y, H:i:s",$item->pubDate));
                                }else
                                {
                                    $shortTime = explode(" ",$item->pubDate);
                                }
                           }catch(Exception $ex){
                                $shortTime = explode(" ",$item->pubDate);
                           }
                        $time="";
                       $i = 0;
                       if(count($shortTime)<=1)
                            $time = $shortTime[0];
                       else{
                       for($i = 0 ;$i < count($shortTime)-1;$i++){
                            $time.= $shortTime[$i] . " ";       
                       }}
                          if ($item->author == null || $item->author == "" )
                            echo(date('Y-m-d',$item->pubDate)." ". $this->translate('by  Unknown') );
                          else
                             echo(date('Y-m-d',$item->pubDate). " ".$this->translate('by').": " . $item->author);
                             
		          ?>               
	            </p>
	            <p class='blogs_browse_info_blurb'>
	              <?php
                    
	              	if($item->image != "")
			       		echo("<img src='".$item->image."' align='left'  style='padding-right:5px;' />");
	              
	                // Not mbstring compat
	                echo $this->feedDescription($item->description,500);
	              ?>
	            </p>
                      
                
                <div style="clear:both"></div>
                 <a  style="float:left" href="<?php echo($item->link_detail);?>" target="_blank" ><?php if (   $item->logo == "" || $item->display_logo == 0) :?> <?php echo $this->translate("")?><?php else:?><?php echo "<img src='".  $item->logo."'  alt=''/>"?> <?php endif;?>  </a>
                  
                 <p class="view_more">
                 <?php
                        $total_coment = $item->total_comment  ;
                        if($item->resource_id == NULL)
                            $total_coment --;
                    ?>
                    
                    <span class="total_comment"><?php echo ''.$this->htmlLink($item->getHref(),$this->translate('Comments'), array('target' => '_parent')) .'<font style="font-weight: bold;">:&nbsp;'.$total_coment.'&nbsp;&nbsp;</font> ';?></span>
                    <a href="<?php echo($item->link_detail);?>" target="_blank" ><?php echo $this->translate('View more').'...'; ?></a>
                    
                </p>
              	             
	          </div>
             
             
	        </li>
	      <?php endforeach; ?>
	    </ul>
	  
	  <?php else:?>
	    <div class="tip" style="margin-top: 10px;">
	      <span>
	        No News with that criteria   
	      </span>
	    </div>
	  <?php endif; ?>
	 <br /><br />
	  <?php echo $this->paginationControl($this->paginator, null, array("pagination/newspagination.tpl","news")); ?>
	</div>
</form>
</div>
<style type="text/css">
    @media projection{      
      .paginationControl{ display:none; }
    }
    @media print{      
      .paginationControl{ display:none; }
    }
    .blogs_browse_info_blurb img{
        padding-right:10px;
    }
     p.view_more{
        
        float:right;
        font-size:11px;
        padding:5px 0px 0px 5px;
        
        margin:0;
    }
    .form-element {
        padding-bottom:0;
    }
    .global_form_box .form-wrapper + .form-wrapper {
        margin-top:0;
        }

    .form-wrapper {
            clear:both;
            padding:0 0;
            }
</style>