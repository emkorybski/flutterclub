<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Inviter
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: AdminSettingsController.php 2010-07-02 19:54 mirlan $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Inviter
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Inviter_AdminSettingsController extends Core_Controller_Action_Admin
{
    public function levelAction()
    {
        $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('inviter_admin_main', array(), 'inviter_admin_main_level');

        // Get level id
        //$level_id = $this->_getParam('level_id', null);
        // Make navigation
        $level_id = $this->_getParam('id', 1);

        $this->view->form = $form = new Inviter_Form_Admin_Level();

        $form->level_id->setValue($level_id);
        $permissionsTable = Engine_Api::_()->getDbtable('permissions', 'authorization');

        if (!$this->getRequest()->isPost()) {
            if (null !== $level_id) {
                $form->populate($permissionsTable->getAllowed('inviter', $level_id, array_keys($form->getValues())));

                return;
            }

            return;
        }

        // Check validitiy
        if (!$form->isValid($this->getRequest()->getPost())) {
            return;
        }

        // Process

        $values = $form->getValues();
        $db = $permissionsTable->getAdapter();
        $db->beginTransaction();

        try
        {
            $permissionsTable->setAllowed('inviter', $level_id, $values);
            // Commit
            $db->commit();
        }
        catch (Exception $e)
        {
            $db->rollBack();
            throw $e;
        }
    }

    public function providersAction()
    {
        $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('inviter_admin_main', array(), 'inviter_admin_main_providers');

        $inviterApi = Engine_Api::_()->getApi('core', 'inviter');
        $this->view->providers = $providers = $inviterApi->getProviders(true, 1000, true);
    }

    public function providersSettingsAction()
    {
        $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('inviter_admin_main', array(), 'inviter_admin_main_prov_settings');

        $this->view->form = $form = new Inviter_Form_Admin_Providers();
        $form->getDecorator('description')->setOption('escape', false);

        if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
            if (isset($product_result['result']) && !$product_result['result']) {
                $form->addError($product_result['message']);
                $this->view->headScript()->appendScript($product_result['script']);

                return;
            }

            $values = $form->getValues();
            $settings = Engine_Api::_()->getApi('settings', 'core');

            foreach ($values as $key => $value) {
                if ($value) {
                    $settings->setSetting($key, $value);
                }
            }
        }
    }

    public function providersClearAction()
    {
        if (defined('_ENGINE_ADMIN_NEUTER') && _ENGINE_ADMIN_NEUTER) {
            return $this->_helper->redirector->gotoRoute(array('module' => 'inviter', 'controller' => 'stats', 'action'=>'index'), 'admin_default', true);
        }
        $viewer = Engine_Api::_()->user()->getViewer();

        if (!$viewer) {
            $this->view->status = false;
            $this->view->message = 1;
            return;
        }

        $params = $this->_getParam('values', false);

        if (!$params) {
            $this->view->status = false;
            $this->view->message = 2;
            return;
        }

        $settings = Engine_Api::_()->getDbTable('settings', 'core');
        foreach ($params as $param) {
            if ($settings->getSetting($param, false))
                $settings->setSetting($param, '');
        }

        $this->view->status = true;
        $this->view->message = 0;
    }

    public function providersSaveAction()
    {
        if (defined('_ENGINE_ADMIN_NEUTER') && _ENGINE_ADMIN_NEUTER) {
            return $this->_helper->redirector->gotoRoute(array('module' => 'inviter', 'controller' => 'stats', 'action'=>'index'), 'admin_default', true);
        }
        $viewer = Engine_Api::_()->user()->getViewer();

        if (!$viewer) {
            $this->view->status = false;
            $this->view->message = 1;
            return;
        }

        $params = $this->_getParam('values', false);

        if (!$params) {
            $this->view->status = false;
            $this->view->message = 2;
            return;
        }

        $settings = Engine_Api::_()->getDbTable('settings', 'core');

        foreach ($params as $key => $value) {
            if ($key)
                $settings->setSetting($key, $value);
        }

        $this->view->status = true;
        $this->view->message = 0;
    }

    public function enableProviderAction()
    {
        if (defined('_ENGINE_ADMIN_NEUTER') && _ENGINE_ADMIN_NEUTER) {
            return $this->_helper->redirector->gotoRoute(array('module' => 'inviter', 'controller' => 'stats', 'action'=>'index'), 'admin_default', true);
        }
        $provider_id = $this->_getParam('provider_id', 0);

        $providersTbl = Engine_Api::_()->getDbtable('providers', 'inviter');
        $provider = $providersTbl->findRow($provider_id);

        if ($provider) {
            $provider->provider_enabled = ($provider->provider_enabled == 1) ? 0 : 1;
            $provider->save();

            $this->view->status = true;
            $this->view->message = $provider->provider_enabled;
            return;
        } else {
            $this->view->status = false;
            $this->view->message = 1;
        }
    }

    public function showProviderAction()
    {
        if (defined('_ENGINE_ADMIN_NEUTER') && _ENGINE_ADMIN_NEUTER) {
            return $this->_helper->redirector->gotoRoute(array('module' => 'inviter', 'controller' => 'stats', 'action'=>'index'), 'admin_default', true);
        }

        $provider_id = $this->_getParam('provider_id', 0);

        $providersTbl = Engine_Api::_()->getDbtable('providers', 'inviter');
        $provider = $providersTbl->findRow($provider_id);

        if ($provider) {
            $provider->provider_default = ($provider->provider_default == 1) ? 0 : 1;
            $provider->save();

            $this->view->status = true;
            $this->view->message = $provider->provider_default;
            return;
        } else {
            $this->view->status = false;
            $this->view->message = 1;
        }
    }
}
