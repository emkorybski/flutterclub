<script type="text/javascript">
  var pageAction =function(page){
    $('nextpage').value = page;
    $('gotoPage').submit();
  }
</script>


<div class='layout_middle'>
<form name="gotoPage" id="gotoPage" method="post">
  <input type="hidden" name="nextpage" id="nextpage">
  <span style="display:none;"><input type="text" name="category" id="category" value="<?php echo($this->categoryId);?>" /></span>
	<div style="overflow: hidden;">	    
	  <?php if( $this->paginator->getTotalItemCount() > 0 ): ?>
	    <ul class="blogs_browse">
	      <?php foreach( $this->paginator as $item ): ?>      	
	        <li style="clear: both;padding-top:10px;">          
	          <div class='blogs_browse_info'>
	            <p class='blogs_browse_info_title'>
                  <?php if($item->mini_logo == 1): ?><img src="<?php echo $item->logo_icon ?>" alt="" style="float: left; margin-right: 5px; padding-top: 5px"> <?php endif; ?><h3><?php echo $this->htmlLink($item->getHref(),$item->title, array('target' => '_parent')) ?></h3>               
	              
	            </p>
	            <p class='blogs_browse_info_date'>
	              <?php echo $this->translate('Posted');?>	               
	              <?php
		               $shortTime =  explode(" ",$item->pubDate);
                        $time ="";
                       if (isset($shortTime[0]))
                            $time.= $shortTime[0] . " ";
                         if (isset($shortTime[1]))
                            $time.= $shortTime[1] . " ";
                         if (isset($shortTime[2]))
                            $time.= $shortTime[2] . " ";
                         if (isset($shortTime[3]))
                            $time.= $shortTime[3] . " ";
		               echo(date('Y-m-d',$item->pubDate). $this->translate('by:') . $item->author);
		          ?>               
	            </p>
	            <p class='blogs_browse_info_blurb'>
	              <?php
	              	if($item->image != "")
					{
			       		echo "<img style='padding-right:10px' src='".$item->image."' align='left'  />";
					}
	              
	                // Not mbstring compat
	                echo $this->feedDescription($item->description);
	              ?>
	            </p>	             
	          </div>
              <p class="view_more">
                    <a href="<?php echo($item->link_detail);?>" target="_blank" ><?php echo $this->translate('View more').'...'; ?></a>
              </p>
	        </li>
	      <?php endforeach; ?>
	    </ul>
	  
	  <?php else:?>
	    <div class="tip">
	      <span>
	        No news with that criteria   
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
    .view_more{
        
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