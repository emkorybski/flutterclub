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
class Article_Widget_ProfileNoticeController extends Engine_Content_Widget_Abstract
{
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
    
    if ($subject->isPublished()) {
    	return $this->setNoRender();
    }
    
		$this->view->is_owner = $subject->getOwner()->isSelf($viewer);
    
    // hack to work around SE v4.1.8 User::isAdmin bug "Registry is already initialized"
    try
    {
    	$is_admin = $viewer->isAdmin();
    } 
    catch (Exception $ex)
    {
      $is_admin = Engine_Api::_()->getApi('core', 'authorization')->isAllowed('admin', null, 'view');	
    }
    
    if (!$is_admin && !$this->view->is_owner)
    {
      return $this->setNoRender();
    }
    
    $this->view->approval = 0;
    if ($viewer->getIdentity()) {
      $this->view->approval = (int) Engine_Api::_()->authorization()->getPermission($viewer->level_id, 'article', 'approval');
    }    
    
  }
}