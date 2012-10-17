<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Inviter
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: SubmitContacts.php 2010-07-02 19:54 mirlan $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Inviter
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Engine_Form_Decorator_SubmitContacts extends Zend_Form_Decorator_Abstract
{
  protected $_placement = null;
  public function render($content)
  {
     $html = "<div style='clear:both;'>".$content."<div class='writer_contacts_loading' id='writer_contacts_loading'>&nbsp;</div></div><div style='clear:both'></div>";

    return $html;
  }
}