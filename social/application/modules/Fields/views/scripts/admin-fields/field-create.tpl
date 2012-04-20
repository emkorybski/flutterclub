<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Fields
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: field-create.tpl 9640 2012-03-07 00:02:49Z richard $
 * @author     John
 */
?>
<script>
  
	function toggle_field(id, id2) {
        var state = document.getElementById(id).style.display;
            if (state == 'block') {
                document.getElementById(id).style.display = 'none';
				document.getElementById(id2).style.display = 'block';
				document.getElementById("map-field-button").setAttribute("class", "admin_button_disabled");
				document.getElementById("create-field-button").setAttribute("class", "");
            } else {
                document.getElementById(id).style.display = 'block';
				document.getElementById(id2).style.display = 'none';
				document.getElementById("create-field-button").setAttribute("class", "admin_button_disabled");
				document.getElementById("map-field-button").setAttribute("class", "");
            }
        }
  
</script>
<?php if( $this->form ): ?>

  <?php
    $this->headScript()
      ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Observer.js');
  ?>

  <div style="padding: 15px 15px 0px 15px;">
    <h3>
      <?php echo $this->translate('Add Profile Question') ?>
    </h3>
  </div>

  <?php if( !empty($this->formAlt) ): ?>
  <div id="select-action" style="padding: 10px 15px 0px 15px">
    <button id="create-field-button" onclick="toggle_field('map-field', 'create-field');">
      <?php echo $this->translate('Create New') ?>
    </button>
    <button id="map-field-button" class="admin_button_disabled" onclick="toggle_field('map-field', 'create-field');">
      <?php echo $this->translate('Duplicate Existing') ?>
    </button>
  </div>
  <?php endif; ?>
  
  <div id="create-field">
    <?php echo $this->form->render($this) ?>
  </div>

  <?php if( !empty($this->formAlt) ): ?>
    <div id="map-field" style="display: none;">
      <?php echo $this->formAlt->render($this) ?>
    </div>
  <?php endif; ?>

<?php else: ?>

  <div class="global_form_popup_message">
    <?php echo $this->translate("Your changes have been saved.") ?>
  </div>

  <script type="text/javascript">
    parent.onFieldCreate(
      <?php echo Zend_Json::encode($this->field) ?>,
      <?php echo Zend_Json::encode($this->htmlArr) ?>
    );
    (function() { parent.Smoothbox.close(); }).delay(1000);
  </script>

<?php endif; ?>