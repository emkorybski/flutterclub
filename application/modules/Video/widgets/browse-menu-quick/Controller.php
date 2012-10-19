<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Video
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: Controller.php 9382 2011-10-14 00:41:45Z john $
 * @author     John Boehr <john@socialengine.com>
 */

/**
 * @category   Application_Extensions
 * @package    Video
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 */
class Video_Widget_BrowseMenuQuickController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
    // Get quick navigation
    $this->view->quickNavigation = $quickNavigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('video_quick');
  }
}
