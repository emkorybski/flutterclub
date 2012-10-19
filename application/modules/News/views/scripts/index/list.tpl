<script type="text/javascript">
  var categoryAction = function(category){
    $('category').value = category;
    $('filter_form').submit();
  }
  var tagAction =function(tag){
    $('tag').value = tag;
    $('filter_form').submit();
  }
  var dateAction =function(start_date, end_date){
    $('start_date').value = start_date;
    $('end_date').value = end_date;
    $('filter_form').submit();
  }

  var pageAction =function(page){
	    $('nextpage').value = page;
	    $('gotoPage').submit();
  }
</script>
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
</style>


<div class='layout_middle'>
  <form name="gotoPage" id="gotoPage" method="post">
  <input type="hidden" name="nextpage" id="nextpage">
  <span style="display:none;"><input type="text" name="category" value="<?php echo($this->categoryId);?>" /></span>
  <?php if(isset($this->category[0])): ?>
  <h3><?php echo $this->translate('List news of category'); ?>: <?php echo $this->category[0]['category_name'] ?></h3>
  <?php endif;?>
  <?php if( $this->paginator != null && $this->paginator->getTotalItemCount() > 0 ): ?>
  <ul class='blogs_entrylist'>
    <?php foreach( $this->paginator as $item ): ?>      	
        <li style="clear:both;">          
          <div class='blogs_browse_info'>
            <p class='blogs_browse_info_title'>
              <h3><?php echo $this->htmlLink($item->getHref(), $item->title) ?></h3>
            </p>
            <p class='blogs_browse_info_date'>
              <?php echo $this->translate('Posted');?>
              <?php echo $this->timestamp(strtotime($item->posted_date)) ?>              
            </p>
            <p class='blogs_browse_info_blurb'>
              <?php
				if($item->image != "")
		       		echo("<img src='".$item->image."' align='left'  style='width:150px;padding-right:5px;' />");
              
                // Not mbstring compat
                echo $this->feedDescription($item->description,350);
              ?>
            </p>                    
          </div>
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
	</form>
</div>
<script>
	function selectedCategory()
	{	
		var obj = document.getElementById("category");
		for(i=0; i<obj.options.length; i++)
		{
			if(obj.options[i].value == <?php echo($this->categoryId);?>)
			{
				obj.options[i].selected = true;
			}
		}		
	}
	selectedCategory();
</script>