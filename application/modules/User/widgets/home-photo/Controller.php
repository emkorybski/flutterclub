<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: Controller.php 9583 2012-01-09 21:47:54Z john $
 * @author     John
 */

/**
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 */
class User_Widget_HomePhotoController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
    // Don't render this if not logged in
    $viewer = Engine_Api::_()->user()->getViewer();
    if( !$viewer->getIdentity() ) {
      return $this->setNoRender();
    }
  }

  public function getCacheKey()
  {
    $viewer = Engine_Api::_()->user()->getViewer();
    $translate = Zend_Registry::get('Zend_Translate');
    return $viewer->getIdentity() . $translate->getLocale() . sprintf('%d', $viewer->photo_id);
  }
}