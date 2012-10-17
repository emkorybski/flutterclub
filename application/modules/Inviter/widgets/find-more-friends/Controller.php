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

class Inviter_Widget_FindMoreFriendsController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
    $viewer = Engine_Api::_()->user()->getViewer();
    $inviterApi = Engine_Api::_()->getApi('core', 'inviter');

    if (!$viewer->getIdentity()) {
      return $this->setNoRender();
    }

    $introductionTbl = Engine_Api::_()->getDbTable('introductions', 'inviter');
    $userIntroduce = $introductionTbl->getUserIntroduction($viewer->getIdentity());

    if ($userIntroduce && $userIntroduce->more_friends_date) {
      $period = Engine_Api::_()->getApi('settings', 'core')->getSetting('inviter.find_more_friends_period', 10);
      $period_ts = strtotime($userIntroduce->more_friends_date) + $period * 24 * 3600;

      if (time() < $period_ts) {
        return $this->setNoRender();
      }
    }

    $this->view->item_count = $item_count = $inviterApi->getInviterUsedFriendCount();
    if ($item_count) {
      $count = 4;
      $this->view->paginator = $paginator = $inviterApi->getInviterUsedFriends(array('order_rand' => true, 'count' => $count));
      $paginator->setItemCountPerPage($count);
    }
  }
}