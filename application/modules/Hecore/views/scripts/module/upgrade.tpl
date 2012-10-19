<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Hecore
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: license.tpl 2010-07-02 17:53 ermek $
 * @author     Ermek
 */
?>


<script type="text/javascript">
  window.addEvent('domready', function(){
    window.setTimeout(function(){
      new Request.JSON({
        url: "<?php echo $this->url(array('module' => 'hecore', 'controller' => 'module', 'action' => 'upgrade', 'name' => $this->product, 'version' => $this->version, 'target_version' => $this->target_version, 'start' => 1), 'default'); ?>",
        method: 'post',
        data: {'random': Math.random()},
        onSuccess: function(response) {
          window.location.href = "<?php echo $this->url(array('module' => 'hecore', 'controller' => 'module', 'action' => 'license', 'name' => $this->product, 'version' => $this->version, 'target_version' => $this->target_version, 'format' => 'smoothbox'), 'default'); ?>";
        },
        onFailure: function(response) {
          window.location.href = "<?php echo $this->url(array('module' => 'hecore', 'controller' => 'module', 'action' => 'license', 'name' => $this->product, 'version' => $this->version, 'target_version' => $this->target_version, 'format' => 'smoothbox'), 'default'); ?>";
        }
      }).send();
    }, 2000);
  });
</script>

<div class="global_form_popup settings form_license_key_error">
  <div class="global_form">
    <div style="padding:20px; padding-right: 55px;" class="hecore_check_license_loader">
      <h3><?php echo $this->translate('HECORE_License verification is processing. Please wait...')?></h3>
    </div>
  </div>
</div>