<?php
/**
 * SocialEngineMods
 *
 * @category   Application_Extensions
 * @package    Friendsinviter
 * @copyright  Copyright 2006-2010 SocialEngineMods
 * @license    http://www.socialenginemods.net/license/
 */
class Friendsinviter_Bootstrap extends Engine_Application_Bootstrap_Abstract
{

  public function __construct($application) {
    
    parent::__construct($application);
    
    $frontController = Zend_Controller_Front::getInstance();
    
    $frontController->registerPlugin( new Friendsinviter_Plugin_Referrer() );
       
    
  }
  
}