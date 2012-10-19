<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Hecore
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: edit.tpl 2010-07-02 17:53 ermek $
 * @author     Ermek
 */
?>

<div class="global_form_popup settings form_license_key_error">

<?php if (isset($this->keyUpdated) && $this->keyUpdated) : ?>

    <script type="text/javascript">
        setTimeout(function(){parent.location.href=parent.location.href;parent.Smoothbox.close();}, 3000);
    </script>
    <div style="padding:20px;">
        <h3><?php echo $this->translate('Plugin license has been successfully updated.')?></h3>
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
        });
    </script>

    <?php echo $this->form->render($this); ?>

<?php endif; ?>

</div>