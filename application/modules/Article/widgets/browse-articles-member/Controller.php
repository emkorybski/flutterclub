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
 
 
 
class Article_Widget_BrowseArticlesMemberController extends Engine_Content_Widget_Abstract
{

  public function indexAction()
  {
    $this->view->formValues = $params = $this->getQueryParams();

    $this->view->userObject = null;
    
    if (!empty($params['user']))
    {
      $this->view->userObject = Engine_Api::_()->user()->getUser($params['user']);
      
      if ($this->view->userObject instanceof User_Model_User && $this->view->userObject->getIdentity()) {
        return;
      }
      
    }
    
    return $this->setNoRender();
  }

  protected function getQueryParams()
  {
    $request = Zend_Controller_Front::getInstance()->getRequest();
    $params = $request->getParams();
    
    foreach (array('action','module','controller','rewrite') as $key) {
      unset($params[$key]);
    }
    
    $params = Engine_Api::_()->getApi('filter','radcodes')->removeKeyEmptyValues($params);
    
    return $params;
  }  
  
}