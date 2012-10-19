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

class Inviter_Widget_FacebookSuggestController extends Engine_Content_Widget_Abstract
{
    private $provider = 'facebook';

    public function indexAction()
    {
        $viewer = Engine_Api::_()->user()->getViewer();
        /**
         * @var $providerApi Inviter_Api_Provider
         */
        $providerApi = Engine_Api::_()->getApi('provider', 'inviter');
        $tokensTbl = Engine_Api::_()->getDbTable('tokens', 'inviter');

        $settings = Engine_Api::_()->getDbTable('settings', 'core');
        $this->view->app_id = $app_id = $settings->getSetting('inviter.facebook.consumer.key', false);
        $this->view->fb_settings = $fb_settings = Engine_Api::_()->inviter()->getFacebookSettings($this->view);
        $this->view->providers = $providers = Engine_Api::_()->inviter()->getIntegratedProviders();

        $secret = $settings->getSetting('inviter.facebook.consumer.secret', false);
        $fbApi = Engine_Api::_()->loadClass('Inviter_Plugin_fbApi');
        $fbApi->init($app_id, $secret);
        $token = $tokensTbl->getUserToken($viewer->getIdentity(), $this->provider);

        if (!$token) {
            $this->view->show_login = true;
            return;
        }
        $fb_user_id = $fbApi->getMe($token->getParam('oauth_token'), true);


        if (!$app_id || $viewer->getIdentity() == 0) {
            $this->setNoRender();
        }

        if (!$fb_user_id) {
            $this->view->show_login = true;
            return;
        }

        $fb_users = $providerApi->getNoneMemberContacts($token, $this->provider);

        if ($fb_users === false) {
            $this->view->show_login = true;
            return;
        } elseif (!$fb_users) {
            return $this->setNoRender();
        }

        $fbUsers = array();
        foreach ($fb_users as $fb_user) {
            $fbUsers[] = new Inviter_Model_FacebookFriend($fb_user, $this->provider);
        }

        $this->view->friends = Zend_Paginator::factory($fbUsers);

        $path = Zend_Controller_Front::getInstance()->getControllerDirectory('hecore');
        $path = dirname($path) . '/views/scripts';
        $this->view->addScriptPath($path);

        $active_theme = $this->view->activeTheme();
        if ($active_theme && is_string($active_theme)) {
            $this->getElement()->setAttrib('class', $active_theme . '_inviter_facebook_suggest');
        }
    }
}