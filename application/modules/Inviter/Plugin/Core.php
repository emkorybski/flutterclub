<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Inviter
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Core.php 2010-07-02 19:54 mirlan $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Inviter
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Inviter_Plugin_Core
{
    public function onMenuInitialize_CoreMainInviter()
    {
        $viewer = Engine_Api::_()->user()->getViewer();

        if (!Engine_Api::_()->authorization()->isAllowed('inviter', null, 'use')) {
            return false;
        }
        return true;
    }

    public function onMenuInitialize_CoreSitemapInviter()
    {
        if (!Engine_Api::_()->authorization()->isAllowed('inviter', null, 'use')) {
            return false;
        }

        return true;
    }

    public function onMenuInitialize_InviterFacebook()
    {
        return false;
        $facebook = Inviter_Api_Provider::getFBInstance();
        $appId = $facebook->getAppId();

        if (!$appId) {
            return false;
        }

        return true;
    }

    public function onUserDeleteBefore($payload)
    {
        $user = $payload->getPayload();

        if ($user instanceof User_Model_User) {
            // Delete invitations
            $inviterTable = Engine_Api::_()->getDbtable('invites', 'inviter');
            $inviterSelect = $inviterTable->select()
                ->orWhere('user_id = ?', $user->getIdentity())
                ->where('new_user_id = ?', $user->getIdentity());

            foreach ($inviterTable->fetchAll($inviterSelect) as $inviter) {
                $inviter->delete();
            }

            // Delete introduction
            $introductionTbl = Engine_Api::_()->getDbTable('introductions', 'inviter');
            $userIntroduce = $introductionTbl->getUserIntroduction($user->getIdentity());
            if ($userIntroduce) {
                $userIntroduce->delete();
            }

            // Delete hidden suggests
            $nonefriendsTbl = Engine_Api::_()->getDbtable('nonefriends', 'inviter');
            $nonefriendsSel = $nonefriendsTbl->select()
                ->where('user_id = ?', $user->getIdentity());
            $nonefriends = $nonefriendsTbl->fetchRow($nonefriendsSel);
            if ($nonefriends) {
                $nonefriends->delete();
            }

            // Delete tokens
            $tokensTbl = Engine_Api::_()->getDbTable('tokens', 'inviter');
            $tokensSel = $tokensTbl->select()
                ->where('user_id = ?', $user->getIdentity());

            foreach ($tokensTbl->fetchAll($tokensSel) as $token) {
                $token->delete();
            }
        }
    }

    public function onUserCreateAfter($payload)
    {
        $user = $payload->getPayload();
        $session = new Zend_Session_Namespace('inviter');

        if ($user instanceof User_Model_User && $session->__isset('invite_code')) {
            // Assign User Reqistered
            $inviterTable = Engine_Api::_()->getDbtable('invites', 'inviter');
            if (!$session->__isset('invite_id'))
                $inviterSelect = $inviterTable->select()->where('code = ?', $session->__get('invite_code'))->limit(1);
            else
                $inviterSelect = $inviterTable->select()->where('invite_id = ?', $session->__get('invite_id'))->limit(1);

            $reffered = 0;
            if ($inviter = $inviterTable->fetchRow($inviterSelect)) {
                //Update Sender Referrals
                $inviter->new_user_id = $user->getIdentity();
                $inviter->referred_date = gmdate('Y-m-d H:i:s');

                $inviter->save();

                $sender = Engine_Api::_()->getItem('user', $inviter->user_id);
                if (!is_null($sender)) {
                    $event = Engine_Hooks_Dispatcher::getInstance()->callEvent('onInviterRefered', $inviter);
                }

                $isEligible = Engine_Api::_()->getApi('settings', 'core')->user_friends_eligible;
                $coreInvitesTable = Engine_Api::_()->getDbtable('invites', 'invite');

                $inviteSel = $coreInvitesTable->select()
                    ->where('user_id = ?', $inviter->user_id)
                    ->where('recipient = ?', $inviter->recipient);

                if (!($isEligible && $coreInvitesTable->fetchRow($inviteSel))) {
                    // add friend request to this user from invited users
                    $activity = Engine_Api::_()->getDbtable('notifications', 'activity');
                    $friend = Engine_Api::_()->user()->getUser($inviter->user_id);
                    $user->membership()->addMember($friend);
                    $user->membership()->setUserApproved($friend);
                    $activity->addNotification($user, $friend, $user, 'friend_request');
                    // end of add friend request to this user from invited users
                }

                //Delete Other invitations to this email address
                $invSelect = $inviterTable->select()
                    ->where("recipient = '{$inviter->recipient}' && code != '{$inviter->code}' && new_user_id = 0");
                foreach ($inviterTable->fetchAll($invSelect) as $inv)
                {
                    $inv->delete();
                }

                //Update Statistics
                Engine_Api::_()->getDbtable('statistics', 'inviter')->increment('inviter.referreds');
            }

            if($session->__isset('user_referral_code')) {
                $codes_tbl = Engine_Api::_()->getDbTable('codes', 'inviter');
                $codes_tbl->setUserId($session->__get('user_referral_code'), $user->getIdentity());
            }


        }
    }

    public function onMenuInitialize_UserProfileIntroduce()
    {
        $viewer = Engine_Api::_()->user()->getViewer();

        if (!Engine_Api::_()->authorization()->isAllowed('inviter', null, 'use')) //@todo add new setting?
        {
            return false;
        }
        return true;
    }
}