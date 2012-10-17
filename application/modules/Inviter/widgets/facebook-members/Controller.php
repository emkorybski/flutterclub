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

class Inviter_Widget_FacebookMembersController extends Engine_Content_Widget_Abstract
{
    private $provider = 'facebook';

    public function indexAction()
    {
        $viewer = Engine_Api::_()->user()->getViewer();

        $settings = Engine_Api::_()->getApi('settings', 'core');
        $app_id = $settings->getSetting('inviter.facebook.consumer.key', false);
        $secret = $settings->getSetting('inviter.facebook.consumer.secret', false);
        $fbApi = Engine_Api::_()->loadClass('Inviter_Plugin_fbApi');
        $fbApi->init($app_id, $secret);

        $providerApi = Engine_Api::_()->getApi('provider', 'inviter');
        $tokensTbl = Engine_Api::_()->getDbTable('tokens', 'inviter');
        $token = $tokensTbl->getUserToken($viewer->getIdentity(), $this->provider);

        if (!$token) {
            $this->view->show_login = true;
            return;
        }

        if (!$app_id || $viewer->getIdentity() == 0) {
            $this->setNoRender();
        }
        $fb_user_id = $fbApi->getMe($token->getParam('oauth_token'), true);

        if (!$fb_user_id) {
            $this->view->show_login = true;
            return;
        }

        if (!$this->getElement()->getTitle()) {
            $this->getElement()->setTitle('INVITER_Facebook Friends on Site');
        }


        $this->view->members = $members = $providerApi->getNoneFriendContacts($token, $this->provider);

        if (!$members) {
            return $this->setNoRender();
        }

        $active_theme = $this->view->activeTheme();
        if ($active_theme && is_string($active_theme)) {
            $this->getElement()->setAttrib('class', $active_theme . '_inviter_facebook_members');
        }
    }
}