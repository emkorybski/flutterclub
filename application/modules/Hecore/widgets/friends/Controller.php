<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Hecore
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Controller.php 2010-08-31 16:05 idris $
 * @author     Idris
 */

/**
 * @category   Application_Extensions
 * @package    Hecore
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Hecore_Widget_FriendsController extends Engine_Content_Widget_Abstract
{
  protected $_childCount;
  
  public function indexAction()
  {
    $path = Zend_Controller_Front::getInstance()->getControllerDirectory('hecore');
    $path = dirname($path) . '/views/scripts';
    $this->view->addScriptPath($path);

		$this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();
		
    if (!Engine_Api::_()->core()->hasSubject()) {
      $this->view->subject = $subject = $viewer;
    } else {
			$this->view->subject = $subject = Engine_Api::_()->core()->getSubject();
		}
		
    if ($subject->getType() != 'user') {
      return $this->setNoRender();
    }

    $settings = Engine_Api::_()->getApi('settings', 'core');
    $privacy = $settings->getSetting('hecore.friend.widget.privacy');
    $this->view->privacy_list = $privacy
      ? unserialize($privacy)
      : array('everyone', 'registered', 'owner_network', 'owner_member_member', 'owner_member', 'owner');
    $this->view->privacy_labels = array(
      'everyone'            => 'Everyone',
      'registered'          => 'All Registered Members',
      'owner_network'       => 'Friends and Networks',
      'owner_member_member' => 'Friends of Friends',
      'owner_member'        => 'Friends Only',
      'owner'               => 'Just Me'
    );

    $this->view->isListing = $settings->getSetting('hecore.friend.widget.listing', 1);

    $he = Engine_Api::_()->getDbTable('user_settings', 'hecore');
    $auth = Engine_Api::_()->getDbTable('allow', 'authorization');

    $value = $he->getSetting('hecore.friend.ipp', $subject->getIdentity());
    $this->view->ipp = $value = ($value) ? $value : 9;

    $this->view->privacy = $privacy = $he->getSetting('hecore.friend.privacy', $subject->getIdentity());
    $this->view->list = $list = $he->getSetting('hecore.friend.list', $subject->getIdentity());

    if (!$viewer->isSelf($subject)) {
      if (!$privacy) {
        $privacy = 'everyone';
      }
      $func = 'is_' . $privacy;
      if (!$auth->$func($subject, $viewer)) {
        return $this->setNoRender();
      }
    }

    $this->view->friends = $friends = Engine_Api::_()->hecore()->getFriends(array('sort_list' => $list), $subject);
    $friends->setItemCountPerPage($value);

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