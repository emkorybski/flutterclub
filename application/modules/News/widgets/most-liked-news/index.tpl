<?php
    $this->headLink()
        ->prependStylesheet($this->baseUrl().'/application/css.php?request=application/modules/News/externals/styles/main-widgets.css');
    $counter = 0;
?>


   
	  <?php if( count( $this->topNews)>0): ?>
	    <ul>
	      <?php foreach(  $this->topNews as $item ): ?>      	
	        <li style="clear: both;padding-top:10px; margin-bottom: -7.5px;" class="layout_news_popular_new">
	          <div class='widget_innerholder_news'>
	            <p class='blogs_browse_info_title'>
                  <h4><?php echo $this->htmlLink($item->getHref(),$item->title, array('target' => '_parent')) ?></h4>               
	              
	            </p>
	            <p class='blogs_browse_info_date'>
	              <?php echo $this->translate('Posted');?>	               
	              <?php
		              if ($item->pubDate_parse != "" && $item->pubDate_parse !=null){
                            $shortTime = explode(" ",$item->pubDate_parse);
                            
                            //echo $shortTime;
                       }
                       else
                       {
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
                              echo( date('Y-m-d',$item->pubDate)." ". $this->translate('by  Unknown') );
                          else
                             echo(date('Y-m-d',$item->pubDate). " ".$this->translate('by') . $item->author);
		          ?>               
	            </p>
	            <p class='blogs_browse_info_blurb'>
	              <?php
	              	 if($item->image != "") :
					
			       		echo $item->image;
					
	              ?> <br /> <?php 
	                // Not mbstring compat
	            
	               endif;	
	               
	               $str = $item->description;
					$pieces = explode("</a>", $str);
					$des = $pieces[1];
					if ($des != null) {
					if(strlen($des) > '60'):
                        echo $this->string()->chunk(substr($des, 0, 60), 60); echo  "..";   
                     else:
                        echo $des;
                     endif; 
					}
					else {
						if(strlen($str) > '60'):
                        echo $this->string()->chunk(substr($str, 0, 60), 60); echo  "..";   
                     else:
                        echo $str;
                     endif; 
					}
	                //echo ($item->description);
	              ?>
	            </p>	             
	          </div>
			  <p style="float:left">
                    <?php echo $this->translate('Likes: ') . "<font style='font-weight:bold'>".$item->count_like."</font>"; ?>
              </p>
              <p class="view_more">
                    <a href="<?php echo($item->link_detail);?>" target="_blank" ><?php echo $this->translate('View more').'...'; ?></a>
              </p>
	        </li>
	      <?php endforeach; ?>
	    </ul>

	   <?php endif; ?>
       
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
        
        
        margin:0;
    }
    p.blogs_browse_info_date{
        color:#999999;
        font-size:0.8em;
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
            .layout_news_top_news > ul, .layout_news_top_news > ul, .layout_news_lasted_news > ul, .layout_news_most_commented_news > ul, .layout_news_most_liked_news > ul{
                background-color:transparent;
}
</style>
