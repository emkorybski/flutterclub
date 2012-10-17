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
class Article_ProfileController extends Core_Controller_Action_Standard
{
  public function init()
  {
    // @todo this may not work with some of the content stuff in here, double-check
    $subject = null;
    if( !Engine_Api::_()->core()->hasSubject() )
    {
      $id = $this->_getParam('article_id');
      if( null !== $id )
      {
        $subject = Engine_Api::_()->getItem('article', $id);
        if( $subject && $subject->getIdentity() )
        {
          Engine_Api::_()->core()->setSubject($subject);
        }
      }
    }

    $this->_helper->requireSubject('article');
    
    if (Engine_Api::_()->core()->hasSubject())
    {    
	    $this->_helper->requireAuth()->setNoForward()->setAuthParams(
	      $subject,
	      Engine_Api::_()->user()->getViewer(),
	      'view'
	    );
    }
  }

  public function indexAction()
  {
    $subject = Engine_Api::_()->core()->getSubject();
    $viewer = Engine_Api::_()->user()->getViewer();

    if (!$subject->isPublished())
    {
	    // hack to work around SE v4.1.8 User::isAdmin bug "Registry is already initialized"
	    try
	    {
	    	$is_admin = $viewer->isAdmin();
	    } 
	    catch (Exception $ex)
	    {
	      $is_admin = Engine_Api::_()->getApi('core', 'authorization')->isAllowed('admin', null, 'view');	
	    }
       	
    	
    	if (!$is_admin && !$subject->getOwner()->isSelf($viewer)) {
    		return;
    	}
    }
    
    // Increment view count
    if( !$subject->getOwner()->isSelf($viewer) )
    {
      $subject->view_count++;
      $subject->save();
    }

      // Get styles
    $table = Engine_Api::_()->getDbtable('styles', 'core');
    $select = $table->select()
      ->where('type = ?', $subject->getType())
      ->where('id = ?', $subject->getIdentity())
      ->limit();

    $row = $table->fetchRow($select);

    if( null !== $row && !empty($row->style) ) {
      $this->view->headStyle()->appendStyle($row->style);
    }    
    
    // Render
    $this->_helper->content
        ->setNoRender()
        ->setEnabled()
        ;
  }
}