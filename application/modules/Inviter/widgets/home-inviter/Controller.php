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

class Inviter_Widget_HomeInviterController extends Engine_Content_Widget_Abstract
{
    protected $_errors = array(), $_success;

    public function init()
    {
        $this->view->headTranslate(array(
            'INVITER_Failed!, please check your contacts and try again.',
            'INVITER_Failed! Please check and try again later.',
        ));
    }

    public function indexAction()
    {
        $front = Zend_Controller_Front::getInstance();
        $module = $front->getRequest()->getModuleName();

        $this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();

        $auth_table = Engine_Api::_()->getDbTable('permissions', 'authorization');
        if (!$auth_table->isAllowed('inviter', $viewer, 'use')) {
            return $this->setNoRender();
        }

        $this->view->fb_settings = Engine_Api::_()->inviter()->getFacebookSettings($this->view);

        $session = new Zend_Session_Namespace('inviter');

        $this->view->success = $session->__get('success', false);
        $this->view->message = $session->__get('message', false);
        $session->__set('success', false);
        $session->__set('message', false);

        $providers = Engine_Api::_()->inviter()->getProviders2(false, 15);
        $this->view->providers = Engine_Api::_()->inviter()->getIntegratedProviders();
        $this->view->count = count($providers);

        if ($viewer->getIdentity() && $module == 'inviter') {
            $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
                ->getNavigation('inviter_profile', array(), 'inviter_profile_invite');
        }

        $path = Zend_Controller_Front::getInstance()->getControllerDirectory('inviter');
        $path = dirname($path) . '/views/scripts';
        $this->view->addScriptPath($path);

        //GET INVITER
        $this->view->form = $form = new Inviter_Form_Import();
        $form->setAction($this->view->url(array('module' => 'inviter', 'controller' => 'index', 'action' => 'process'), 'default', true));

        if ($viewer->getIdentity()) {
            $this->view->form_upload = $form_upload = new Inviter_Form_Upload();
            $this->view->form_write = $form_write = new Inviter_Form_Write();
        }

        if ($this->_getParam('success', 0)) {
            $form->addNotice('INVITER_Invites sent successfully!');
        }

        if ($viewer->getIdentity()) {
            $suggest_array = Engine_Api::_()->getDbtable('nonefriends', 'inviter')->getSuggests();

            $current_suggests = array();

            $noneFriendCount = $suggest_array['noneFriendCount'];
            $suggest_array = $suggest_array['suggests'];

            foreach ($suggest_array as $suggest)
            {
                $current_suggests[$suggest->getIdentity()]['user_id'] = $suggest->getIdentity();
            }

            $this->view->noneFriendCount = $noneFriendCount;
            $this->view->current_suggests = $current_suggests;
            $this->view->suggests = $suggests = $this->view->suggests(array('suggests' => $suggest_array));
        }
    }
}