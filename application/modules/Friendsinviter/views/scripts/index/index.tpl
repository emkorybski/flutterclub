<?php
  $this->headScript()
    ->appendFile($this->baseUrl() . '/application/modules/Friendsinviter/externals/scripts/friendsinviter.js')
    ->appendFile($this->baseUrl() . '/application/modules/Semods/externals/scripts/semods.js')
?>

<div id="friendsinviter_form">


<?php

    $viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('ViewRenderer');
    if( null !== $viewRenderer && $viewRenderer->view instanceof Zend_View_Interface ) {
      $myview = $viewRenderer->view;
    }
  
?>
    <?php if( count($myview->navigation) ): ?>
    <div class="headline">
      <h2>
        <?php echo $myview->translate('Invite Your Friends');?>
      </h2>
      <div class="tabs">
        <?php
          // Render the menu
          echo $myview->navigation()
            ->menu()
            ->setContainer($myview->navigation)
            ->render();
        ?>
      </div>
    </div>
    <?php endif; ?>


    <?php if ($myview->result != ""): ?>
    <div id="statusmessage" style='margin-left:340px;'>
      <ul class="form-notices"><li><?php echo $myview->translate($myview->result) ?></li></ul>
    </div>
    <?php endif; ?>




    <?php if ($myview->error_message != ""): ?>
    <div id="errormessage" style='margin-left:340px;'>
      <ul class="form-errors"><li><?php echo $myview->translate($myview->error_message) ?></li></ul>
    </div>
    <?php endif; ?>



    <?php if ($myview->captcha_required): ?>
    
    <div id="captcha_required" style="text-align:left">
    
      <form method="post" action="<?php echo $myview->action ?>" name="frm_contacts" id="SignupForm">
        <input type="hidden" name="task" value="<?php if (isset($myview->captcha_task)): ?><?php echo $myview->captcha_task ?><?php else: ?>doinvitefriends<?php endif; ?>">
        <input type="hidden" name="session" value="<?php echo $myview->session ?>">
        <input type="hidden" name="captcha[id]" value='<?php echo $myview->captcha_id ?>'>
        <input type="hidden" name="captcha[input]" value='<?php echo $myview->captcha_input ?>'>
        <input type="hidden"  name="findfriends" value="<?php echo $myview->find_friends ?>">    
        <input type="hidden"  name="nextStep" value="<?php echo $myview->nextStep ?>">    
        <input type="hidden"  name="skip" id="skip" value="">    
        
      <div style="margin-left: 10px;width:500px;"> 
        <h2 style="border-bottom: 1px solid #DDD; text-align: left; padding-bottom: 5px; font-size: 12pt"> <?php echo $myview->translate('100010311') ?> </h2>

        <table class="editor" style="margin: 0px">
          
        <tr class="email">
        <td class="label">&nbsp;</td>
        <td><img src="<?php echo $myview->captcha_url ?>"></td>
        </tr>
    
        <tr>
        <td class="label" Xstyle="width:55px"><?php echo $myview->translate('100010312') ?></td>
        <td style="text-align:left"><input autocomplete="off" type="captcha_response" id="captcha_response" name="captcha_response" class="text" value="" size="30"/></td>
        </tr>

        <tr>
        <td class="label">&nbsp;</td>
        <td>&nbsp;</td>
        </tr>
    
        <tr>
        <td></td>
        <td style="text-align:left">
          <button type="submit" class="button" onclick="this.disabled=true; showLoadingDiv(); this.form.submit();" id="submit_button" name="submit_button"> <?php echo $myview->translate('100010313') ?></button>
          &nbsp;
          <?php if($myview->show_skip_option): ?>
          <?php echo $this->translate('100010195')?> <a href="javascript:void(0);" onclick="javascript:skipForm();"> <?php echo $this->translate('100010168')?> </a>
          <?php else: ?>
          <?php echo $myview->translate('100010195') ?> <a href="<?php echo $myview->url(array('module' => 'core', 'controller' => 'invite'), 'default', true); ?><?php if ($myview->find_friends): ?>?findfriends=1<?php endif; ?>"> <?php echo $myview->translate('100010168') ?> </a>
          <?php endif; ?>
        </td>
        </tr>
        </table>  
        </form>
          
      </div>
        
    </div>

    <?php /* / captcha_required */ ?>
  
  <?php elseif ($myview->importing): ?>
  
    <?php if ($myview->found_friends > 0 ): ?>
    
    <div id="befriender" style="text-align:center">
        
      <div id="befriend_selector" style="margin: 0px auto;width:520px;"> 
        <h2><?php echo $myview->translate('100010190') ?><?php echo $myview->found_friends ?><?php echo $myview->translate('100010191') ?></h2>
        <div class="instructions"><?php echo $myview->translate('100010193') ?></div>
      
        <div id="friends_header">
          <table border="0" cellpadding="0" cellspacing="0">
          <tr>
          <td class="checkbox"><input type="checkbox" class="inputcheckbox" onclick="befriend_toggle_all(true);return false;" id="befriend_toggle_all_checkbox" name="contacts" value="" /></td>
          <td><a href="javascript:void(0);" onclick="befriend_toggle_all();return false;"><?php echo $myview->translate('100010189') ?></a></td>
          </tr>
          </table>
        </div>
      
        <div id="friends_wrapper" style="height: 300px;">
          <table border="0" cellpadding="0" cellspacing="0" id="friends_list">
        
          <?php foreach($myview->friends as $friend) { ?>
        
          <tr>
          <td class="checkbox">
              <input type="checkbox" value="" name="contacts" id="contacts" class="checkbox"/>
              <input type="hidden" value="<?php echo $friend->user_id?>" name="user_id" id="user_id" class="checkbox"/>
          </td>
          <td class="pic hover" onclick="toggle_this_row(this);">
            <?php echo $this->itemPhoto($friend, 'thumb.icon', $friend->getTitle()) ?>
            <?php /*
              <img width="50px" src=""
                  */
            ?>
          </td>
          <td class="name hover" onclick="toggle_this_row(this);">
            <div class="full_name"><?php echo $friend->getTitle() ?> </div>
            <div class="email hover" style="direction: ltr">&lt;<?php echo $friend->email ?>&gt;</div>
          </td>
          <td class="networks hover" onclick="toggle_this_row(this);">

          &nbsp;
          </td>
          </tr>
          
          <?php } ?>
          
          </table>
        </div>
      
        <div class="buttons">
          <button class="button" onclick="add_as_friends(); return false;" id="save" name="save" style="margin-left: 10px"> <?php echo $myview->translate('100010192') ?> </button>
          <?php if($myview->show_skip_option): ?>
          <?php echo $this->translate('100010195')?> <a href="javascript:void(0);" onclick="javascript:skipForm();"> <?php echo $this->translate('100010168')?> </a>
          <?php else: ?>
          <button class="button" onclick="showInviterOrContinue(); return false;" id="cancel" name="cancel"> <?php echo $myview->translate('100010168') ?> </button>
          <?php endif; ?>
        </div>
    
        <script type="text/javascript">
          befriend_toggle_all();
        </script>
    
      </div>
    </div>
    
    
    <?php endif; /* / found_friends */ ?> 
  
    <?php if ($myview->social_contacts ): ?>
    
    <div id="contact_importer" style="text-align:center; <?php if ($myview->found_friends > 0 ): ?>display:none<?php endif; ?>">
    
      <form method="post" action="<?php echo $myview->action ?>" name="frm_contacts" id="<?php if($myview->show_skip_option): ?>SignupForm<?php else: ?>frm_contacts<?php endif;?>">
        <input type="hidden" name="task" value="doinvite">
        <input type="hidden" name="social_contacts" value="1">
        <input type="hidden" name="invite_ids" value="">
        <input type="hidden" name="session" value="<?php echo $myview->session ?>">
        <input type="hidden" name="count" value="">
        <input type="hidden" name="imported" value="0">
        <input type="hidden" name="captcha[id]" value='<?php echo $myview->captcha_id ?>'>
        <input type="hidden" name="captcha[input]" value='<?php echo $myview->captcha_input ?>'>
        <input type="hidden"  name="findfriends" value="<?php echo $myview->find_friends ?>">    
        <input type="hidden" name="invite_message" id="invite_message_social_contacts" value="">
        <input type="hidden"  name="nextStep" value="<?php echo $myview->nextStep ?>">    
        <input type="hidden"  name="skip" id="skip" value="">    
      </form>    
        
      <div style='float:left'>
        
        <div id="social_selector" style="margin: 0px auto;width:520px; float: left"> 
        <h2 style="border-bottom: 1px solid #DDD; width: 500px"><?php echo $myview->translate('100010140') ?>&nbsp;<b><?php echo $myview->unfound_friends ?>&nbsp;<?php echo $myview->translate('100010181') ?>&nbsp;</b><?php echo $myview->translate('100010182') ?></h2>
        <div class="instructions"><?php echo $myview->translate('100010314') ?></div>
      
        <div id="friends_header">
          <table border="0" cellpadding="0" cellspacing="0">
          <tr>
          <td class="checkbox"><input type="checkbox" class="inputcheckbox" onclick="social_toggle_all(true);return false;" id="social_toggle_all_checkbox" name="contacts" value="" /></td>
          <td><a href="javascript:void(0);" onclick="social_toggle_all();return false;"><?php echo $myview->translate('100010315') ?></a></td>
          </tr>
          </table>
        </div>
      
        <div id="friends_wrapper" style="height: 300px;">
          <table border="0" cellpadding="0" cellspacing="0" id="social_contacts_list">
        
          <?php foreach($myview->contacts as $contact) { ?>
        
          <tr>
          <td class="checkbox">
              <input type="checkbox" value="" name="contacts" id="contacts" class="checkbox"/>
              <input type="hidden" value="<?php if(isset($contact['email']) && ($contact['email'] != '')) { echo $contact['email']; } else { echo $contact['uid']; } ?>" name="user_id" id="user_id" class="checkbox"/>
          </td>
          <td class="pic hover" onclick="toggle_this_row(this);">
              <img width="50px" src="<?php echo $contact['pic_square'] ?>"
          </td>
          <td class="name hover" style="width:100%" onclick="toggle_this_row(this);">
            <div class="full_name"><?php echo $contact['name'] ?></div>
          </td>
          </tr>
          
          <?php } ?>
          
          </table>
        </div>
      
        <div class="buttons">
            <button name="save" id="save" onclick="this.disable;ge('invite_message_social_contacts').value=ge('invite_message_social_contacts_textarea').value;save_social_contacts()" class="button"><?php echo $myview->translate('100010167') ?></button>
            <?php if($myview->show_skip_option): ?>
            <?php echo $this->translate('100010195')?> <a href="javascript:void(0);" onclick="javascript:skipForm();"> <?php echo $this->translate('100010168')?> </a>
            <?php else: ?>
            <?php echo $myview->translate('100010195') ?> <a href="<?php echo $myview->url(array('module' => 'core', 'controller' => 'invite'), 'default', true) ?><?php if ($myview->find_friends): ?>?findfriends=1<?php endif; ?>"> <?php echo $myview->translate('100010168') ?> </a>
            <?php endif; ?>
        </div>
    
        <script type="text/javascript">
          social_toggle_all();
        </script>
    
      </div>
    </div>
    
      <div style='float:left; padding-top: 49px; padding-left: 40px; text-align: left'>
        
        <?php echo $myview->translate('100010145') ?>

        <div style='margin-top: 10px'>
          <textarea id='invite_message_social_contacts_textarea' style='height: 137px; width: 300px'><?php echo $myview->invite_message ?></textarea><br>
          <?php echo $myview->translate('100010146') ?>
        </div>
        
      </div>

      <div style='clear: both'></div>

    </div>
    
    <?php /* social_friends */ ?>
    
    <?php else: ?>
    
    <?php /* email contacts */ ?>
    
    <div id="contact_importer" class="contact_importer" <?php if ($myview->found_friends > 0 ): ?> style="display:none" <?php endif; ?>>
      
      <form method="post" action="<?php echo $myview->action ?>" name="frm_contacts" id="<?php if($myview->show_skip_option): ?>SignupForm<?php else: ?>frm_contacts<?php endif;?>">
        <input type="hidden" name="task" value="doinvite">
        <input type="hidden" name="invite_emails" value="">
        <input type="hidden" name="session" value="<?php echo $myview->session ?>">
        <input type="hidden" name="count" value="">
        <input type="hidden" name="imported" value="0">
        <input type="hidden" name="captcha[id]" value='<?php echo $myview->captcha_id ?>'>
        <input type="hidden" name="captcha[input]" value='<?php echo $myview->captcha_input ?>'>
        <input type="hidden"  name="findfriends" value="<?php echo $myview->find_friends ?>">    
        <input type="hidden" name="invite_message" id="invite_message_email_contacts" value="">
        <input type="hidden"  name="nextStep" value="<?php echo $myview->nextStep ?>">    
        <input type="hidden"  name="skip" id="skip" value="">    
      </form>    
      
      <div id="importer_frame" style='float:left'>
      
        <h3><?php echo $myview->translate('100010140') ?>&nbsp;<b><?php echo $myview->unfound_friends?>&nbsp;<?php echo $myview->translate('100010181') ?>&nbsp;</b><?php echo $myview->translate('100010182') ?></h3>
      
        <div id="address_book_selector">
          <div class="instructions"><?php echo $myview->translate('100010165') ?></div>
          <div class="contacts_header" id="email_contacts_header">
            <table cellspacing="0" cellpadding="0" border="0">
            <tr>
            <td class="checkbox"><input type="checkbox" value="" name="contacts" id="toggle_all_checkbox" onclick="toggle_all(true);" class="inputcheckbox"/></td>
            <td><a onclick="toggle_all(); return false;" href="javascript:void(0);"><?php echo $myview->translate('100010166') ?></a></td>
            </tr>
            </table>
          </div>
      
          <div style="height: 250px;" class="contacts_wrapper" id="email_contacts_wrapper">
            <table cellspacing="0" cellpadding="0" border="0" class="contacts_list" id="email_contacts_list">
      
            <?php foreach($myview->contacts as $contact) { ?>
            
            <?php /* <tr class='{cycle values="row0,row1"}'> */ ?>
            <tr class='row0'>
            <td class="checkbox"><input type="checkbox" value="" name="contacts" id="contacts" class="checkbox"/></td>
            <td onclick="toggle_this_row(this)" class="name"><?php echo $contact['name'] ?></td>
            <td onclick="toggle_this_row(this)" class="email">&lt;<?php echo $contact['email'] ?>&gt;</td>
            </tr>
            
            <?php } ?>
      
            </table>
          </div>
      
          <div class="buttons">
            <button name="save" id="save" onclick="this.disable;ge('invite_message_email_contacts').value=ge('invite_message_email_contacts_textarea').value;save_contacts()" class="button"><?php echo $myview->translate('100010167') ?></button>
            <?php if($myview->show_skip_option): ?>
            <?php echo $this->translate('100010195')?> <a href="javascript:void(0);" onclick="javascript:skipForm();"> <?php echo $this->translate('100010168')?> </a>
            <?php else: ?>
            <?php echo $myview->translate('100010195') ?> <a href="<?php echo $myview->url(array('module' => 'core', 'controller' => 'invite'), 'default', true) ?><?php if ($myview->find_friends): ?>?findfriends=1<?php endif; ?>"> <?php echo $myview->translate('100010168') ?> </a>
            <?php endif; ?>
          </div>
      
          <script type="text/javascript">
            toggle_all();
          </script>
        </div>
      </div>
      
      <div style='float:left; padding-top: 42px; padding-left: 40px; text-align: left'>
        
        <?php echo $myview->translate('100010145') ?>

        <div style='margin-top: 10px'>
          <textarea id='invite_message_email_contacts_textarea' style='height: 137px; width: 300px'><?php echo $myview->invite_message ?></textarea><br>
          <?php echo $myview->translate('100010146') ?>
        </div>
        
      </div>

      <div style='clear: both'></div>
      
    </div>
    
    <?php endif; /* / email contacts */ ?>
  
  
    <?php /* / importing */ ?>
 
  <?php else: ?>















    <div style="<?php if ($myview->screen == "webcontacts"): ?>display:block<?php else: ?>display:none<?php endif; ?>" name="friendsinvite" id="friendsinvite">
       
      <table style="margin:0px auto">
      <tr style="vertical-align:top">
      <td>        
        <div class="invite_title"> <?php if (!$myview->find_friends): ?><?php echo $myview->translate('100010154') ?><?php else: ?><?php echo $myview->translate('100010194') ?><?php endif; ?> <br><?php echo $myview->translate('100010169') ?> </div>
        <div class="logos clearfix">
          <table>
           <tr style="vertical-align:middle">
           <td><div class="logo" onclick="logo_onClick(0, 'gmail.com')"><img src="application/modules/Friendsinviter/externals/images/brands/gmail_logo.gif"></div></td>
           <td><div class="logo" onclick="logo_onClick(0, 'yahoo.com')"><img src="application/modules/Friendsinviter/externals/images/brands/yahoo_logo.gif"></div></td>
           <td><div class="logo" onclick="logo_onClick(0, 'hotmail.com')"><img src="application/modules/Friendsinviter/externals/images/brands/hotmail_logo.gif"></div></td>
           <td><div class="logo" onclick="logo_onClick(0, 'aol.com')"><img src="application/modules/Friendsinviter/externals/images/brands/aol_logo.gif"></div></td>
           <td><div class="logo" onclick="logo_onClick(1, 'messenger')"><img src="application/modules/Friendsinviter/externals/images/brands/livemessenger.gif"></div></td>
           </tr>
          </table>
        </div>
    
        <div style="clear:both"></div>
    
        <div style="height: <?php if ($myview->render_captcha): ?>330<?php else: ?>240<?php endif; ?>px">
    
          <div id="importer" class="VEACCORDION">
      
            <div id="box_0" class="<?php if ($myview->provider != 'auto'): ?>VEACCORDIONHEADER<?php else: ?>VEACCORDIONHEADERACTIVE<?php endif; ?>"><table cellspacing="0" cellpadding="0" width="100%"><tr><td style="color: #333"><?php echo $myview->translate('100010170') ?> <span style="color: #BBB"><?php echo $myview->translate('100010174') ?></span> </td> <td style="text-align:right;color: #D0D0D0"> <?php echo $myview->translate('100010178') ?> </td></tr></table> </div>
        
            <div class="<?php if ($myview->provider != 'auto'): ?>VEACCORDIONCONTENT<?php else: ?>VEACCORDIONCONTENTACTIVE<?php endif; ?>">
            
              <form method="post" action="<?php echo $myview->action ?>" name="frm" Xid="frm" id="SignupForm">
                <input type='hidden' name='task' value='doinvitefriends'>
                <input type="hidden"  name="provider" value="auto">    
                <input type="hidden"  name="findfriends" value="<?php echo $myview->find_friends ?>">    
                <input type="hidden"  name="nextStep" value="<?php echo $myview->nextStep ?>">    
                <input type="hidden"  name="skip" id="skip" value="">    
                <table class="editor" style="width:395px">
                <tr class="email">
                <td class="label" style="width:55px"><?php echo $myview->translate('100010155') ?></td>
                <td style="width:290px">
                  <table cellpadding='0' cellspacing='0' style="width:290px">
                  <tr>
                  <td style="width:137px; padding-left: 0px"><input style="width:130px" type="text" id="user" name="user" class="text" value="<?php if ($myview->provider == 'auto'): ?><?php echo $myview->login?><?php endif; ?>" onkeypress="fi_user_onKeyPress(event)"/></td>
                  <td style="padding-left: 5px; padding-right: 5px; width:16px"> @ </td>
                  <td style="width:137px">
                    <div>
                      <select style="width:130px;<?php if ($myview->typed_domain != ''): ?>display:none;<?php endif; ?>" name=domain id=domain class="select" onchange="domain_onChange(this)">
                        
                          <?php foreach($myview->domains as $domain_loop) { ?>
                            <option value='<?php echo $domain_loop ?>'<?php if ($domain_loop == $myview->domain): ?> SELECTED<?php endif; ?>><?php echo $domain_loop ?></option>
                          <?php } ?>
                          
                          <option value="">---</option>
                          <option value=""><?php echo $myview->translate('100010179') ?></option>
                          
                      </select>
        
                      <input style="width:124px; <?php if ($myview->typed_domain == ''): ?>display:none;<?php endif; ?>" type="text" id="domain_type" name="domain_type" class="text"   value="<?php echo $myview->typed_domain ?>" size="20" autocomplete=off onfocus="var source=new custom_source([{t:'aol.com',i:'0'},{t:'fastmail.fm',i:'1'},{t:'fastmail.cn',i:'2'},{t:'fastmail.co.uk',i:'3'},{t:'fastmail.com.au',i:'4'},{t:'fastmail.es',i:'5'},{t:'fastmail.in',i:'6'},{t:'fastmail.jp',i:'7'},{t:'fastmail.net',i:'8'},{t:'fastmail.to',i:'9'},{t:'fastmail.us',i:'10'},{t:'123mail.org',i:'11'},{t:'airpost.net',i:'12'},{t:'eml.cc',i:'13'},{t:'fmail.co.uk',i:'14'},{t:'fmgirl.com',i:'15'},{t:'fmguy.com',i:'16'},{t:'mailbolt.com',i:'17'},{t:'mailcan.com',i:'18'},{t:'mailhaven.com',i:'19'},{t:'mailmight.com',i:'20'},{t:'ml1.net',i:'21'},{t:'mm.st',i:'22'},{t:'myfastmail.com',i:'23'},{t:'proinbox.com',i:'24'},{t:'promessage.com',i:'25'},{t:'rushpost.com',i:'26'},{t:'sent.as',i:'27'},{t:'sent.at',i:'28'},{t:'sent.com',i:'29'},{t:'speedymail.org',i:'30'},{t:'warpmail.net',i:'31'},{t:'xsmail.com',i:'32'},{t:'150mail.com',i:'33'},{t:'150ml.com',i:'34'},{t:'16mail.com',i:'35'},{t:'2-mail.com',i:'36'},{t:'4email.net',i:'37'},{t:'50mail.com',i:'38'},{t:'allmail.net',i:'39'},{t:'bestmail.us',i:'40'},{t:'cluemail.com',i:'41'},{t:'elitemail.org',i:'42'},{t:'emailcorner.net',i:'43'},{t:'emailengine.net',i:'44'},{t:'emailengine.org',i:'45'},{t:'emailgroups.net',i:'46'},{t:'emailplus.org',i:'47'},{t:'emailuser.net',i:'48'},{t:'f-m.fm',i:'49'},{t:'fast-email.com',i:'50'},{t:'fast-mail.org',i:'51'},{t:'fastem.com',i:'52'},{t:'fastemail.us',i:'53'},{t:'fastemailer.com',i:'54'},{t:'fastest.cc',i:'55'},{t:'fastimap.com',i:'56'},{t:'fastmailbox.net',i:'57'},{t:'fastmessaging.com',i:'58'},{t:'fea.st',i:'59'},{t:'fmailbox.com',i:'60'},{t:'ftml.net',i:'61'},{t:'h-mail.us',i:'62'},{t:'hailmail.net',i:'63'},{t:'imap-mail.com',i:'64'},{t:'imap.cc',i:'65'},{t:'imapmail.org',i:'66'},{t:'inoutbox.com',i:'67'},{t:'internet-e-mail.com',i:'68'},{t:'internet-mail.org',i:'69'},{t:'internetemails.net',i:'70'},{t:'internetmailing.net',i:'71'},{t:'jetemail.net',i:'72'},{t:'justemail.net',i:'73'},{t:'letterboxes.org',i:'74'},{t:'mail-central.com',i:'75'},{t:'mail-page.com',i:'76'},{t:'mailandftp.com',i:'77'},{t:'mailas.com',i:'78'},{t:'mailc.net',i:'79'},{t:'mailforce.net',i:'80'},{t:'mailftp.com',i:'81'},{t:'mailingaddress.org',i:'82'},{t:'mailite.com',i:'83'},{t:'mailnew.com',i:'84'},{t:'mailsent.net',i:'85'},{t:'mailservice.ms',i:'86'},{t:'mailup.net',i:'87'},{t:'mailworks.org',i:'88'},{t:'mymacmail.com',i:'89'},{t:'nospammail.net',i:'90'},{t:'ownmail.net',i:'91'},{t:'petml.com',i:'92'},{t:'postinbox.com',i:'93'},{t:'postpro.net',i:'94'},{t:'realemail.net',i:'95'},{t:'reallyfast.biz',i:'96'},{t:'reallyfast.info',i:'97'},{t:'speedpost.net',i:'98'},{t:'ssl-mail.com',i:'99'},{t:'swift-mail.com',i:'100'},{t:'the-fastest.net',i:'101'},{t:'the-quickest.com',i:'102'},{t:'theinternetemail.com',i:'103'},{t:'veryfast.biz',i:'104'},{t:'veryspeedy.net',i:'105'},{t:'yepmail.net',i:'106'},{t:'your-mail.com',i:'107'},{t:'fusemail.com',i:'108'},{t:'gmail.com',i:'109'},{t:'hotmail.com',i:'110'},{t:'live.com',i:'111'},{t:'inbox.com',i:'112'},{t:'indiatimes.com',i:'113'},{t:'lycos.com',i:'114'},{t:'mail.ru',i:'115'},{t:'inbox.ru',i:'116'},{t:'list.ru',i:'117'},{t:'bk.ru',i:'118'},{t:'myway.com',i:'119'},{t:'nana10.co.il',i:'120'},{t:'nana.co.il',i:'121'},{t:'coolmail.co.il',i:'122'},{t:'graffiti.net',i:'123'},{t:'rediff.com',i:'124'},{t:'rediffmail.com',i:'125'},{t:'walla.com',i:'126'},{t:'walla.co.il',i:'127'},{t:'yahoo.com',i:'128'},{t:'yandex.com',i:'129'},{t:'yandex.ru',i:'130'},{t:'mail.com',i:'131'},{t:'email.com',i:'132'},{t:'iname.com',i:'133'},{t:'cheerful.com',i:'134'},{t:'consultant.com',i:'135'},{t:'europe.com',i:'136'},{t:'mindless.com',i:'137'},{t:'earthling.net',i:'138'},{t:'myself.com',i:'139'},{t:'post.com',i:'140'},{t:'techie.com',i:'141'},{t:'usa.com',i:'142'},{t:'writeme.com',i:'143'},{t:'alumni.com',i:'144'},{t:'alumnidirector.com',i:'145'},{t:'graduate.org',i:'146'},{t:'berlin.com',i:'147'},{t:'dallasmail.com',i:'148'},{t:'delhimail.com',i:'149'},{t:'dublin.com',i:'150'},{t:'london.com',i:'151'},{t:'madrid.com',i:'152'},{t:'moscowmail.com',i:'153'},{t:'munich.com',i:'154'},{t:'nycmail.com',i:'155'},{t:'paris.com',i:'156'},{t:'rome.com',i:'157'},{t:'sanfranmail.com',i:'158'},{t:'singapore.com',i:'159'},{t:'tokyo.com',i:'160'},{t:'torontomail.com',i:'161'},{t:'australiamail.com',i:'162'},{t:'brazilmail.com',i:'163'},{t:'chinamail.com',i:'164'},{t:'germanymail.com',i:'165'},{t:'indiamail.com',i:'166'},{t:'irelandmail.com',i:'167'},{t:'israelmail.com',i:'168'},{t:'italymail.com',i:'169'},{t:'japan.com',i:'170'},{t:'koreamail.com',i:'171'},{t:'mexicomail.com',i:'172'},{t:'polandmail.com',i:'173'},{t:'russiamail.com',i:'174'},{t:'scotlandmail.com',i:'175'},{t:'spainmail.com',i:'176'},{t:'swedenmail.com',i:'177'},{t:'angelic.com',i:'178'},{t:'atheist.com',i:'179'},{t:'minister.com',i:'180'},{t:'muslim.com',i:'181'},{t:'oath.com',i:'182'},{t:'orthodox.com',i:'183'},{t:'priest.com',i:'184'},{t:'protestant.com',i:'185'},{t:'reborn.com',i:'186'},{t:'religious.com',i:'187'},{t:'saintly.com',i:'188'},{t:'artlover.com',i:'189'},{t:'bikerider.com',i:'190'},{t:'birdlover.com',i:'191'},{t:'catlover.com',i:'192'},{t:'collector.org',i:'193'},{t:'comic.com',i:'194'},{t:'cutey.com',i:'195'},{t:'disciples.com',i:'196'},{t:'doglover.com',i:'197'},{t:'elvisfan.com',i:'198'},{t:'fan.com',i:'199'},{t:'fan.net',i:'200'},{t:'gardener.com',i:'201'},{t:'hockeymail.com',i:'202'},{t:'madonnafan.com',i:'203'},{t:'musician.org',i:'204'},{t:'petlover.com',i:'205'},{t:'reggaefan.com',i:'206'},{t:'rocketship.com',i:'207'},{t:'rockfan.com',i:'208'},{t:'thegame.com',i:'209'},{t:'cyberdude.com',i:'210'},{t:'cybergal.com',i:'211'},{t:'cyber-wizard.com',i:'212'},{t:'webname.com',i:'213'},{t:'who.net',i:'214'},{t:'accountant.com',i:'215'},{t:'adexec.com',i:'216'},{t:'allergist.com',i:'217'},{t:'archaeologist.com',i:'218'},{t:'bartender.net',i:'219'},{t:'brew-master.com',i:'220'},{t:'chef.net',i:'221'},{t:'chemist.com',i:'222'},{t:'clerk.com',i:'223'},{t:'columnist.com',i:'224'},{t:'contractor.net',i:'225'},{t:'counsellor.com',i:'226'},{t:'count.com',i:'227'},{t:'deliveryman.com',i:'228'},{t:'diplomats.com',i:'229'},{t:'doctor.com',i:'230'},{t:'dr.com',i:'231'},{t:'engineer.com',i:'232'},{t:'execs.com',i:'233'},{t:'financier.com',i:'234'},{t:'fireman.net',i:'235'},{t:'footballer.com',i:'236'},{t:'geologist.com',i:'237'},{t:'graphic-designer.com',i:'238'},{t:'hairdresser.net',i:'239'},{t:'instructor.net',i:'240'},{t:'insurer.com',i:'241'},{t:'journalist.com',i:'242'},{t:'lawyer.com',i:'243'},{t:'legislator.com',i:'244'},{t:'lobbyist.com',i:'245'},{t:'mad.scientist.com',i:'246'},{t:'monarchy.com',i:'247'},{t:'optician.com',i:'248'},{t:'orthodontist.net',i:'249'},{t:'pediatrician.com',i:'250'},{t:'photographer.net',i:'251'},{t:'physicist.net',i:'252'},{t:'politician.com',i:'253'},{t:'popstar.com',i:'254'},{t:'presidency.com',i:'255'},{t:'programmer.net',i:'256'},{t:'publicist.com',i:'257'},{t:'radiologist.net',i:'258'},{t:'realtyagent.com',i:'259'},{t:'registerednurses.com',i:'260'},{t:'repairman.com',i:'261'},{t:'representative.com',i:'262'},{t:'rescueteam.com',i:'263'},{t:'salesperson.net',i:'264'},{t:'scientist.com',i:'265'},{t:'secretary.net',i:'266'},{t:'socialworker.net',i:'267'},{t:'sociologist.com',i:'268'},{t:'songwriter.net',i:'269'},{t:'teachers.org',i:'270'},{t:'technologist.com',i:'271'},{t:'therapist.net',i:'272'},{t:'tvstar.com',i:'273'},{t:'umpire.com',i:'274'},{t:'worker.com',i:'275'},{t:'africamail.com',i:'276'},{t:'americamail.com',i:'277'},{t:'arcticmail.com',i:'278'},{t:'asia.com',i:'279'},{t:'asia-mail.com',i:'280'},{t:'californiamail.com',i:'281'},{t:'dutchmail.com',i:'282'},{t:'englandmail.com',i:'283'},{t:'europemail.com',i:'284'},{t:'pacific-ocean.com',i:'285'},{t:'pacificwest.com',i:'286'},{t:'safrica.com',i:'287'},{t:'samerica.com',i:'288'},{t:'swissmail.com',i:'289'},{t:'amorous.com',i:'290'},{t:'caress.com',i:'291'},{t:'couple.com',i:'292'},{t:'feelings.com',i:'293'},{t:'yours.com',i:'294'},{t:'mail.org',i:'295'},{t:'cliffhanger.com',i:'296'},{t:'disposable.com',i:'297'},{t:'doubt.com',i:'298'},{t:'homosexual.net',i:'299'},{t:'hour.com',i:'300'},{t:'instruction.com',i:'301'},{t:'mobsters.com',i:'302'},{t:'nastything.com',i:'303'},{t:'nightly.com',i:'304'},{t:'nonpartisan.com',i:'305'},{t:'null.net',i:'306'},{t:'revenue.com',i:'307'},{t:'royal.net',i:'308'},{t:'sister.com',i:'309'},{t:'snakebite.com',i:'310'},{t:'soon.com',i:'311'},{t:'surgical.net',i:'312'},{t:'theplate.com',i:'313'},{t:'toke.com',i:'314'},{t:'toothfairy.com',i:'315'},{t:'wallet.com',i:'316'},{t:'winning.com',i:'317'},{t:'inorbit.com',i:'318'},{t:'humanoid.net',i:'319'},{t:'weirdness.com',i:'320'},{t:'2die4.com',i:'321'},{t:'activist.com',i:'322'},{t:'aroma.com',i:'323'},{t:'been-there.com',i:'324'},{t:'bigger.com',i:'325'},{t:'comfortable.com',i:'326'},{t:'hilarious.com',i:'327'},{t:'hot-shot.com',i:'328'},{t:'howling.com',i:'329'},{t:'innocent.com',i:'330'},{t:'loveable.com',i:'331'},{t:'playful.com',i:'332'},{t:'poetic.com',i:'333'},{t:'seductive.com',i:'334'},{t:'sizzling.com',i:'335'},{t:'tempting.com',i:'336'},{t:'tough.com',i:'337'},{t:'whoever.com',i:'338'},{t:'witty.com',i:'339'},{t:'alabama.usa.com',i:'340'},{t:'alaska.usa.com',i:'341'},{t:'arizona.usa.com',i:'342'},{t:'arkansas.usa.com',i:'343'},{t:'california.usa.com',i:'344'},{t:'colorado.usa.com',i:'345'},{t:'connecticut.usa.com',i:'346'},{t:'delaware.usa.com',i:'347'},{t:'florida.usa.com',i:'348'},{t:'georgia.usa.com',i:'349'},{t:'hawaii.usa.com',i:'350'},{t:'idaho.usa.com',i:'351'},{t:'illinois.usa.com',i:'352'},{t:'indiana.usa.com',i:'353'},{t:'iowa.usa.com',i:'354'},{t:'kansas.usa.com',i:'355'},{t:'kentucky.usa.com',i:'356'},{t:'louisiana.usa.com',i:'357'},{t:'maine.usa.com',i:'358'},{t:'maryland.usa.com',i:'359'},{t:'massachusetts.usa.com',i:'360'},{t:'michigan.usa.com',i:'361'},{t:'minnesota.usa.com',i:'362'},{t:'mississippi.usa.com',i:'363'},{t:'missouri.usa.com',i:'364'},{t:'montana.usa.com',i:'365'},{t:'nebraska.usa.com',i:'366'},{t:'nevada.usa.com',i:'367'},{t:'newhampshire.usa.com',i:'368'},{t:'newjersey.usa.com',i:'369'},{t:'newmexico.usa.com',i:'370'},{t:'newyork.usa.com',i:'371'},{t:'northcarolina.usa.com',i:'372'},{t:'northdakota.usa.com',i:'373'},{t:'ohio.usa.com',i:'374'},{t:'oklahoma.usa.com',i:'375'},{t:'oregon.usa.com',i:'376'},{t:'pennsylvania.usa.com',i:'377'},{t:'rhodeisland.usa.com',i:'378'},{t:'southcarolina.usa.com',i:'379'},{t:'southdakota.usa.com',i:'380'},{t:'tennessee.usa.com',i:'381'},{t:'texas.usa.com',i:'382'},{t:'utah.usa.com',i:'383'},{t:'vermont.usa.com',i:'384'},{t:'virginia.usa.com',i:'385'},{t:'washington.usa.com',i:'386'},{t:'westvirginia.usa.com',i:'387'},{t:'wisconsin.usa.com',i:'388'},{t:'wyoming.usa.com',i:'389'}]);source.text_placeholder='<?php echo $myview->translate('100010171') ?>';source.text_nomatch='<?php echo $myview->translate('100010172') ?>';source.text_noinput='<?php echo $myview->translate('100010173') ?>';var ac=new autocompleter(this, source);"/>
        
                    </div>
                  </td>
                  </tr>
                  </table>
                </td>
                </tr>
          
                <tr class="subrow">
                <td></td>
                <td><div style="font-size:11px;line-height:normal;" class="tips"></div></td>
                </tr>
          
                <tr>
                <td class="label" style="width:55px"><?php echo $myview->translate('100010157') ?></td>
                <td style="width:290px"><input style="width:282px" type="password" id="pass" name="pass" class="text" value=""/></td>
                </tr>
          
                <tr class="subrow">
                <td></td>
                <td><div style="font-size:11px;line-height:normal;" class="tips"></div></td>
                </tr>
          
                <?php if ($myview->render_captcha): ?>

                <script>
                var fi_secure_image_required = 1;
                </script>
          
                <tr>
                <td class='label'>&nbsp;</td>
                <td Xclass='form2' style="padding-left: 0px">
                  <table cellpadding='0' cellspacing='0'>
                  <tr>
                  <td><input id='fi_secure_id_2' type="hidden" name="captcha[id]" value=''><input type='text' name='captcha[input]' class='text' size='6' maxlength='10'>&nbsp;</td>
                  <td><img id='fi_secure_image_2' border='0' Xheight='20' Xwidth='67' class='signup_code'>&nbsp;&nbsp;</td>
                  <td><img src='application/modules/Friendsinviter/externals/images/icons/tip.gif' border='0' class='Tips1' title='<?php echo str_replace("'","&#039;",$myview->translate('100010151')) ?>'></td>
                  </tr>
                  </table>
                </td>
                </tr>
              
                <tr class="subrow">
                <td></td>
                <td><div style="font-size:11px;line-height:normal;" class="tips"></div></td>
                </tr>
                
                <?php endif; ?>
          
          
                <tr>
                <td></td>
                <td>
                  <button type="submit" class="button" onclick="this.disabled=true; showLoadingDiv(); this.form.submit();" id="submit_button" name="submit_button"><?php if ($myview->find_friends): ?><?php echo $myview->translate('100010022') ?><?php else: ?><?php echo $myview->translate('100010160') ?><?php endif; ?></button>
                  <?php if($myview->show_skip_option): ?>  <?php echo $this->translate('or')?> <a href="javascript:void(0);" onclick="javascript:skipForm();"> <?php echo $this->translate('skip')?> </a> <?php endif; ?>
                </td>
                </tr>
                </table>  
              </form>
            </div>
      
            <div id="box_1" class="<?php if ($myview->provider == 'auto'): ?>VEACCORDIONHEADER<?php else: ?>VEACCORDIONHEADERACTIVE<?php endif; ?>"><table cellspacing="0" cellpadding="0" width="100%"><tr><td style="color: #333"><?php echo $myview->translate('100010161') ?></td><td style="text-align:right;color: #D0D0D0"><?php echo $myview->translate('100010178') ?></td></tr></table></div>
            
            <div class="<?php if ($myview->provider == 'auto'): ?>VEACCORDIONCONTENT<?php else: ?>VEACCORDIONCONTENTACTIVE<?php endif; ?>" style="position:relative">
            
              <form method="post" action="<?php echo $myview->action ?>" name="frm" Xid="frm" id="SignupForm">
                <input type='hidden' name='task' value='doinvitefriends'>
                <input type="hidden"  name="findfriends" value="<?php echo $myview->find_friends ?>">    
                <input type="hidden"  name="nextStep" value="<?php echo $myview->nextStep ?>">    
                <input type="hidden"  name="skip" id="skip" value="0">    
              
                <table class="editor">
                  
                <tr class="email">
                <td class="label"><?php echo $myview->translate('100010155') ?></td>
                <td><input type="text" id="user" name="user" class="text"   value="<?php if ($myview->provider != 'auto'): ?><?php echo $myview->login ?><?php endif; ?>" size="30"/></td>
                </tr>
                
                <tr class="subrow">
                <td></td>
                <td><div style="font-size:11px;line-height:normal;" class="tips"><?php echo $myview->translate('100010156') ?></div></td>
                </tr>
            
                <tr>
                <td class="label" style="width:55px"><?php echo $myview->translate('100010157') ?></td>
                <td><input type="password" id="pass" name="pass" class="text"   value="" size="30"/></td>
                </tr>
            
                <tr class="subrow">
                <td></td>
                <td><div style="font-size:11px;line-height:normal;" class="tips"></div></td></tr>
            
                <tr>
                <td class="label"><?php echo $myview->translate('100010159') ?></td>
                <td>
                  <select style="width:184px;" name=provider id=provider class="select">
                    
                      <?php foreach($myview->services as $service) { ?>
                      <?php if ($service['e']): ?>
                        <option value='<?php echo $service['n']?>'<?php if ($service['n'] == $myview->provider): ?> SELECTED<?php endif; ?>><?php echo $service['d']?></option>
                      <?php endif; ?>
                      <?php } ?>
                      
                  </select>
                </td>
                </tr>
            
                <tr class="subrow">
                <td></td>
                <td><div style="font-size:11px;line-height:normal;" class="tips"></div></td></tr>
            
                <?php if ($myview->render_captcha): ?>

                <script>
                var fi_secure_image_required = 1;
                </script>
                
                <tr>
                <td class='label'>&nbsp;</td>
                <td class='form2' style="padding-left: 0px">
                  <table cellpadding='0' cellspacing='0'>
                  <tr>
                  <td><input id='fi_secure_id_3' type="hidden" name="captcha[id]" value=''><input type='text' name='captcha[input]' class='text' size='6' maxlength='10'>&nbsp;</td>
                  <td><img id='fi_secure_image_3' src='' border='0' Xheight='20' Xwidth='67' class='signup_code'>&nbsp;&nbsp;</td>
                  <td><img src='application/modules/Friendsinviter/externals/images/icons/tip.gif' border='0' class='Tips1' title='<?php echo str_replace("'","&#039;",$myview->translate('100010151')) ?>'></td>
                  </tr>
                  </table>
                </td>
                </tr>
              
                <tr class="subrow">
                <td></td>
                <td><div style="font-size:11px;line-height:normal;" class="tips"></div></td>
                </tr>
                
                <?php endif; ?>
            
                <tr>
                <td></td>
                <td>
                  <button type="submit" onclick="this.disabled=true; showLoadingDiv(); this.form.submit();" id="submit_button" name="submit_button"><?php if ($myview->find_friends): ?><?php echo $myview->translate('100010022') ?><?php else: ?><?php echo $myview->translate('100010160') ?><?php endif; ?></button>
                  <?php if($myview->show_skip_option): ?>  <?php echo $this->translate('or')?> <a href="javascript:void(0);" onclick="javascript:skipForm();"> <?php echo $this->translate('skip')?> </a> <?php endif; ?>
                </td>
                </tr>
                </table>  
              </form>
            </div>
            
          </div>
    
          
          <script type="text/javascript">
            jcl.LoadBehaviour("importer", AccordionBehaviour);
          </script>
      
          <div class="logos clearfix">
            <table>
             <tr style="vertical-align:middle">

              <?php foreach ($myview->services as $service_logo) { ?>
              <?php if ($service_logo['e'] && $service_logo['l']): ?>
               <td><div class="logo" onclick="logo_onClick(1, '<?php echo $service_logo['n']?>')"><img src="application/modules/Friendsinviter/externals/images/brands/<?php echo $service_logo['l']?>"></div></td>
              <?php endif; ?>
              <?php } ?>

             </tr>
            </table>
          </div>

        </div>  <!-- wrapper div -->
    
        <br>
            
        <div>
        <?php if (!$myview->find_friends): ?>
        <a href="javascript:void(0)" onclick="switchBetweenInviteDivs();textarea_autogrow('invite_message');return false;"><?php echo $myview->translate('100010177') ?></a>
        <span style="padding-left: 5px; padding-right: 5px; color: #DDD">|</span>
        <?php endif; ?>
        <a href="javascript:void(0)" onclick="fi_uploadcsv(); return false;"><?php echo $myview->translate('100010336') ?></a>
        </div>
        
      </td>
      </tr>
      </table>
    </div>




    <div style="<?php if ($myview->screen == "manual"): ?>display:block<?php else: ?>display:none<?php endif; ?>" name="manualinvite" id="manualinvite">
    
      <div style="display:none">
        <table>
        <tr id="emailrow" name="emailrow">
        <td class='form1'><?php echo $myview->translate('100010143') ?></td>
        <td class='form2'> <input name='invite_emails[]' value='' type="text" class="text" onblur="validateEmail(this)"> </td>
        </tr>
        </table>
      </div>
        
      <form action="<?php echo $myview->action ?>" method='POST'>
        <input type='hidden' name='task' value='doinvite'>
        <input type='hidden' name='screen' value='manual'>
        <input type="hidden"  name="nextStep" value="<?php echo $myview->nextStep ?>">    
        <input type="hidden"  name="skip" id="skip" value="0">    
    
        <table cellpadding='0' cellspacing='0'>
        <tr style="vertical-align:top">
        <td>
          <table cellpadding='0' cellspacing='0'>

      
          <?php $_akjsdai = 0; ?>
          <?php do { ?>
          <tr>
          <td class='form1'><?php echo $myview->translate('100010143') ?></td>
          <td class='form2'> <input name="invite_emails[]" value="<?php echo isset($myview->invite_emails[$_akjsdai]) ? $myview->invite_emails[$_akjsdai] : '' ?>" type="text" class="text" onblur="validateEmail(this)"></td>
          </tr>
          <?php } while(isset($myview->invite_emails[$_akjsdai++]) || ($_akjsdai < 5)) ?>
          
          <tr id="addmorerow" name="addmorerow">
          <td class='form1'></td>
          <td class='form2'> <a href="javascript:void(0)" onclick="addEmailRow(); return false;"> <?php echo $myview->translate('100010180') ?> </a> </td>
          </tr>
          
          <tr>
          <td class='form1'>&nbsp;</td>
          <td class='form2'><button type='submit' class='button'><?php echo $myview->translate('100010147') ?></button></td>
          </tr>
          </table>
        </td>
        <td style="padding-left: 40px;vertical-align:top">
          <table cellpadding='0' cellspacing='0'>
          <tr>
          <td class='form1'><?php echo $myview->translate('100010145') ?></td>
          <td class='form2'> 
              <textarea id='invite_message' name='invite_message' rows='5' cols='60'><?php echo $myview->invite_message ?></textarea><br>
              <?php echo $myview->translate('100010146') ?>
          </td>
          </tr>
          
          <?php if ($myview->render_captcha): ?>

          <script>
          var fi_secure_image_required = 1;
          </script>
          
          <tr>
          <td class='form1'>&nbsp;</td>
          <td class='form2'>
            <table cellpadding='0' cellspacing='0'>
            <tr>
            <td><input id='fi_secure_id_1' type="hidden" name="captcha[id]" value=''><input type='text' name='captcha[input]' class='text' size='6' maxlength='10'>&nbsp;</td>
            <td><img id='fi_secure_image_1' src='' border='0' Xheight='20' Xwidth='67' class='signup_code'>&nbsp;&nbsp;</td>
            <td><img src='application/modules/Friendsinviter/externals/images/icons/tip.gif' border='0' class='Tips1' title='<?php echo str_replace("'","&#039;",$myview->translate('100010151')) ?>'></td>
            </tr>
            </table>
          </td>
          </tr>
          
          <?php endif; ?>
          
          </table>
        </td>
        </tr>
        </table>
      </form>
    
      <br>
      <a href="javascript:void(0)" onclick="switchBetweenInviteDivs();return false;"><?php echo $myview->translate('100010176') ?></a>
    
    </div>






    <div style="display:none" name="uploadcsv" id="uploadcsv">
        
      <div style="width: 450px; margin: 0px auto">
          
        <form action="<?php echo $myview->action ?>" method='post' enctype='multipart/form-data'>
        <input type='hidden' name='task' value='douploadcsv'>
        <input type="hidden"  name="findfriends" value="{$find_friends}">    
        <input type="hidden"  name="nextStep" value="<?php echo $myview->nextStep ?>">    
        <input type="hidden"  name="skip" id="skip" value="0">    
    
        <div style="padding-bottom: 10px">
          <?php echo $myview->translate('100010337') ?>
        </div>

        <div>
        <input type='file' name='csvfile' class='text'>
        <br><br>

        <?php if ($myview->render_captcha): ?>

          <script>
          var fi_secure_image_required = 1;
          </script>
        
          <table cellpadding='0' cellspacing='0'>
          <tr>
          <td><input id='fi_secure_id_4' type="hidden" name="captcha[id]" value=''><input type='text' name='captcha[input]' class='text' size='6' maxlength='10'>&nbsp;</td>
          <td><img id='fi_secure_image_4' src='' border='0' Xheight='20' Xwidth='67' class='signup_code'>&nbsp;&nbsp;</td>
          <td><img src='application/modules/Friendsinviter/externals/images/icons/tip.gif' border='0' class='Tips1' title='<?php echo str_replace("'","&#039;",$myview->translate('100010151')) ?>'></td>
          </tr>
          </table>
        
          <br>
        <?php endif; ?>
        
        <button type="submit" Xonclick="this.disabled=true;" name="submit_button"><?php echo $myview->translate('100010338') ?></button>
        </div>
        
        </form>

        <br><br>
          
        <div>
        <?php if (!$myview->find_friends): ?>
        <a href="javascript:void(0)" onclick="fi_manualinvite();return false;"><?php echo $myview->translate('100010177') ?></a>
        <span style="padding-left: 5px; padding-right: 5px; color: #DDD">|</span>
        <?php endif; ?>
        <a href="javascript:void(0)" onclick="fi_import(); return false;"><?php echo $myview->translate('100010176') ?></a>
        </div>

      </div>
    
    
    </div>


  <?php endif; /* importing */ ?> 

    <div id="loading" name="loading" style="display:none;text-align:center; margin: 50px auto">
      <?php echo $myview->translate('100010175') ?> <br><br>
      <div id="progressimage" name="progressimage" style="width:225; height:40; background-repeat:no-repeat; background-position:bottom;">&nbsp;</div>
    </div>
  
  <?php /* Preload Progress Image */ ?>
  
  <script type="text/javascript">
    var progressImage = new Image(220,19); 
    progressImage.src = "application/modules/Friendsinviter/externals/images/invite_progressbar.gif";

    <?php if($myview->render_captcha): ?>
    <?php $this->form->getElement('captcha')->render($this); ?>
  
    // security image    
    if(typeof fi_secure_image_required != 'undefined') {
      var fi_secure_image = new Image(); 
      fi_secure_image.onload = function() {
        ge('fi_secure_image_1') ? ge('fi_secure_image_1').src=fi_secure_image.src:0;
        ge('fi_secure_image_2') ? ge('fi_secure_image_2').src=fi_secure_image.src:0;
        ge('fi_secure_image_3') ? ge('fi_secure_image_3').src=fi_secure_image.src:0;
        ge('fi_secure_image_4') ? ge('fi_secure_image_4').src=fi_secure_image.src:0;

        ge('fi_secure_id_1') ? ge('fi_secure_id_1').value=fi_secure_id:0;
        ge('fi_secure_id_2') ? ge('fi_secure_id_2').value=fi_secure_id:0;
        ge('fi_secure_id_3') ? ge('fi_secure_id_3').value=fi_secure_id:0;
        ge('fi_secure_id_4') ? ge('fi_secure_id_4').value=fi_secure_id:0;
      }
      fi_secure_image.src = "<?php echo $this->form->getElement('captcha')->getCaptcha()->getImgUrl() . $this->form->getElement('captcha')->getValue() . $this->form->getElement('captcha')->getCaptcha()->getSuffix()?>";
      var fi_secure_id = "<?php echo $this->form->getElement('captcha')->getValue() ?>";
    }
    
    <?php endif; ?>
    
  </script>






</div>

