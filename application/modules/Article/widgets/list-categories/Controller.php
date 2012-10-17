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
 
 
 
class Article_Widget_ListCategoriesController extends Engine_Content_Widget_Abstract
{

  public function indexAction()
  {
  	
    // Don't render this if not authorized
    $viewer = Engine_Api::_()->user()->getViewer();

    $this->view->showdetails = $this->_getParam('showdetails', 0);
    $this->view->showphoto = $this->_getParam('showphoto', 1);
    $this->view->descriptionlength = $this->_getParam('descriptionlength', 68);
    
    $this->view->categories = $categories = Engine_Api::_()->getItemTable('article_category')->getParentChildrenAssoc();
    
    
    if (empty($categories))
    {
    	return $this->setNoRender();
    }
  }

}
