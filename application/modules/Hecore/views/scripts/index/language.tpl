<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Hecore
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: list.tpl 2010-07-02 17:53 ermek $
 * @author     Ermek
 */
?>

<?php
  $langVars = array(
    'Confirm' => $this->translate('Confirm'),
    'Cancel' => $this->translate('Cancel'),
    'or' => $this->translate('or'),
    'close' => $this->translate('close')
  );
?>

en4.core.runonce.add(function(){
  he_add_lang_vars(<?php echo $this->jsonInline($langVars); ?>);
});