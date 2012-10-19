<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Album
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: Photos.php 9747 2012-07-26 02:08:08Z john $
 * @author     Sami
 */

/**
 * @category   Application_Extensions
 * @package    Album
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
class Album_Form_Album_Photos extends Engine_Form
{
  public function init()
  {
    $this
      ->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()))
      ;
    $this->addElement('Radio', 'cover', array(
      'label' => 'Album Cover',
    ));
    $this->addElement('Button', 'submit', array(
      'label' => 'Save Changes',
      'type' => 'submit',
    ));
  }
}