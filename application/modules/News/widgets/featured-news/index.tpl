<?php
    $this->headLink()
        ->prependStylesheet($this->baseUrl().'/application/css.php?request=application/modules/News/externals/styles/main-widgets.css')
        ->prependStylesheet($this->baseUrl().'/application/css.php?request=application/modules/News/externals/styles/newsfeed.css')
        ->prependStylesheet($this->baseUrl().'/application/css.php?request=application/modules/News/externals/styles/slider.css')
        ;
    $this->headScript()
         ->appendFile($this->baseUrl() . '/application/modules/News/externals/scripts/jquery.js')
         ->appendFile($this->baseUrl() . '/application/modules/News/externals/scripts/jquery-easing-1.3.pack.js')
         ->appendFile($this->baseUrl() . '/application/modules/News/externals/scripts/jquery-easing-compatibility.1.2.pack.js')
         ->appendFile($this->baseUrl() . '/application/modules/News/externals/scripts/coda-slider.1.1.1.pack.js')
         ;
    $counter = 0;
?>




    <script type="text/javascript">
		jQuery.noConflict();



        var startnumber = 1;
        var endnumber = 5;
        var totalItem = <?php echo $this->totalItem?>;
        var theInt = null;
        var $crosslink, $navthumb;
        var curclicked = 0;

        theInterval = function(cur){
            clearInterval(theInt);

            if( typeof cur != 'undefined' )
                curclicked = cur;

            $crosslink.removeClass("active-thumb");
            $navthumb.eq(curclicked).parent().addClass("active-thumb");
                jQuery(".stripNav ul li a").eq(curclicked).trigger('click');

            theInt = setInterval(function(){
                $crosslink.removeClass("active-thumb");
                $navthumb.eq(curclicked).parent().addClass("active-thumb");
                 jQuery(".stripNav ul li a").eq(curclicked).trigger('click');
                curclicked++;

                if( totalItem == curclicked )
                {
                    curclicked = 0;
                    startnumber = 0;
                    endnumber = 4;
                    show(1);
                }

                if ( curclicked>5)
                {
                     show(1);
                }

            }, 3000);

        };

        jQuery(document).ready(function($){


            $("#main-photo-slider").codaSlider();

            $navthumb = $(".nav-thumb");
            $crosslink = $(".cross-link");

            $navthumb
            .click(function() {
                var $this = $(this);
                theInterval($this.parent().attr('number').slice(1) - 1);
                return false;
            });

            theInterval();
        });
        function show(step)
        {
            //alert(step);
            startnumber = startnumber + step;
            endnumber = endnumber + step;


            if (endnumber >totalItem)
            {
                startnumber = startnumber - step;
                endnumber = totalItem;
                return;
            }
            if (startnumber <=0)
            {
               startnumber = 1;
               endnumber = 5;
               return;
            }
            //console.log(startnumber);
            //console.log(endnumber);
            var i = 1;
            for ( i = 1 ; i <= totalItem ; i++)
            {
                var eleShow = document.getElementById("item_mover_"+i);
                if ( i < startnumber)
                    eleShow.style.display = "none";
                else if(i > endnumber)
                    eleShow.style.display = "none";
                else
                    eleShow.style.display = "block";
            }


        }
        function clickA(id)
        {

             jQuery('#clickA_'+id).trigger('click');
        }
    </script>

<div id="page-wrap" style="">

    <div class="slider-wrap" style="margin-top: -10px;">
        <div id="main-photo-slider" class="csw">
            <div class="panelContainer">
            <?php $count = 0;?>
               <?php foreach($this->featuredNews as $item ) : ?>

                  <?php $count++;?>
                 <div class="panel">
                    <div class="wrapper" style="width:510px;">
                        <div id="news_list">
                             <div class="row_title">

                               <h4> <?php echo $this->htmlLink($item->getHref(),$item->title, array('target' => '_parent')) ?></h4>
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
                                     $author = $this->translate('Unknown');
                                  else
                                     $author = $item->author;
                                ?>
                                <div>
                                 <span><span class="datetime" ><?php echo $this->translate('Posted')." ".date('Y-m-d',$item->pubDate) ?></span></span>
                                    <span><span class="datetime" ><?php echo $this->translate('by')." ".$author ?></span></span><br/>
                                 </div>
                            </div>

                            <div class="blog_content">
                                <div class = "image_content">
                                    <?php if ($item->image): ?>
                                        <img src="<?php echo $item->image ?>" alt=""/>
                                    <?php else:?>
                                        <?php
                                            $img = catch_that_image($item->description);
                                            if ($img !='')
                                               echo '<img src="'.$img.'" alt="" width=70 height=70/> ';
                                        ?>
                                    <?php endif;?>
                                </div>
                                <div class = "description_content">

                                    <span class = "description" style=""> <?php echo $this->feedDescription($item->description,300); ?></span>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach;?>

            </div>
        </div>

 <div style="clear:both"></div>
        <div id="movers-row" style="min-width:700px;">

        <?php $count=0?>
            <?php foreach($this->featuredNews as $item ) : ?>

              <?php $count++;?>

                <div <?php if ($count>5): ?>style="display:none"<?php endif;?> id="item_mover_<?php echo $count?>" >
                    <div  number="#<?php echo $count?>" class="cross-link" >
                        <div class="nav-thumb" id="clickA_<?php echo $count?>">
                            <?php if($item->image!=''&& $item->image!=null): ?>
                                <img width="80px" height="50px" src="<?php echo $item->image?>" />
                            <?php else:?>
                                        <?php
                                        $des = $item->description;
                                            $img = catch_that_image($des);
                                            if ($img !='')
                                               echo '<img src="'.$img.'" alt="" width="80px" height="50px"/> ';
                                        ?>
                            <?php endif;?>
                            </div>
                        <a href="javascript:clickA(<?php echo $count?>)" id="view_title_small" <?php if($item->image == '' && $item->image!=null): ?> style="margin-top:30px"<?php endif; ?> ><?php echo substr($item->title,0,32).'...'?></a>
                    </div>
                </div>

              <?php endforeach;?>

        </div>

    </div>

         <div id="button-next-prev">
          <?php if ( $this->totalItem >5):?>
         <a href="javascript:show(-1)" onclick=""><img src="application/modules/News/externals/images/back_icon.gif"/></a>
        <a href="javascript:show(1)" onclick=""><img src="application/modules/News/externals/images/next_icon.gif"/></a>
         <?php endif;?>
        </div>

    </div>
<div style="clear:both"></div>
<?php
    function catch_that_image(&$des) {
          $output = preg_match_all('/<img.+src=[\'"]([^\'"]+)[\'"].*>/i', $des, $matches);
          $first_img = @$matches [1] [0];

          if(empty($first_img)){ //Defines a default image
            return '';
          }
          return $first_img;
      }

?>

 <style type="text/css">
     .active-thumb
      {

          background:url("<?php echo $this->baseUrl();?>/application/modules/News/externals/images/bg_over.gif") no-repeat center 15%;

          /* background:#e9f4fa;         */
      }
      #movers-row div.active-thumb a {
          color:white;
      }
     .photo-meta-data { background: transparent url("~/application/modules/News/externals/images/icon-transpBlack.png") }
     .first_cross{
         margin: 4px 0 0 0 ;
         display:inline-block;

     }
     #view_title_small{
         font-size: 9px;
         width: 80px;
         text-align: justify;
     }

     </style>


