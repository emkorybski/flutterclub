<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Inviter
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: SignupController.php 2010-07-02 19:54 mirlan $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Inviter
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Inviter_SignupController extends Engine_Controller_Action
{
    public function __call($method, $args)
    {
        // Psh, you're already signed up
        $viewer = Engine_Api::_()->user()->getViewer();
        if ($viewer && $viewer->getIdentity()) {
            return $this->_helper->redirector->gotoRoute(array(), 'default', true);
        }
        //for user account fields
        $session = new Zend_Session_Namespace('invite');
        $session->invite_code = $this->_getParam('code');
        $session->invite_email = $this->_getParam('email');

        // Get invite params
        $session = new Zend_Session_Namespace('inviter');
        $session->invite_code = $this->_getParam('code');
        $session->invite_id = $this->_getParam('sender');
        $session->invite_email = $this->_getParam('email');
        $session->lock();

        /**
         * @var $providerApi Inviter_Api_Provider
         */
        $providerApi = Engine_Api::_()->getApi('provider', 'inviter');
        $inviteTable = Engine_Api::_()->getDbtable('invites', 'inviter');

        if (empty($session->invite_code)) {
            return $this->_helper->redirector->gotoRoute(array(), 'default', true);
        }

        if ($session->invite_id) {
            $inviteSelect = $inviteTable->select()->where('invite_id = ?', $session->invite_id);
            $invite = $inviteTable->fetchRow($inviteSelect);
            if ($invite)
                return $this->_helper->redirector->gotoRoute(array(), 'user_signup', true);
        } else {
            $inviteSelect = $inviteTable->select()->where('code = ?', $session->invite_code);
            $invite = $inviteTable->fetchRow($inviteSelect);
        }

        if ($invite && $providerApi->checkIntegratedProvider($invite->provider)) {
            return $this->_helper->redirector->gotoRoute(array(), 'user_signup', true);
        }

        // Check code now if set
        $settings = Engine_Api::_()->getApi('settings', 'core');
        if ($settings->getSetting('user.signup.inviteonly') > 0) {
            // Check code

            // Check email
            if ($settings->getSetting('user.signup.checkemail')) {
                // Tsk tsk no email
                if (empty($session->sender)) {
                    if (empty($session->invite_email)) {
                        return $this->_helper->redirector->gotoRoute(array(), 'default', true);
                    }
                    $inviteSelect
                        ->where('recipient = ?', $session->invite_email);
                }
            }

            $inviteRow = $inviteTable->fetchRow($inviteSelect);

            // No invite or already signed up
            if (!$inviteRow || $inviteRow->new_user_id) {
                return $this->_helper->redirector->gotoRoute(array(), 'default', true);
            }
        }
        return $this->_helper->redirector->gotoRoute(array(), 'user_signup', true);
    }
}