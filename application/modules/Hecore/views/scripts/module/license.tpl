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

<div class="global_form_popup settings form_license_key_error">

  <?php if (isset($this->pluginInstalled) && $this->pluginInstalled) : ?>

  <script type="text/javascript">
    setTimeout(function(){parent.location.href=parent.location.href;parent.Smoothbox.close();}, 3000);
  </script>
  <div style="padding:20px;">
    <h3><?php echo $this->translate('Provided License key is correct.  The Module has been successfully installed.')?></h3>
  </div>

  <?php else: ?>

  <?php
  $form_description = $this->translate('Please update your license key <a href="http://www.hire-experts.com" target="_blank">here</a>');
  $form_description = Zend_Json::encode($form_description);
  ?>
  <script type="text/javascript">
    window.addEvent('domready', function(){
      var $description = $$('.form-description');
      var $error = $$('.global_form .errors li');

      if ($description.length != 0) {
        $description.set('html', <?php echo $form_description; ?>);
      }

      if ($error.length != 0) {
        $error.set('html', $error.get('text'));
      }

      var $form = $$('.global_form')[0];
      $form.getElement('.form-errors').addClass('display_none');

      window.hecore_form_submitted = false;
      $form.addEvent('submit', function() {
        if (window.hecore_form_submitted) {
          return false;
        }

        window.hecore_form_submitted = true;

        $form.getChildren()[0].addClass('hecore_check_license_loader');
        var form_values = $form.toQueryString();
        form_values += '&format=json&random=' + Math.random();
        new Request.JSON({
          url: "<?php echo $this->url(array('module' => 'hecore', 'controller' => 'module', 'action' => 'license'), 'default'); ?>",
          method: 'post',
          data: form_values,
          onSuccess: function(response) {
            window.hecore_form_submitted = false;
            $form.getChildren()[0].removeClass('hecore_check_license_loader');
            if (response.result == 'failed') {
              $form.getElement('.form-errors').removeClass('display_none');
              $form.getElement('.form-errors .errors li').set('html', response.message);
            } else {
              window.location.reload();
            }
          },
          onFailure: function(data) {
            window.hecore_form_submitted = false;
            $form.getChildren()[0].removeClass('hecore_check_license_loader');
            try { eval('var response = ' + data.responseText); }
            catch (e) { var response = {result: 'failed'}; }

            if (response.result == 'failed') {
              $form.getElement('.form-errors').removeClass('display_none');
              $form.getElement('.form-errors .errors li').set('html', response.message);
            } else {
              window.location.reload();
            }
          }
        }).send();

        return false;
      });
    });
  </script>

  <?php echo $this->form->render($this); ?>

  <?php endif; ?>

</div>