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
class Article_Widget_ProfilePhotosController extends Engine_Content_Widget_Abstract
{
  
  protected $_childCount;
    
  public function indexAction()
  {
    // Don't render this if not authorized
    $viewer = Engine_Api::_()->user()->getViewer();
    if( !Engine_Api::_()->core()->hasSubject() ) {
      return $this->setNoRender();
    }

    // Get subject and check auth
    $this->view->article = $subject = Engine_Api::_()->core()->getSubject('article');
    
    if( !($subject instanceof Article_Model_Article) ) {
      return $this->setNoRender();
    }    
  
    if( !$subject->authorization()->isAllowed($viewer, 'view') ) {
      return $this->setNoRender();
    }
    // album material
    $this->view->album = $album = $subject->getSingletonAlbum();
    $this->view->paginator = $paginator = $album->getCollectiblesPaginator();    
    $paginator->setCurrentPageNumber(1);
    $paginator->setItemCountPerPage($this->_getParam('max', 8));
        
    // Add count to title if configured
    if( $this->_getParam('titleCount', true) && $paginator->getTotalItemCount() > 0 ) {
      $this->_childCount = $paginator->getTotalItemCount();
    }
    
    $this->view->photosNavigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('article_photos');    
    
  }
  
  public function getChildCount()
  {
    return $this->_childCount;
  }  
}