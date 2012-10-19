<h2><?php echo $this->translate("Friends Inviter Plugin") ?></h2>

<?php if( count($this->navigation) ): ?>
  <div class='tabs'>
    <?php
      // Render the menu
      //->setUlClass()
      echo $this->navigation()->menu()->setContainer($this->navigation)->render();
      
    ?>
  </div>
<?php endif; ?>

<p>
  <?php echo $this->translate("Find some quick answers on this page") ?>
</p>

<br />


<div class="admin_statistics">

  <div style='cursor: pointer; margin-bottom: 10px; padding: 5px 10px; font-weight: bold; background-color: #F6F6F6' onclick="($('friendsinviter_faq_1').style.display=='block') ? $('friendsinviter_faq_1').style.display='none' : $('friendsinviter_faq_1').style.display='block';">
    <?php echo $this->translate('Showing Top Referrer / Top Inviter widgets on global site homepage') ?>
  </div>
  <div style='border: 1px solid #F6F6F6; padding: 5px; display: none; margin-bottom: 40px' id='friendsinviter_faq_1'>


    <br><br>
    <i><?php echo $this->translate("Available in the Advanced Version") ?> <a target=_blank href="http://www.socialenginemods.net/social-engine/plugins/1/friends-inviter-contacts-importer"><?php echo $this->translate("Click here to visit") ?></a></i>
    <br><br>
    <br><br>

    <img src="application/modules/Friendsinviter/externals/images/help/1.png">
  </div>

  <div style='cursor: pointer; margin-bottom: 10px; padding: 5px 10px; font-weight: bold; background-color: #F6F6F6' onclick="($('friendsinviter_faq_2').style.display=='block') ? $('friendsinviter_faq_2').style.display='none' : $('friendsinviter_faq_2').style.display='block';">
    <?php echo $this->translate('Showing "Find Friends" Teaser on User Homepage') ?>
  </div>
  <div style='display: none;' id='friendsinviter_faq_2'>
  <div style='border: 1px solid #F6F6F6; padding: 5px'>

    <br><br>
    <i><?php echo $this->translate("Available in the Advanced Version") ?> <a target=_blank href="http://www.socialenginemods.net/social-engine/plugins/1/friends-inviter-contacts-importer"><?php echo $this->translate("Click here to visit") ?></a></i>
    <br><br>
    <br><br>

    <img src="application/modules/Friendsinviter/externals/images/help/3.png">
  </div>  
  <br>
  <div style='border: 1px solid #F6F6F6; padding: 5px'>
    <img src="application/modules/Friendsinviter/externals/images/help/2.png">
  </div>
  </div>

  <div style='cursor: pointer; margin-bottom: 10px; padding: 5px 10px; font-weight: bold; background-color: #F6F6F6' onclick="($('friendsinviter_faq_3').style.display=='block') ? $('friendsinviter_faq_3').style.display='none' : $('friendsinviter_faq_3').style.display='block';">
    <?php echo $this->translate('Resetting hidden "Find Friends" Teaser') ?>
  </div>
  <div style='border: 1px solid #F6F6F6; padding: 5px; display: none; margin-bottom: 40px' id='friendsinviter_faq_3'>
    If user chooses to hide the widget, you can reset it (force to re-appear) for all users (for example once a month).
    <br>
      <a href="<?php echo $this->url(array('action' => 'clear')) ?>">Click here</a> to show it for all users. Currently hidden for <?php echo $this->hidden_count ?> out of <?php echo $this->total_users ?> total users (<?php echo sprintf("%.2f",($this->hidden_count / $this->total_users) * 100) ?>%).
  </div>

  <div style='cursor: pointer; margin-bottom: 10px; padding: 5px 10px; font-weight: bold; background-color: #F6F6F6' onclick="($('friendsinviter_faq_4').style.display=='block') ? $('friendsinviter_faq_4').style.display='none' : $('friendsinviter_faq_4').style.display='block';">
    <?php echo $this->translate('Including "Unsubscribe" link in the invitation emails.') ?>
  </div>
  <div style='border: 1px solid #F6F6F6; padding: 5px; display: none; margin-bottom: 40px' id='friendsinviter_faq_4'>
    To conform with privacy policies, every outgoing invitation email can include an "Unsubscribe" link, which allows opting out of receiving invitation emails.
    <br>
    The link will look like: <?php echo "http://{$_SERVER['HTTP_HOST']}"
                          . Zend_Controller_Front::getInstance()->getRouter()->assemble(array(
                                'module'      => 'core',
                                'controller'  => 'invite',
                                'action'      => 'unsubscribe',
                                ), 'default');
                          ?>
                          
    <br><br>
      
      To include it in every invitation email, put a <span style='font-weight: bold'>[unsubscribe_link]</span> variable in the invitation email template.

    <br><br>

    <div style='border: 1px solid #F6F6F6; padding: 5px'>
      <img src="application/modules/Friendsinviter/externals/images/help/4.png">
    </div>

  </div>
  
  


</div>
