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

class Inviter_Widget_LinkedinSuggestController extends Engine_Content_Widget_Abstract
{
  private $provider = 'linkedin';

  public function indexAction()
  {
    $viewer = Engine_Api::_()->user()->getViewer();
    $this->view->provider = $this->provider;

    /**
     * @var $providerApi Inviter_Api_Provider
     */
    $providerApi = Engine_Api::_()->getApi('provider', 'inviter');

    /**
     * @var $tokensTbl Inviter_Model_DbTable_Tokens
     */
    $tokensTbl = Engine_Api::_()->getDbTable('tokens', 'inviter');

    if (!$providerApi->checkIntegratedProvider($this->provider)) {
      $this->setNoRender();
    }

    if ($viewer->getIdentity() == 0) {
      $this->setNoRender();
    }

    $token = $tokensTbl->getUserToken($viewer->getIdentity(), $this->provider);
    $contacts = $providerApi->getNoneMemberContacts($token, $this->provider, 9);

    if ($contacts === false) {
      $this->view->show_login = true;
      return false;
    } elseif (!$contacts) {
      return $this->setNoRender();
    }

    $contact_list = array();
    foreach ($contacts as $contact) {
      $contact_list[] = new Inviter_Model_FacebookFriend($contact, $this->provider);
    }

    $this->view->friends = Zend_Paginator::factory($contact_list);

    $path = Zend_Controller_Front::getInstance()->getControllerDirectory('hecore');
    $path = dirname($path) . '/views/scripts';
    $this->view->addScriptPath($path);

    $active_theme = $this->view->activeTheme();
    if ($active_theme && is_string($active_theme)) {
      $this->getElement()->setAttrib('class', $active_theme . '_inviter_facebook_suggest');
    }
  }
}