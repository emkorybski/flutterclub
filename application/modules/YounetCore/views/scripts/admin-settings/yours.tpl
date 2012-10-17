<?php

?>
<style type="text/css">
   .package_query_results ul {
    border: 1px solid #CCCCCC;
    margin-top: 10px;
    max-height: 400px;
    overflow-y: auto;
   
    margin-bottom: 10px;
}
.package_query_results li.error {
    background-image: url("application/modules/YounetCore/externals/images/error.png");
    background-position: 5px center;
    background-repeat: no-repeat;
}
span.validated1{
       background:url('application/modules/YounetCore/externals/images/success.png') no-repeat center left;
       
    }
span.invalid1{
        background:url('application/modules/YounetCore/externals/images/error.png') no-repeat center left;
        
    }
.package_query_results li {
    color: red;
    font-size: 0.8em;
    font-weight: bold;
    padding: 10px 10px 10px 27px;
    text-transform: uppercase;
    
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
         <span><?php echo $this->translate('YouNet plugins for your site') ?></span> 
    </h3>
    <?php if(isset($_SESSION['invalid'])):?>
    <div class="package_query_results"><ul><li class="error"><?php echo $_SESSION['invalid'].' '.$this->translate('was not be verified. Please check again.');unset($_SESSION['invalid']);?></li></ul></div>
    <?php endif;?>
     <div id="verify_lis" style="margin-bottom:15px"></div>
    <?php
        if( count($this->modules) ):
    ?>
      <table class='admin_table' style="width:100%">
        <thead>
          <tr>
            
             <th align="left">
                <?php echo $this->translate("Name"); ?>
            </th>
            <th align="left">
                <?php echo $this->translate("Latest Version"); ?>
            </th>
            <th align="left">
                <?php echo $this->translate("Current Version"); ?>
            </th>
            <th align="left">
                <?php echo $this->translate("Status"); ?>
            </th>
          </tr>
        </thead>
        
        <tbody>
            <?php foreach ($this->modules as $key=>$item):?>    
                <?php
                    $current_v = $item['current_v'];
                    $latest_v = $item['latest_v'];
                    $versionInfo = 0;
                    $action = $this->translate('Up To Date');
                    $current_v = (int)str_replace('.','',$current_v);
                    $latest_v = (int)str_replace('.','',$latest_v);
                    if($latest_v > $current_v)
                    {
                        $action = $this->translate('Upgrade Now.'); 
                    }
                    $verify = "javascript:verify('".$key."')";
                ?>
              <td><?php echo $item['name']; ?></td>
                        <td><?php echo $item['current_v']; ?></td>
                        <td><?php echo $item['latest_v']; ?></td>
                        <td>
                        <?php if(isset($item['params']) && $item['params'] !="" && $item['is_active'] == 1){if($latest_v > $current_v){echo '<a target="_blank" href="'.$item['download'].'">'.$action .'</a>';}else{echo $action; }}else{echo '<a href="'.$verify.'">Verify</a>';}?>
                        </td>
            </tr>
               
          <?php endforeach; ?>
        </tbody>
      </table>
      <br />
        
            <form action="" method="post" id="refixformcheck" name="refixformcheck" onsubmit="">
            <input type="hidden" name="checkingmodule" value="checkmodule"/>
            <div class="tip">
            <span><?php echo $this->translate("If the plugins which are not developed by YouNet Company appear in the list above. Please click to button \"Check YouNet Plugins\" to solve. ");?></span>
            </div>
            <button name="refixmodule" id="refixmodule" type="submit"><?php echo $this->translate("Check YouNet Plugins");?></button>
        </form>
    <?php else: ?>
      <div class="tip">
        <span>
          <?php echo 'No plugins by YouNet Company were found on your site. Click <a href="http://socialengine.modules2buy.com" target="_blank">here</a> to view and purchase them.'; ?>
        </span>
      </div>
    <?php endif; ?>
    </div>
    <div class="news" style="width:45%;float:right">
        <h3 class="sep">
          <span><?php echo $this->translate('YouNet News') ?></span>
       </h3>
       <div class="younetnews" style="min-height: 500px;">
       <?php if (count($this->news['items'])>0):?>
           
           <ul class="news_ul">
                <?php foreach ($this->news['items'] as $n):?>  
                <li class="news_li">
                    <div class="news_title"><span><?php echo date('M-d-Y',strtotime($n['pubDate']))?></span><a href="<?php echo $n['link']?>"><?php echo $n['title']?></a></div>
                    <div class="clear"></div>
                    <div class="news_content">
                        <?php echo $n['description'];?>
                    </div>
                    
                </li>
              <?php endforeach;?>   
           </ul>
        <?php else:?> 
            <div class="messages_list_new">There are no news.</div>
        <?php endif;?>
       </div>
    </div>
    <div class="clear"></div>
</div>
<script type="text/javascript">
   
    function verify(name)
    {
        var req = new Request({
          method: 'post',
          url: en4.core.baseUrl + 'admin/younet-core/settings/f',
          data: {ur : name},
          onRequest: function() {request1();  },
          onComplete: function(response) { 
              $('verify_lis').set('html',response);q();
              var scroll = new Fx.Scroll('global_content_wrapper', {
                wait: false,
                duration: 2500,
                offset: {'x': -200, 'y': -50},
                transition: Fx.Transitions.Quad.easeInOut
            });
            scroll.toElement('yn_verify_f');
          },
          onFailure: function(){
              
          },

        }).send();
    }
    function q()
    {
         $('done_verify').removeEvents('click');
          $('done_verify').addEvent('click', function(event) {
                 event.stop();
                 $('ls').value = $('l').value;
                 var req = new Request.JSON({
                  method: 'post',
                  url: en4.core.baseUrl +'admin/younet-core/settings/l',
                  data: $('yn_verify_f'),
                  onRequest: function() {request();  },
                  onComplete: function(response) { 
                      viewrequest();viewmessage(response.key); 
                  },
                  onFailure: function(){
                      viewmessage(false);  
                  },

                }).send();
              });
              $('cancel_verify').addEvent('click', function(event)
            {
                $('verify_lis').set('html','');
            });
            $('img_loadding').set('src','application/modules/YounetCore/externals/images/loading.gif');
            $('img_loadding').set('style','display: inline-block;float: left;height: 40px;margin-right: 15px;vertical-align: middle; width: 300px;');
    }
    function request1()
    {
        var img = '<img src="application/modules/YounetCore/externals/images/loading.gif" align="left" style="display: inline-block;float: left;height: 50px;margin-right: 15px;vertical-align: middle;" id="img_loadding"/>';
        $('verify_lis').set('html',img);
    }
    function request()
    {
        $('loadding_yn').show();$('l').hide();$('done_verify').hide();
        $('submit_yn').set('style','margin:0');
        $('m_yn').hide();
        $('cancel_verify').removeEvents('click');
        $('cancel_verify').addEvent('click', function(event)
        {
        viewrequest();  
        });
    }    
    function viewrequest()
    {
        $('loadding_yn').hide();$('l').show();$('done_verify').show();
        $('submit_yn').set('style','margin-top:15px');
        $('m_yn').show();
        $('cancel_verify').removeEvents('click');
        $('cancel_verify').addEvent('click', function(event)
        {
            $('verify_lis').set('html','');
        });
           
    }
    function viewmessage(mes)
    {
        if(mes == false)
        {
        $('m_yn').set('text','Invalid Key.');
        $('m_yn').set('class','mes invalid1');
        }
        else
        {
            var svl = '<input type="hidden" name="svl" id="svl" value="'+mes+'">';
            $('yn_verify_f').set("html", $('yn_verify_f').get("html") + svl);
            $('m_yn').set('text','Validated.');
            $('m_yn').set('class','mes validated1');
            $('done_verify').set('text','Continue');
            $('l').set('disabled', true);
            $('l').set('style', 'display:inline-block;background:#FAF8E9');
            $('l').value = $('ls').value;
            $('done_verify').removeEvents('click'); 
            $('done_verify').addEvent('click', function(event)
            {
                $('yn_verify_f').submit();
            });
            $('cancel_verify').addEvent('click', function(event)
            {
                $('verify_lis').set('html','');
            });
        }

    }
</script>
