<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Core
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: UtilityController.php 9436 2011-10-26 20:21:18Z john $
 * @author     John Boehr <john@socialengine.com>
 */

/**
 * @category   Application_Core
 * @package    Core
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 */
class Core_CrossDomainController extends Core_Controller_Action_Standard
{
  public function indexAction()
  {
    $allowedHosts = array();
    
    // self
    $host = $_SERVER['HTTP_HOST'];
    if( preg_match('/\d+\.\d+\.\d+\.\d+/', $host) ) {
      $allowedHosts[] = $host;
    } else {
      $allowedHosts[] = '*.' . $host;
      $allowedHosts[] = $host;
    }
    
    // For static base url
    $staticBaseUrl = Zend_Registry::get('StaticBaseUrl');
    $parts = parse_url($staticBaseUrl);
    if( !empty($parts['host']) ) {
      $host = $parts['host'];
      if( preg_match('/\d+\.\d+\.\d+\.\d+/', $host) ) {
        $allowedHosts[] = $host;
      } else {
        $allowedHosts[] = '*.' . $host;
        $allowedHosts[] = $host;
      }
    }
    
    // hooks
    $event = Engine_Hooks_Dispatcher::_()->callEvent('onGenerateCrossDomain');
    if( ($r = $event->getResponses()) && 
        is_array($r) ) {
      $allowedHosts += $r;
    }
    
    $this->view->allowedHosts = $allowedHosts;
    
    // options
    $this->_helper->layout()->disableLayout();
    $this->getResponse()->setHeader('Content-Type', 'application/xml');
  }
}