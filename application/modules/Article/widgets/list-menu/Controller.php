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
 
 
 
class Article_Widget_ListMenuController extends Engine_Content_Widget_Abstract
{

  public function indexAction()
  {
  	
    $viewer = Engine_Api::_()->user()->getViewer();
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('article_main'); 
      
    /*
    $this->view->navigation = $navigation = new Zend_Navigation();
    
    $navigation->addPage(array(
      'label' => Zend_Registry::get('Zend_Translate')->_('Browse Articles'),
      'route' => 'article_browse',
      'module' => 'article',
      'controller' => 'index',
      'action' => 'browse',
    ));

    if( $viewer->getIdentity() )
    {
	    $navigation->addPage(array(
	      'label' => Zend_Registry::get('Zend_Translate')->_('My Articles'),
	      'route' => 'article_manage',
	      'module' => 'article',
	      'controller' => 'index',
	      'action' => 'manage',
	    ));
	    
	    if (Engine_Api::_()->authorization()->isAllowed('article', $viewer, 'create'))
	    {
	      $navigation->addPage(array(
	        'label' => Zend_Registry::get('Zend_Translate')->_('Post New Article'),
	        'route' => 'article_create',
	        'module' => 'article',
	        'controller' => 'index',
	        'action' => 'create'
	      ));
	    }
    }
    */
  }

}