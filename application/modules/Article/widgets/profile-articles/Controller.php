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
class Article_Widget_ProfileArticlesController extends Engine_Content_Widget_Abstract
{
  protected $_childCount;
  
  public function indexAction()
  {
    $viewer = Engine_Api::_()->user()->getViewer();
    
    $user_type = $this->_getParam('user_type', 'owner');
    
    if ($user_type == 'viewer') {
      if (!$viewer->getIdentity()) {
        return $this->setNoRender();
      }
      else {
        $user = $viewer;
      }     
    }
    else {
      // owner mode
      if( !Engine_Api::_()->core()->hasSubject() ) {
        return $this->setNoRender();
      }
      
      // Get subject and check auth
      $subject = Engine_Api::_()->core()->getSubject();
      
      if (!($subject instanceof Core_Model_Item_Abstract)) {
        return $this->setNoRender();
      }
      
      if( !$subject->authorization()->isAllowed($viewer, 'view') ) {
        return $this->setNoRender();
      }       
      
      if( !($subject instanceof User_Model_User) ) {
        $user = $subject->getOwner('user');
      }
      else {
        $user = $subject;
      }
    }
    
    if( !($user instanceof User_Model_User) || !$user->getIdentity()) {
      return $this->setNoRender();
    }
    
    $params = array(
      'published' => 1,
      'search' => 1,
      'limit' => $this->_getParam('max', 5),
      'order' => $this->_getParam('order', 'recent'),
      'period' => $this->_getParam('period'),
      'keyword' => $this->_getParam('keyword'),
      'category' => $this->_getParam('category'),
    );
    
    if ($this->_getParam('featured', 0)) {
      $params['featured'] = 1;
    }
    
    if ($this->_getParam('sponsored', 0)) {
      $params['sponsored'] = 1;
    }
    
    $params['user'] = $user;
    
    $this->view->paginator = $paginator = Engine_Api::_()->article()->getArticlesPaginator($params);

    $this->view->display_style = $this->_getParam('display_style', 'wide');
    
    $this->view->showphoto = $this->_getParam('showphoto', $this->view->display_style == 'narrow' ? 0 : 1);
    $this->view->showmeta = $this->_getParam('showmeta', $this->view->display_style == 'narrow' ? 0 : 1); 
    $this->view->showdescription = $this->_getParam('showdescription', $this->view->display_style == 'narrow' ? 0 : 1); 
        
    $this->view->showmemberarticleslink = $this->_getParam('showmemberarticleslink', $this->view->display_style == 'narrow' ? 0 : 1); 
    
    $this->view->order = $params['order'];
    
    $this->view->user = $user;
    
    // Add count to title if configured
    $this->_childCount = $paginator->getTotalItemCount();
    
    if ($paginator->getTotalItemCount() <= 0) {
      return $this->setNoRender();
    }
    
  }  
  
  public function indexActionBAK()
  {
    // Don't render this if not authorized
    $viewer = Engine_Api::_()->user()->getViewer();
    if( !Engine_Api::_()->core()->hasSubject() ) {
      return $this->setNoRender();
    }

    // Get subject and check auth
    $subject = Engine_Api::_()->core()->getSubject();
    if( !$subject->authorization()->isAllowed($viewer, 'view') ) {
      return $this->setNoRender();
    }   

    if( !($subject instanceof User_Model_User) ) {
      return $this->setNoRender();
    }    
    
    $this->view->items_per_page = $max = $this->_getParam('max', 5);
    $this->view->show_page_details = $this->_getParam('details', 0);

    $this->view->paginator = $paginator = Engine_Api::_()->article()->getPublishedArticlesPaginator(array(
      'order' => 'creation_date',
      'user_id' =>  Engine_Api::_()->core()->getSubject()->getIdentity(),
      'limit' => $this->view->items_per_page,
    ));

    // Do not render if nothing to show
    if( $paginator->getTotalItemCount() <= 0 ) {
      return $this->setNoRender();
    }

    // Add count to title if configured
    if( $this->_getParam('titleCount', false) && $paginator->getTotalItemCount() > 0 ) {
      $this->_childCount = $paginator->getTotalItemCount();
    }
    
  }

  public function getChildCount()
  {
    return $this->_childCount;
  }
}