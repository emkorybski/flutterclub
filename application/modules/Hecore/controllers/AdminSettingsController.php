<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Page
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: AdminSettingsController.php 2010-08-31 16:05 idris $
 * @author     Idris
 */

/**
 * @category   Application_Extensions
 * @package    Page
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Hecore_AdminSettingsController extends Core_Controller_Action_Admin
{
  public function init()
  {
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('hecore_admin_main', array(), 'hecore_admin_main_settings');
  }

  public function indexAction()
  {
    $this->view->form = $form = new Hecore_Form_Admin_Global();
    
    if (!$this->getRequest()->isPost()) {
      return;
    }

    if (!$form->isValid($this->getRequest()->getPost())) {
      return;
    }

    $settings = Engine_Api::_()->getApi('settings', 'core');

    $listing = $form->getValue('listing');
    $privacy = serialize($form->getValue('privacy'));

    $settings->setSetting('hecore.friend.widget.privacy', $privacy);
    $settings->setSetting('hecore.friend.widget.listing', $listing);
  }
}