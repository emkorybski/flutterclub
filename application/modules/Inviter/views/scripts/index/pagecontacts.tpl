<?php if ($this->provider == 'facebook') : ?>
    <script src="http://connect.facebook.net/en_US/all.js"></script>
    <script type='text/javascript'>
        en4.core.runonce.add(function(){
            FB.init({appId: '<?php echo $this->app_id; ?>', xfbml: true, cookie: true});
        });
    </script>
    <input name="fb-redirect-url" id="fb-redirect-url" type="hidden" value="<?php echo $this->redirect_url; ?>">
    <input name="fb-code" id="fb-code" type="hidden" value="<?php echo $this->invite_code; ?>">
    <input name="fb-caption" id="fb-caption" type="hidden" value="<?php echo $this->translate('INVITER_Join our social network!'); ?>">
    <input name="fb-invitation-url" id="fb-invitation-url" type="hidden" value="<?php echo $this->invitation_url; ?>">
    <input name="fb-host" id="fb-host" type="hidden" value="<?php echo $this->host; ?>">
<?php endif; ?>

<script type='text/javascript'>
en4.core.runonce.add(function()
{
  he_contacts.callback = "inviter.friend_request";
  he_contacts.list_type = 'all';
  he_contacts.init();
});
</script>

<div class='global_form'>
<div class="backlink_wrapper" style="margin-bottom: 5px;">
	<a class="backlink" href="<?php echo $this->url(array('page_id'=>$this->page->page_id), 'page_inviter'); ?>"><?php echo $this->translate('PAGE_INVITER_Back to Providers'); ?></a>
</div><br>
  <div>
    <div>
        
        <?php echo $this->render('_pageTitle.tpl'); ?>

      <h3><?php echo $this->translate('PAGE_INVITER_Invite your friends from your contact list.') ?></h3>
			<?php if ($this->provider === 'facebook'): ?>
      	<p class='form-description'><?php echo $this->translate('PAGE_INVITER_VIEWS_INDEX_CONTACTSFACEBOOK_DESCRIPTION')?></p>
			<?php else: ?>
				<p class='form-description'><?php echo $this->translate('PAGE_INVITER_VIEWS_INDEX_CONTACTS_DESCRIPTION')?></p>
			<?php endif; ?>
      <div class="form-elements" style='margin-top:0px'>
      
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
            <label for='select_all_contacs'><?php echo $this->translate('PAGE_INVITER_Select all members') ?></label>
          </div>
					<br/>

          <div class="clr"></div>


          <div class="contacts inviter_contacts" style='position: relative;'>
            <div id="inviter_contacts_loading">&nbsp;</div>
            <div id="he_contacts_list" style="text-align:left">
                <?php foreach ($this->contacts as $id=>$contact): ?>
                  <a style='width: 450px; height:25px;' class="item visible item_contact" id="contact_<?php echo $id; ?>" href='javascript://' 
                    onclick="he_contacts.choose_contact('<?php echo $id ?>');
                             if ($(this).hasClass('active')){$('checkbox_<?php echo $id ?>').checked = true;}else{$('checkbox_<?php echo $id ?>').checked = false;} 
							 $('selected_contacts_count').set('text', he_contacts.contacts.length)
							 ">
                    <span class='photo' style='background-image: url(); width:20px; margin: 5px;' >
                      <input type='checkbox' id='checkbox_<?php echo $id ?>' name='selected_contacts[]' value='<?php echo $id?>' class='selected_contacts'/>
                    </span>
                    <span class="name" style="width: 400px;"><?php echo $contact['name']?>&lt;<?php echo $contact['email']?>&gt;</span>
                    <div class="clr"></div>
                  </a>
                <?php endforeach; ?>
              <div id="no_result" class="hidden"><?php echo $this->translate("PAGE_INVITER_There is no contacts."); ?></div>
              <div class="clr" id="he_contacts_end_line"></div>
            </div>
              <div class="clr"></div>
          </div>

          <?php if (Engine_Api::_()->getApi('settings', 'core')->getSetting('invite.allowCustomMessage', 1) > 0) : ?>
						<div style="margin: 5px; text-align: center;">
						<textarea style='width: 480px; max-width: 480px' id='message_box'><?php echo $this->translate('PAGE_INVITER_You are being invited to join our social network.'); ?></textarea>
						</div>
          <?php endif; ?>
          
          <div class="clr"></div>
          <div class="btn"><button onclick="inviter.page_id=<?php echo $this->page_id;?>; inviter.page_invitation_send('<?php echo $this->provider; ?>',
            '<?php echo $this->page_id; ?>',
            '<?php echo $this->host_url . $this->url(array('module'=>'inviter', 'controller'=>'index', 'action'=>'invitationsend'), 'default', true)?>');"><?php echo $this->translate('PAGE_INVITER_Send Invitations'); ?></button>
            <?php echo $this->translate('or')?> 
            <a href="<?php echo $this->url(array('module'=>'inviter', 'controller'=>'index', 'action'=>'index'), 'default', true); ?>"><?php echo $this->translate('cancel')?></a>
          </div>
          
        </div>

      </div>        
    </div>
  </div>
</div>