<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    News
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: view.tpl 7244 2010-09-01 01:49:53Z john $
 * @author     Jung
 */
?>
<?php
$flag = false;
if(Engine_Api::_()->user()->getViewer()->getIdentity() > 0):
        
    $username  = Engine_Api::_()->user()->getViewer()->username;
    $users = Engine_Api::_()->news()->getAllUsers();
    foreach ($users as $user):
       if ($user['username'] == $username):
           $flag = true; 
       endif;
   endforeach;
   if ( Engine_Api::_()->user()->getViewer()->level_id == 1 || Engine_Api::_()->user()->getViewer()->level_id == 2):
           $flag = true; 
       endif;
   endif;
   if($flag == true):

?>
<div class="headline">
  <h2>
    <?php echo $this->translate('News');?>
  </h2>
  <?php if( count($this->navigation) > 0 ): ?>
    <div class="tabs">
    <?php
      // Render the menu
      echo $this->navigation()
        ->menu()
        ->setContainer($this->navigation)
        ->render();
    ?>
  </div>
  <?php endif; ?>
</div>
<?php endif;  ?>
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
</script>
<style type="text/css">
	@media projection{	  
	  div{ display:none; }
	}
	@media print{	  
	  #global_footer{display:none;}
	  #global_header{display:none;}
	  #ats_fbmenu_menu{display:none;}
	  .im_item_tooltip_settings{display:none;}	  
	  .ats_fbmenu_color_dark{display:none;}
	  .ats_fbmenu_table{display:none;}	 
	  #share{display:none;} 
	  .layout_right{display:none;} 
	  .layout_middle{display:block;}
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
.blogs_browse_info_blurb img,.blog_entrylist_entry_body img{
        padding-right:10px;
    }

</style>


<div class='layout_middle'>
  <ul class='blogs_entrylist'>
    <li>
      <h3>
        <?php echo $this->content->title ?>
      </h3>
      <div class="blog_entrylist_entry_date">
        <?php echo $this->translate('Posted date');?> 
          <?php
            if ($this->content->pubDate_parse != "" && $this->content->pubDate_parse !=null){
                            $shortTime = explode(" ",$this->content->pubDate_parse);
                            //echo $shortTime;
                       }
                       else
                       {
                                try{
                                if (is_numeric($this->content->pubDate)){
                                    $shortTime = explode(",",date("Y:m:d",$this->content->pubDate));
                                }else
                                {
                                    $shortTime = explode(" ",$this->content->pubDate);
                                }
                           }catch(Exception $ex){
                                $shortTime = explode(" ",$this->content->pubDate);
                           }
                       }    
                        $time="";
                       $i = 0;
                        if(count($shortTime)<=1)
                            $time = $shortTime[0];
                       else{
                       for($i = 0 ;$i < count($shortTime)-1;$i++){
                            $time.= $shortTime[$i] . " ";       
                       }
                       }
                          if ($this->content->author == null || $this->content->author == "" )
                             echo( date('Y-m-d',$this->content->pubDate)." ". $this->translate('by  Unknown') );
                          else
                             echo(date('Y-m-d',$this->content->pubDate). " ".$this->translate('by').": " . $this->content->author);
                  ?>               
      </div>
      <div class="blog_entrylist_entry_body">
       
        <?php 
            if ( $this->content->content != ''){
                echo $this->content->content ;
            }else{
                echo $this->content->description ;
            }
            
        ?>
        <div style="clear:both"></div>
        
                <a href="<?php echo($this->content->link_detail);?>" target="_blank" ><?php if (   $this->category[0]['category_logo'] == "") :?> <?php echo $this->translate("")?><?php else:?><?php echo "<img src='". $this->category[0]['category_logo']."'  alt=''/>"?> <?php endif;?>  </a>
                <div style="clear:both"></div>   
         <p class="view_more">
            <a href="<?php echo($this->content->link_detail);?>" target="_blank" ><?php echo $this->translate('View more').'...'; ?></a>
       </p>
      </div>
      <div style="clear:both"></div>
       
    </li>
  </ul>																 
   <?php  echo $this->action("lists", "index", "news", array("type"=>"news_content", "id"=>$this->content->getIdentity())); ?>
  <div id="share" style="margin-top:10px;">
  <span style="padding-bottom:0px;padding-right:10px;"><?php echo $this->htmlLink(Array('module'=> 'core', 'controller' => 'report', 'action' => 'create', 'route' => 'default', 'subject' => "news_content_".$this->content->getIdentity(), 'format' => 'smoothbox'), $this->translate("Report"), array('class' => 'smoothbox')); ?></span>
  <span style="width:100px;padding-right:10px;cursor:pointer;background-image: url('application/modules/News/externals/images/print_icon.gif'); background-repeat:no-repeat;"  onclick="printpage();">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span><span class="st_facebook"></span><span class="st_twitter"></span><span class="st_sharethis" displayText="ShareThis"></span>
  </div>
</div>
<script type="text/javascript" src="http://w.sharethis.com/button/buttons.js"></script><script type="text/javascript">stLight.options({publisher:'1aa52842-757c-4d8b-b509-ad6790dc26e6'});</script>
<script language="Javascript1.2">  
  function printpage() {
  	window.print();
  }
  function clickPostComment(){
    var div = document.getElementById('comments');
    
    if(div)
    {
        var tagA = div.getElementsByTagName('a')[0];
       
        if(tagA)
        {
            
          tagA.onclick();
            
        }
        
    }
  }

	window.addEvent('domready',function(){
	    $('comment-form').style.display = '';
	    $$('.blog_entrylist_entry_body a').each(function(a){
	    	a.setAttribute('target','_default');
	    	});
	    //tinyMCE.execCommand('mceAddControl', true,'body');setTimeout('tinyMCE.execCommand(\'mceFocus\',\'\',\'body\');',300);
	});


</script>

