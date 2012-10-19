<?php
    $session = new Zend_Session_Namespace('inviter');
    $providerApi = Engine_Api::_()->getApi('provider', 'inviter');
    $settings = Engine_Api::_()->getApi('settings', 'core');
    $host = $_SERVER['HTTP_HOST'];

?>
<?php if ($session->__get('provider') == 'facebook'): ?>
    <script src="http://connect.facebook.net/en_US/all.js"></script>
    <script type='text/javascript'>
        //en4.core.runonce.add(function(){
            //FB.init({appId: '<?php echo $settings->getSetting('inviter.facebook.consumer.key', false); ?>', xfbml: true, cookie: true});
        //});
    </script>
<?php endif; ?>

<script type='text/javascript'>
en4.core.runonce.add(function()
{
  he_contacts.callback = "inviter.friend_request";
  he_contacts.list_type = 'all';
  he_contacts.init();

  $('inviterContacts').value = '';
});

function skipFormContacts()
{
  document.getElementById("skip").value = "skipFormContacts";
  document.getElementById("invitation_send").submit();
}

function submitFormContacts()
{
  if (he_contacts.contacts.length > 0)
  {
      var provider = '<?php echo $session->__get('provider'); ?>';
      if(provider == 'facebook') {
          //var link = '<?php //$codes_tbl = EngineApi::_()->getDbTable('codes','inviter'); echo $codes_tbl->getReferralLinkByCode($session->__get('invite_code')); ?>';
          //var host = '<?php //echo $host; ?>';
          inviter.send_to_fb_on_signup(link, host);
      }


  	document.getElementById("inviterStep").value = 'inviterFinalize';
    document.getElementById("inviterContacts").value = he_contacts.contacts;
    document.getElementById("invitation_send").submit();
  }
  else
  {
    he_show_message("<?php echo $this->translate('INVITER_No contacts selected!!!'); ?>", 'error');
    return false;
  }
}
</script>

<div class='global_form'>
  <div>
    <div>
      <h3><?php echo $this->translate('INVITER_Invite your friends from your contact list.') ?></h3>
      <?php
        $session = new Zend_Session_Namespace('inviter');
        if ($session->__get('provider') === 'facebook'): ?>
        <p class='form-description'><?php echo $this->translate('INVITER_VIEWS_INDEX_CONTACTSFACEBOOK_DESCRIPTION')?></p>
      <?php else: ?>
        <p class='form-description'><?php echo $this->translate('INVITER_VIEWS_INDEX_CONTACTS_DESCRIPTION')?></p>
      <?php endif; ?>
      <div class="form-elements">
      
        <div class="he_contacts">
          <div class="options">
            <div class="select_btns">
              <a href="javascript:void(0)" class="active" onClick="he_contacts.select('all'); he_contacts.add_class(this, 'active', $$('.select_btns a')[1]); this.blur();">
                  <?php echo $this->translate("All"); ?>
              </a>
              <a href="javascript:void(0)" onClick="he_contacts.select('selected'); he_contacts.add_class(this, 'active', $$('.select_btns a')[0]); this.blur();">
                  <?php echo $this->translate("Selected"); ?>&nbsp;(<span id="selected_contacts_count">0</span>)
              </a>
            </div>
            <div class="contacts_filter">
              <input type="text" id="contacts_filter" class="filter" style='padding-left: 15px;'/>
            </div>
            <div class="clr"></div>
          </div>

          <div style='margin:5px;padding-left:20px;width:480px; padding-bottom:5px'>
            <input type='checkbox' name='select_all_contacs' id='select_all_contacs' 
        onclick="he_contacts.choose_all_contacts($(this)); $('selected_contacts_count').set('text', he_contacts.contacts.length)"/>
            <label for='select_all_contacs'><?php echo $this->translate('INVITER_Select all members') ?></label>
          </div>
          <br/>

          <div class="clr"></div>
        
          <div class="contacts inviter_contacts" style='position: relative;'>
            <div id="inviter_contacts_loading">&nbsp;</div>
            <div id="he_contacts_list" style="text-align:left">
                <?php foreach ($this->form->_contacts as $id=>$contact): ?>
                  <a style='width: 450px; height:25px;' class="item visible item_contact" id="contact_<?php echo $id; ?>" href='javascript://' 
                    onclick="he_contacts.choose_contact('<?php echo $id ?>');
                             if ($(this).hasClass('active')){$('checkbox_<?php echo $id ?>').checked = true;}else{$('checkbox_<?php echo $id ?>').checked = false;} 
                              $('selected_contacts_count').set('text', he_contacts.contacts.length)">
                    <span class='photo' style='background-image: url(); width:20px; margin: 5px;' >
                      <input type='checkbox' id='checkbox_<?php echo $id ?>' name='selected_contacts[]' value='<?php echo $id?>'/>
                    </span>
                    <span class="name" style="width: 400px;"><?php echo $contact['name']?>&lt;<?php echo $contact['email']?>&gt;</span>
                    <div class="clr"></div>
                  </a>
                <?php endforeach; ?>
              <div id="no_result" class="hidden"><?php echo $this->translate("INVITER_There is no contacts."); ?></div>
              <div class="clr" id="he_contacts_end_line"></div>
            </div>
              <div class="clr"></div>
          </div>
          <br/>
          <div class="clr"></div>
          <div class="btn">
          
            <?php echo $this->form->render($this)?>
            <button onclick="submitFormContacts();"><?php echo $this->translate('INVITER_Send Invitations'); ?></button>
            <?php echo $this->translate('or')?> 
            <a href="javascript:void(0);" onclick='javascript:skipFormContacts();'>
              <?php echo $this->translate('Skip')?>
            </a>
          </div>
          
        </div>

      </div>        
    </div>
  </div>
</div>