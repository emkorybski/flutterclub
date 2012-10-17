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

class Inviter_Widget_ListTopReferralsController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
    $viewer = Engine_Api::_()->user()->getViewer();
    if(!Engine_Api::_()->authorization()->isAllowed('inviter', null, 'use') && !$viewer->getIdentity())
    {
      return $this->setNoRender();
    }

    $userTb = Engine_Api::_()->getItemTable('user');
    $invitesTb = Engine_Api::_()->getDbtable('invites', 'inviter');
    $select = $userTb->select()
      ->setIntegrityCheck(false)
      ->from($userTb->info('name'))
      ->joinInner(
        $invitesTb->info('name'),
        $userTb->info('name').'.user_id = '.$invitesTb->info('name').'.user_id', array('COUNT('.$invitesTb->info('name').'.invite_id) AS referrals'))
      ->where($userTb->info('name').'.search = ?', 1)
      ->where($userTb->info('name').'.enabled = ?', 1)
      ->where($userTb->info('name').'.verified = ?', 1)
      ->where($userTb->info('name').'.member_count > ?', -1)
      ->where($invitesTb->info('name').'.new_user_id > 0')
      ->group($invitesTb->info('name').'.user_id')
      ->order('referrals DESC');

    $this->view->paginator = $paginator = Zend_Paginator::factory($select);
    $paginator->setItemCountPerPage($this->_getParam('itemCountPerPage', 4));

    // Do not render if nothing to show
    if( $paginator->getTotalItemCount() <= 0 ) {
      return $this->setNoRender();
    }
  }
}