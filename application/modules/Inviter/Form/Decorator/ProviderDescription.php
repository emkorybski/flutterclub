<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Inviter
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: DefaultProviders.php 2010-07-02 19:54 mirlan $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Inviter
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Engine_Form_Decorator_ProviderDescription extends Zend_Form_Decorator_Abstract
{
  protected $_placement = null;
  
  public function render($content)
  {
    $label = $this->getOption('label');
    $description = $this->getOption('description');

    $html = "<div class='inviter_provider_description'>
      <div class='inviter_provider_label'>{$label}</div>
      <div class='form-wrapper'>{$description}</div>
      {$content}
    </div>";

    return $html;
  }
}