<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Chat
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: AdminManageController.php 7493 2010-09-29 04:08:05Z shaun $
 * @author     John
 */

/**
 * @category   Application_Extensions
 * @package    Chat
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 */
class Chat_Plugin_Task_Cleanup extends Core_Plugin_Task_Abstract
{
  public function execute()
  {
    // Garbage collect
    Engine_Api::_()->getDbtable('events', 'chat')->gc();
    Engine_Api::_()->getDbtable('users', 'chat')->gc();
    Engine_Api::_()->getDbtable('roomUsers', 'chat')->gc();
    Engine_Api::_()->getDbtable('whispers', 'chat')->gc();
    
    // This task shouldn't take too long, just set was idle
    $this->_setWasIdle();
  }
}
