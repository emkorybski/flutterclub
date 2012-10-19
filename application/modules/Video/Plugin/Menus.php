<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Video
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: Menus.php 9281 2011-09-21 00:21:40Z john $
 * @author     Jung
 */

/**
 * @category   Application_Extensions
 * @package    Video
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 */
class Video_Plugin_Menus
{
  public function canCreateVideos()
  {
    $viewer = Engine_Api::_()->user()->getViewer();

    if( !Engine_Api::_()->authorization()->isAllowed('video', $viewer, 'create') ) {
      return false;
    }

    return true;
  }
  
  public function onMenuInitialize_VideoMainManage($row)
  {
    $viewer = Engine_Api::_()->user()->getViewer();
    if( !$viewer->getIdentity() ) {
      return false;
    }

    return true;
  }

  public function onMenuInitialize_VideoMainCreate($row)
  {
    $viewer = Engine_Api::_()->user()->getViewer();

    if( !Engine_Api::_()->authorization()->isAllowed('video', $viewer, 'create') ) {
      return false;
    }

    return true;
  }
}