<?php

?>
 <script type="text/javascript" src="application/modules/YounetCore/externals/scripts/sl.js"></script>
<link rel="stylesheet" href="application/modules/YounetCore/externals/styles/sl.css" type="text/css" media="screen" />
<style type="text/css">
     .i_title{
         font-weight: bold;
         padding-bottom:2px;    
     }
     .i_title .t
     {
         font-weight: bold;
         padding-bottom:2px;    
     }
     .price{
         padding-bottom:3px;    
         font-weight: bold;
     }
     table.admin_table tbody tr td:last-child 
     {
          white-space:normal;
     }
     ul.view_yn_photo
     {
         float:left;
         margin-left:10px;
     }
     ul.view_yn_photo li
     {
         float:left;
         padding:5px;
     }
     ul.view_yn_photo li img
     {
         width:130px;
         border: 1px solid;
     }
</style>
<h2>
  <?php echo $this->translate('YouNet Developments') ?>
</h2>
<?php if( count($this->navigation) ): ?>
<div class='tabs'>
  <?php
    // Render the menu
    echo $this->navigation()->menu()->setContainer($this->navigation)->render();
  ?>
</div>
<?php endif; ?>

<div>
    <div style="float:left;width:50%">
    <h3 class="sep">
         <span><?php echo $this->translate('YouNet plugins') ?></span> 
    </h3>
    <?php
        if( count($this->paginator)>0 ):
    ?>
      <table class='admin_table' style="width:100%">
        <thead>
          <tr>
             <th align="center" width="50px">
                <?php echo $this->translate("Image"); ?>  
            </th>
             <th align="left">
                <?php echo $this->translate("Modules"); ?>
            </th>
            
            
          </tr>
        </thead>
        <tbody>
            <?php foreach ($this->paginator as $key=>$item):?>   
            <tr>
              <td><img style="vertical-align: middle;" src="<?php if (isset($item->image_url) && $item->image_url !=""){echo $item->image_url;}else{echo 'http://younetco.com/wp-content/themes/yns_younetco/images/logo.png';}?>" alt="<?php echo $item->name?>" width="50px" height="50px"/></td>
              <td>
                <div class="i_title"><span style="color: #5F93B4;font-weight: bold;"><a class="t" href="javascript:viewPhotos('<?php echo $key?>')"><?php echo $item->name .' - '.$item->latest_v; ?></a></span><span style="float:right"><?php echo $this->htmlLink($item->purchase, $this->translate('[ Detail ]'),array('target'=>'blank')); ?></span></div>
                <div class="price"><?php echo $this->translate('Price').':'.' ';if(array_key_exists($key,$this->yours)){ echo "Purchased";}else{echo $item->price. ' '.$item->currency;} ?></div>
                <div class="des"><?php echo ($item->sort_description); ?></div>
              </td>
              
            </tr>
               
          <?php endforeach; ?>
        </tbody>
      </table>
      <br />
       <?php echo $this->paginationControl($this->paginator, null, null,null); ?>
       
    <?php else: ?>
      
        <span>
          <?php echo '<div class="tip"><span>No plugins by YouNet Development were found on your site. Click <a href="http://socialengine.modules2buy.com" target="_blank">here</a> to view and purchase them.</span></div>'; ?>
        </span>
      
    <?php endif; ?>
    </div>
   <div class="news" style="width:47%;float:right">
        <h3 class="sep">
          <span id="yn_id_description" style="text-transform :capitalize;"><?php echo $this->translate('YouNet Descriptions') ?></span>
       </h3>
       <div class="younetnews" style="min-height: 500px;" id="view_descriptions"  align="center">
            
       </div>
    </div>
    <div class="clear"></div>
</div>
<script type="text/javascript">
    function viewPhotos(name)
    {
         var req = new Request.JSON({
         method: 'post',
          url: 'admin/younet-core/settings/p',
          data: {m:name,t:'photo'},
          onRequest: function() { req1(name)},
          onComplete: function(response) { 
              viewP(response,name);
          },
          onFailure: function(){
              
          },
       
        }).send();
    }
    function req1(name)
    {
        $('yn_id_description').set('text',name +" Descriptions");  
        var img = '<img src="application/modules/YounetCore/externals/images/loading.gif" align="left" style="display: inline-block;float: left;height: 50px;margin-right: 15px;vertical-align: middle;" id="img_loadding"/>';
        $('view_descriptions').set('html',img);
    }
    function viewP(response,name)
    {
        var html = "";
        if(response == false && response == "undefined")
        {
            html = "There are no photos description.";
        }
        else
        {
            
            var length = response.length;
            var i = 0;
            for( i = 0 ; i< length; i++)
            {
                html+="<li><a href='"+response[i].reallink+"' rel='lightbox-atomium' ><img src='"+response[i].thumnail+"' alt='"+name+"'/></a></li>";
            }
        }
        $('view_descriptions').set('html','<ul class="view_yn_photo" >'+html+'</ul>');
        Slimbox.scanPage();
        
    }
    viewPhotos('advalbum');
</script>