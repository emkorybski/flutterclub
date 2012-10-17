<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Inviter
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Controller.php 2010-07-02 19:54 mirlan $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Inviter
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Inviter_Widget_IntroduceMemberController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
    $viewer = Engine_Api::_()->user()->getViewer();

    if (!$viewer->getIdentity()) {
      return $this->setNoRender();
    }

    $levels = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('inviter', $viewer, 'introduction');

    if(!in_array($viewer->level_id, $levels)) {
        return $this->setNoRender();
    }

    $introductionTbl = Engine_Api::_()->getDbTable('introductions', 'inviter');
    $userIntroduce = $introductionTbl->getUserIntroduction($viewer->getIdentity());

    $exclude_ids = array();
    if ($userIntroduce->exclude_ids) {
      $exclude_ids = explode(',', $userIntroduce->exclude_ids);
    }

    $memberIntroduce = $introductionTbl->getRandomIntroduction($viewer->getIdentity(), $exclude_ids);

    if ($memberIntroduce === null) {
      return $this->setNoRender();
    }

    $path = Zend_Controller_Front::getInstance()->getControllerDirectory('inviter');
    $path = dirname($path) . '/views/scripts';
    $this->view->addScriptPath($path);

    $this->view->memberIntroduce = $memberIntroduce;
    $this->view->memberItem = Engine_Api::_()->getItem('user', $memberIntroduce->user_id);

    $inviterApi = Engine_Api::_()->getApi('core', 'inviter');

    $this->view->mutual_friend_count = $inviterApi->getMutualFriendCount($memberIntroduce->user_id);
    $this->view->mutual_like_count = $inviterApi->getMutualLikeCount($memberIntroduce->user_id);

    if ($this->view->mutual_like_count) {
      $params = array('poster_type' => $this->view->memberItem->getType(), 'poster_id' => $this->view->memberItem->getIdentity());

      $select = Engine_Api::_()->like()->getLikesSelect($params);
      $select->where('like1.resource_type IN ("page", "user")');

      $this->view->likedMembersAndPages = Engine_Api::_()->like()->getTable()->fetchAll($select)->count();
    }

    $active_theme = $this->view->activeTheme();
    if ($active_theme && is_string($active_theme)) {
      $this->getElement()->setAttrib('class', $active_theme . '_inviter_introduce_member');
    }
  }
}