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
 
 
 
class Article_Widget_ListSponsoredController extends Engine_Content_Widget_Abstract
{

  public function indexAction()
  {
  	
    // Don't render this if not authorized
    $viewer = Engine_Api::_()->user()->getViewer();

    $params = array(
      'published' => 1,
      'search' => 1,
      'sponsored' => 1,
      'limit' => $this->_getParam('max', 5),
      'order' => $this->_getParam('order', 'random'),
      'period' => $this->_getParam('period'),
      'user' => $this->_getParam('user'),
      'keyword' => $this->_getParam('keyword'),
      'category' => $this->_getParam('category'),
    ); 
    
    $this->view->showphoto = $this->_getParam('showphoto', 1);
    $this->view->showmeta = $this->_getParam('showmeta', 1); 
    $this->view->showdescription = $this->_getParam('showdescription', 1); 

    $this->view->paginator = $paginator = Engine_Api::_()->article()->getArticlesPaginator($params);

      // Do not render if nothing to show
    if( $paginator->getTotalItemCount() <= 0 ) {
      return $this->setNoRender();
    }
    
    $this->view->widget_name = 'article_sponsoredarticles_'.$this->getElement()->getIdentity();
    $this->view->use_slideshow = $paginator->getTotalItemCount() > 1;    
  }

}