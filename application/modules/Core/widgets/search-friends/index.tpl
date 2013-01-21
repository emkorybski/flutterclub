<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Core
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: index.tpl 9329 2011-09-27 22:55:58Z john $
 * @author     John
 */
?>

<style type="text/css">

	#search-friends-wrapper
	{
	    background:transaprent;
	    width:100%;
	    margin-bottom:-5px;
	   
	    
	}
	
	
	#search-friends-wrapper h3
	{
	border-radius:none;
		background-color:#d40000;
		 -webkit-border-top-left-radius: 7px;
		 -webkit-border-top-right-radius: 7px;
                     -moz-border-top-left-radius: 7px;
		 -moz-border-top-right-radius:7px;    
               border-top-left-radius: 7px;
	       border-top-right-radius: 7px;
	       color:#fff;
	       padding-bottom:10px;
	       
		
	}
	ul#friends_search_form_container
	{
		list-style-type:none;
	}
	
	#friends_search_form_container
	{
		background:#efefef;
		padding:10px;
		margin-top:-5px;
	}
	
	#friends_search_form_container  p
	{
		color:#990000;
		line-height:18px;
		font-weight:bold;
		margin-left:5px;
		margin-bottom:10px;
		
	}
	
	#friends_search_form_container  p span
	{
		color:#990000;
		font-weight:normal;
		margin-top:5px
		
	}
	
	#friends_search_form_container  input
	{
			margin-bottom:10px;
			margin-left:20px;
			
	
	
	#loading
	{ border-color:#000;display:none }


</style>

<div id="search-friends-wrapper">

<h3> Search friends  </h3>

<script src="http://code.jquery.com/jquery-latest.js"></script>
<script>

	jQuery.noConflict();
						
	jQuery(document).ready(function() {
	
	jQuery('#loading').hide();		
	
	jQuery('form#friends_search_form').submit(function(){
	
		// ENTER has '13' keyCode for keydown or keypress
		// var key =  e.keyCode ? e.keyCode : e.which;
			jQuery('#loading').show('fast');

		
	   });
	
	});
						
</script>

    <?php if($this->search_check):?>
    <ul>
      <li id="friends_search_form_container">
      <p> Quick-search your friends that may be on flutterclub. <br/><span>(hit ENTER to submit)</span></p>
        <form id="friends_search_form" action="<?php echo $this->url(array('controller' => 'search'), 'default', true) ?>" method="get">
          <input type='text' class='text suggested' name='query' id='global_search_field' size='20' placeholder="Type name" maxlength='150' alt='<?php echo $this->translate('Search') ?>' />
	  <img id="loading" src="/fc/application/modules/Core/externals/images/loading.gif" border="0" width="16" height="16"/>
        </form>
      </li>
      </ul>
      
    <?php endif;?>

</div>

<script type='text/javascript'>
  var notificationUpdater;

  en4.core.runonce.add(function(){
    if($('global_search_field')){
      new OverText($('global_search_field'), {
        poll: true,
        pollInterval: 500,
        positionOptions: {
          position: ( en4.orientation == 'rtl' ? 'upperRight' : 'upperLeft' ),
          edge: ( en4.orientation == 'rtl' ? 'upperRight' : 'upperLeft' ),
          offset: {
            x: ( en4.orientation == 'rtl' ? -4 : 4 ),
            y: 2
          }
        }
      });
    }

    if($('notifications_markread_link')){
      $('notifications_markread_link').addEvent('click', function() {
        //$('notifications_markread').setStyle('display', 'none');
        en4.activity.hideNotifications('<?php echo $this->string()->escapeJavascript($this->translate("0 Updates"));?>');
      });
    }

    <?php if ($this->updateSettings && $this->viewer->getIdentity()): ?>
    notificationUpdater = new NotificationUpdateHandler({
              'delay' : <?php echo $this->updateSettings;?>
            });
    notificationUpdater.start();
    window._notificationUpdater = notificationUpdater;
    <?php endif;?>
  });


  var toggleUpdatesPulldown = function(event, element, user_id) {
    if( element.className=='updates_pulldown' ) {
      element.className= 'updates_pulldown_active';
      showNotifications();
    } else {
      element.className='updates_pulldown';
    }
  }

  var showNotifications = function() {
    en4.activity.updateNotifications();
    new Request.HTML({
      'url' : en4.core.baseUrl + 'activity/notifications/pulldown',
      'data' : {
        'format' : 'html',
        'page' : 1
      },
      'onComplete' : function(responseTree, responseElements, responseHTML, responseJavaScript) {
        if( responseHTML ) {
          // hide loading icon
          if($('notifications_loading')) $('notifications_loading').setStyle('display', 'none');

          $('notifications_menu').innerHTML = responseHTML;
          $('notifications_menu').addEvent('click', function(event){
            event.stop(); //Prevents the browser from following the link.

            var current_link = event.target;
            var notification_li = $(current_link).getParent('li');

            // if this is true, then the user clicked on the li element itself
            if( notification_li.id == 'core_menu_mini_menu_update' ) {
              notification_li = current_link;
            }

            var forward_link;
            if( current_link.get('href') ) {
              forward_link = current_link.get('href');
            } else{
              forward_link = $(current_link).getElements('a:last-child').get('href');
            }

            if( notification_li.get('class') == 'notifications_unread' ){
              notification_li.removeClass('notifications_unread');
              en4.core.request.send(new Request.JSON({
                url : en4.core.baseUrl + 'activity/notifications/markread',
                data : {
                  format     : 'json',
                  'actionid' : notification_li.get('value')
                },
                onSuccess : function() {
                  window.location = forward_link;
                }
              }));
            } else {
              window.location = forward_link;
            }
          });
        } else {
          $('notifications_loading').innerHTML = '<?php echo $this->string()->escapeJavascript($this->translate("You have no new updates."));?>';
        }
      }
    }).send();
  };

  /*
  function focusSearch() {
    if(document.getElementById('global_search_field').value == 'Search') {
      document.getElementById('global_search_field').value = '';
      document.getElementById('global_search_field').className = 'text';
    }
  }
  function blurSearch() {
    if(document.getElementById('global_search_field').value == '') {
      document.getElementById('global_search_field').value = 'Search';
      document.getElementById('global_search_field').className = 'text suggested';
    }
  }
  */
</script>