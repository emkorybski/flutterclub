
<script type='text/javascript'>
en4.core.runonce.add(function(){
  he_contacts.callback = "inviter.friend_request";
  he_contacts.list_type = 'all';
  he_contacts.init();
});

function skipFormMembers()
{
  document.getElementById("skip").value = "skipFormMembers";
  $('friend_request').submit();
}

function submitFormMembers()
{
  if (he_contacts.contacts.length > 0)
  {
    document.getElementById("inviterStep").value = 'friendRequest';
    document.getElementById("inviterMembers").value = he_contacts.contacts;
    document.getElementById("friend_request").submit();
  }
  else
  {
    he_show_message(<?php echo $this->jsonInline($this->translate('INVITER_No members specified')); ?>, 'error');
    return false;
  }
  
  
}
</script>

<div class='global_form'>
  <div>
    <div>
      <h3><?php echo $this->translate('INVITER_VIEWS_SCRIPTS_SIGNUP_MEMBERS') ?></h3>
      <p class='form-description'><?php echo $this->translate('INVITER_VIEWS_SIGNUP_MEMBERS_DESCRIPTION')?></p>
      <div class="form-elements">
      
<div id="he_contacts_message" style="display:none;"><div class="msg"></div></div>

<div class="he_contacts">
  <div class="options">
    <div class="select_btns">
      <a href="javascript:void(0)" class="active" onClick="he_contacts.('all'); he_contacts.add_class(this, 'active', $$('._btns a')[1]); this.blur();">
          <?php echo $this->translate("All"); ?>
      </a>
      <a href="javascript:void(0)" onClick="he_contacts.('ed'); he_contacts.add_class(this, 'active', $$('._btns a')[0]); this.blur();">
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
    <label for='select_all_contacs'><?php echo $this->translate('INVITER_Select all members') ?></label>
  </div>
  <br/>
  
  <div class="clr"></div>

  <div class="contacts inviter_contacts" style='position: relative;'>
    <div id="inviter_contacts_loading">&nbsp;</div>
    <div id="he_contacts_list" style="text-align:left">
        <?php foreach ($this->form->_members as $item): ?>
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
      <div id="no_result" class="hidden"><?php echo $this->translate("INVITER_There is no contacts."); ?></div>
      <div class="clr" id="he_contacts_end_line"></div>
    </div>
      <div class="clr"></div>
  </div>
  
  <div class="clr"></div>
  <div class="btn">
    <?php echo $this->form->render($this)?>
    <button onclick="submitFormMembers();"><?php echo $this->translate('Send Request'); ?></button>
    <?php echo $this->translate('or')?> 
    <a href="javascript://" onclick='javascript:skipFormMembers();'>
      <?php echo $this->translate('Skip')?>
    </a>
  </div>
  
</div>

      </div>        
    </div>
  </div>
</div>