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

class Inviter_Widget_TwitterConnectedFriendsController extends Engine_Content_Widget_Abstract
{
  private $provider = 'twitter';

  public function indexAction()
  {
    $viewer = Engine_Api::_()->user()->getViewer();
    /**
     * @var $providerApi Inviter_Api_Provider
     */
    $providerApi = Engine_Api::_()->getApi('provider', 'inviter');

    /**
     * @var $tokensTbl Inviter_Model_DbTable_Tokens
     */
    $tokensTbl = Engine_Api::_()->getDbTable('tokens', 'inviter');

    if (!$viewer->getIdentity() || !$providerApi->checkIntegratedProvider($this->provider)) {
      $this->setNoRender();
      return;
    }

    $token = $tokensTbl->getUserToken($viewer->getIdentity(), $this->provider);
    $this->view->members = $members = $providerApi->getAlreadyFriendContacts($token, $this->provider, 9);

    if ($members === false) {
      $this->view->show_login = true;
      return;
    } elseif (!$members) {
      $this->setNoRender();
      return;
    }

    $active_theme = $this->view->activeTheme();
    if ($active_theme && is_string($active_theme)) {
      $this->getElement()->setAttrib('class', $active_theme . '_inviter_facebook_members');
    }
  }
}