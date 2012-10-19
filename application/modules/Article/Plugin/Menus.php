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

class Article_Plugin_Menus
{

  public function canCreateArticles()
  {
    // Must be logged in
    $viewer = Engine_Api::_()->user()->getViewer();
    if( !$viewer || !$viewer->getIdentity() ) {
      return false;
    }

    // Must be able to create articles
    if( !Engine_Api::_()->authorization()->isAllowed('article', $viewer, 'create') ) {
      return false;
    }

    return true;
  }

  public function canViewArticles()
  {
    $viewer = Engine_Api::_()->user()->getViewer();
    
    // Must be able to view articles
    if( !Engine_Api::_()->authorization()->isAllowed('article', $viewer, 'view') ) {
      return false;
    }

    return true;
  }  
  
  
  public function onMenuInitialize_ArticleGutterList($row)
  {
    if( !Engine_Api::_()->core()->hasSubject() ) {
      return false;
    }

    $article = Engine_Api::_()->core()->getSubject('article');

    if( !($article instanceof Article_Model_Article) ) {
      return false;
    } 
    
    // Modify params
    $params = $row->params;
    $params['params']['user'] = $article->owner_id;
    return $params;
  }

  public function onMenuInitialize_ArticleGutterCreate($row)
  {
    // Must be logged in
    $viewer = Engine_Api::_()->user()->getViewer();
    if( !$viewer || !$viewer->getIdentity() ) {
      return false;
    }

    // Must be able to create articles
    if( !Engine_Api::_()->authorization()->isAllowed('article', $viewer, 'create') ) {
      return false;
    }

    return true;
  }

  public function onMenuInitialize_ArticleGutterEdit($row)
  {
    if( !Engine_Api::_()->core()->hasSubject() ) {
      return false;
    }

    $viewer = Engine_Api::_()->user()->getViewer();
    $article = Engine_Api::_()->core()->getSubject('article');

    if( !($article instanceof Article_Model_Article) ) {
      return false;
    }    
    
    if( !$article->authorization()->isAllowed($viewer, 'edit') ) {
      return false;
    }

    // Modify params
    $params = $row->params;
    $params['params']['article_id'] = $article->getIdentity();
    return $params;
  }

  public function onMenuInitialize_ArticleGutterDelete($row)
  {
    if( !Engine_Api::_()->core()->hasSubject() ) {
      return false;
    }

    $viewer = Engine_Api::_()->user()->getViewer();
    $article = Engine_Api::_()->core()->getSubject('article');

    if( !($article instanceof Article_Model_Article) ) {
      return false;
    }
    
    if( !$article->authorization()->isAllowed($viewer, 'delete') ) {
      return false;
    }

    // Modify params
    $params = $row->params;
    $params['params']['article_id'] = $article->getIdentity();
    return $params;
  }
  
  
  public function onMenuInitialize_ArticlePhotosList($row)
  {
    if( !Engine_Api::_()->core()->hasSubject() ) {
      return false;
    }

    $viewer = Engine_Api::_()->user()->getViewer();
    $article = Engine_Api::_()->core()->getSubject('article');

    if( !($article instanceof Article_Model_Article) ) {
      return false;
    }
    
    if( !$article->authorization()->isAllowed($viewer, 'view') ) {
      return false;
    }
    
    // Modify params
    $params = $row->params;
    $params['params']['subject'] = $article->getGuid();
    return $params;
  }
  
  
  public function onMenuInitialize_ArticlePhotosManage($row)
  {
    if( !Engine_Api::_()->core()->hasSubject() ) {
      return false;
    }

    $viewer = Engine_Api::_()->user()->getViewer();
    $article = Engine_Api::_()->core()->getSubject('article');

    if( !($article instanceof Article_Model_Article) ) {
      return false;
    }
    
    if( !$article->authorization()->isAllowed($viewer, 'photo') ) {
      return false;
    }
    
    // Modify params
    $params = $row->params;
    $params['params']['subject'] = $article->getGuid();
    return $params;
  }
  
  
  public function onMenuInitialize_ArticlePhotosUpload($row)
  {
    if( !Engine_Api::_()->core()->hasSubject() ) {
      return false;
    }

    $viewer = Engine_Api::_()->user()->getViewer();
    $article = Engine_Api::_()->core()->getSubject('article');

    if( !($article instanceof Article_Model_Article) ) {
      return false;
    }
    
    if( !$article->authorization()->isAllowed($viewer, 'photo') ) {
      return false;
    }
    
    // Modify params
    $params = $row->params;
    $params['params']['subject'] = $article->getGuid();
    return $params;
  }  
  
}