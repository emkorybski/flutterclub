<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Quiz
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: view.tpl 2010-07-02 17:53 ermek $
 * @author     Ermek
 */
?>
<script type="text/javascript">
    function skipFormInviter()
    {
      document.getElementById("skip").value = "skipFormInviter";
      document.getElementById("invite_friends").submit();
    }
</script>
<?php echo $this->render('_providers_settings.tpl'); ?>
<div class='layout_middle'>

  <div style="padding: 10px; padding-top: 5px; width: 750px">
    <?php echo $this->form->render($this)?>
  </div>
  
</div>

<div id='default_provider_list' style='display:none;'></div>