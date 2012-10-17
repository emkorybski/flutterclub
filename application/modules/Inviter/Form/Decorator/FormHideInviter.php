<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Inviter
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: FormHideInviter.php 2010-07-02 19:54 mirlan $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Inviter
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Engine_Form_Decorator_FormHideInviter extends Zend_Form_Decorator_Abstract
{
  protected $_placement = null;
  
  public function render($content)
  {
    $translate = Zend_Registry::get('Zend_Translate');
    return $content."or <a href='javascript:void(0);' onclick='javascript:hideForm();'>".$translate->translate('hide')."</a>";
  }
}
