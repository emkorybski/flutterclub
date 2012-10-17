<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Page
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Controller.php 2010-08-31 16:05 idris $
 * @author     Idris
 */

/**
 * @category   Application_Extensions
 * @package    Page
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Hecore_Widget_MutualFriendsController extends Engine_Content_Widget_Abstract
{
  protected $_childCount;

  public function indexAction()
  {
    $path = Zend_Controller_Front::getInstance()->getControllerDirectory('hecore');
    $path = dirname($path) . '/views/scripts';
    $this->view->addScriptPath($path);

    if (!Engine_Api::_()->core()->hasSubject()) {
      return $this->setNoRender();
    }

    $this->view->subject = $subject = Engine_Api::_()->core()->getSubject();
    $this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();

    if (($subject && $subject->getType() != 'user') || ($viewer && $viewer->isSelf($subject))) {
      return $this->setNoRender();
    }

    $this->view->friends = $friends = Engine_Api::_()->hecore()->getMutualFriends($subject, $viewer);
    $friends->setItemCountPerPage(9);

    if (!$friends->getTotalItemCount()) {
      return $this->setNoRender();
    }

    if( $this->_getParam('titleCount', false) && $friends->getTotalItemCount() > 0 ) {
      $this->_childCount = $friends->getTotalItemCount();
    }
  }

  public function getChildCount()
  {
    return $this->_childCount;
  }
}