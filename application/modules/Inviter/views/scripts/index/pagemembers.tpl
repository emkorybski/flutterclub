<?php if (!empty($this->facebook_error) && $this->facebook_error) { ?>

<div id="fb-root"></div>

<script type="text/javascript">
  var appId = <?php echo $this->jsonInline($this->appId); ?>;
  window.fbAsyncInit = function() {
    FB.init({
      appId  : appId,
      status : true, // check login status
      cookie : true, // enable cookies to allow the server to access the session
      xfbml  : false  // parse XFBML
    });
    FB.logout(function(){
      window.location.reload();
    });
  };
  (function() {
    var e = document.createElement('script'); e.async = true;
    e.src = document.location.protocol + '//connect.facebook.net/<?php echo $this->getFacebookLocale() ?>/all.js';
    document.getElementById('fb-root').appendChild(e);
  }());
</script>

<?php } else { ?>

<script type='text/javascript'>
en4.core.runonce.add(function()
{
  he_contacts.callback = "inviter.friend_request";
  he_contacts.list_type = 'all';
  he_contacts.init();
});
</script>

<?php
//   $viewer = Engine_Api::_()->user()->getViewer();
//if (!$this->form->_signup && $viewer->getIdentity()): ?>
<!--	<div class="headline">-->
<!--	  <div class="tabs">-->
<!--	    --><?php
//	      // Render the menu
//      echo $this->navigation()
//        ->menu()
//        ->setContainer($this->navigation)
//        ->render();
//    	?>
<!--	  </div>-->
<!--	</div>-->
<?php //endif; ?>

<div class='global_form'>
  <div>
    <div>
      <h3><?php echo $this->translate('PAGE_INVITER_VIEWS_SCRIPTS_INDEX_MEMBERS') ?></h3>
      <p class='form-description'><?php echo $this->translate('PAGE_INVITER_VIEWS_SCRIPTS_INDEX_MEMBERS_DESCRIPTION')?></p>
      <div class="form-elements" style="margin-top:0px">
      
<div id="he_contacts_message" style="display:none;"><div class="msg"></div></div>

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
    <input type='checkbox' name='select_all_contacs' id='select_all_contacs' onclick="he_contacts.choose_all_contacts($(this)); $('selected_contacts_count').set('text', he_contacts.contacts.length)"/>
    <label for='select_all_contacs'><?php echo $this->translate('PAGE_INVITER_Select all members') ?></label>
  </div>
	<br/>
	
  <div class="clr"></div>

  <div class="contacts inviter_contacts" style='position: relative;'>
    <div id="inviter_contacts_loading">&nbsp;</div>
    <div id="he_contacts_list" style="text-align:left">
        <?php foreach ($this->members as $item): ?>
          <a class="item visible item_contact" id="contact_<?php echo $item->getIdentity(); ?>" href='javascript://' 
			onclick="he_contacts.choose_contact(<?php echo $item->getIdentity(); ?>); $('selected_contacts_count').set('text', he_contacts.contacts.length)">
            <span class='photo' style='background-image: url()'>
              <?php echo $this->itemPhoto($item, 'thumb.icon'); ?>
              <span class="inner"></span>
            </span>
            <span class="name"><?php echo $item->getTitle(); ?></span>
            <div class="clr"></div>
          </a>
        <?php endforeach; ?>
      <div id="no_result" class="hidden"><?php echo $this->translate("PAGE_INVITER_There is no contacts."); ?></div>
      <div class="clr" id="he_contacts_end_line"></div>
    </div>
      <div class="clr"></div>
  </div>

  <div class="clr"></div>
  <div class="btn">
    <button onclick="inviter.friend_request();"><?php echo $this->translate('Send Request'); ?></button> 
    <?php echo $this->translate('or')?> 
    <a href="<?php echo $this->url(array('module'=>'inviter', 'action'=>'index', 'controller'=>'contacts'), 'default', true); ?>">
      <?php echo $this->translate('Skip')?>
    </a>
  </div>
  
</div>

      </div>        
    </div>
  </div>
</div>

<?php } ?>