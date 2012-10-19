<?php



/**
 * Radcodes - SocialEngine Module
 *
 * @category   Application_Extensions
 * @package    Article
 * @copyright  Copyright (c) 2009-2010 Radcodes LLC (http://www.radcodes.com)
 * @license    http://www.radcodes.com/license/
 * @version    $Id$
 * @author     Vincent Van <vincent@radcodes.com>
 */
 
 
 
class Article_Widget_TopSubmittersController extends Engine_Content_Widget_Abstract
{

  public function indexAction()
  {
  	
    // Don't render this if not authorized
    $viewer = Engine_Api::_()->user()->getViewer();
  

    $params = array(
      'published' => 1,
      'search' => 1,
      'limit' => $this->_getParam('max', 5),
      'period' => $this->_getParam('period')
    );
    
    $this->view->submitters = Engine_Api::_()->article()->getTopSubmitters($params);
    
    if (empty($this->view->submitters)) {
      return $this->setNoRender();
    }
  }

}