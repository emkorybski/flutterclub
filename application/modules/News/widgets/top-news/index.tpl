<?php
$this->headLink()
        ->prependStylesheet($this->baseUrl() . '/application/css.php?request=application/modules/News/externals/styles/main-widgets.css');
$counter = 0;
?>

<ul>
  <?php $i= 0;
  foreach ($this->items as $item): ++$i;?>      	
    <li class="layout_news_popular_new <?php echo $i==1?"first":"" ?>">          
      <div class='widget_innerholder_news'>
        <h4 class="ynnew-tt3"><?php echo $this->htmlLink($item->getHref(), $item->title, array('target' => '_parent')) ?></h4>               
        <p class="ynnews-info3">
          <?php  echo $this->translate('Posted')." ".date('Y-m-d',$item->pubDate)." ".$this->translate('by').": ". $item->author; ?>
        </p>
        <p class="ynnews-bd3">
  
      <?php
           if($item->image != "") :
            echo $item->image;
           endif;
           echo $this->feedDescription($item->description);
      ?>
        </p>	             
        <div style="clear:both;"></div>
        <p class="ynnews-ft3">
          <?php echo $this->translate('Views').": " . "<font style='font-weight:bold'>" . $item->count_view . "</font>"; ?>
          <a class="ynnews-viewmore" href="<?php echo($item->link_detail); ?>" target="_blank" ><?php echo $this->translate('View more') . '...'; ?></a>
        </p>
      </div>
    </li>
  <?php endforeach; ?>
</ul>
