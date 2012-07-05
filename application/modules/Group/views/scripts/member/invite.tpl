<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Group
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: invite.tpl 9643 2012-03-10 00:34:52Z john $
 * @author	   John
 */
?>

<script type="text/javascript">
  en4.core.runonce.add(function(){
    $('selectall').addEvent('click', function(event) {
      var el = $(event.target);
      $$('input[type=checkbox]').set('checked', el.get('checked'));
    })
  });
</script>

<?php echo $this->form->setAttrib('class', 'global_form_popup')->render($this) ?>
