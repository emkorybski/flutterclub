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
class Article_Widget_ProfileTagsController extends Engine_Content_Widget_Abstract
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
    
    $this->view->tagMaps = $tags = $subject->tags()->getTagMaps();
    
    if (count($tags) == 0) {
      return $this->setNoRender();
    }
    
    // Add count to title if configured
    if( $this->_getParam('titleCount', true) && count($tags) > 0 ) {
      $this->_childCount = count($tags);
    }
    
  }
  
  
  public function getChildCount()
  {
    return $this->_childCount;
  }    
}