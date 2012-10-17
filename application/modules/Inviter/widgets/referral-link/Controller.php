<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Inviter
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Controller.php 2010-07-02 19:54 kirill $
 * @author     Kirill
 */

/**
 * @category   Application_Extensions
 * @package    Inviter
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Inviter_Widget_ReferralLinkController extends Engine_Content_Widget_Abstract
{

  public function indexAction() {
      $this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();
      if (!$viewer->getIdentity()) {
        return $this->setNoRender();
      }

      $codes_tbl = Engine_Api::_()->getDbTable('codes','inviter');
      $this->view->referral_link = $codes_tbl->getUserReferralLink($viewer->getIdentity());
      $host_url = (_ENGINE_SSL ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'];
      $host_url .= $this->view->baseUrl();
      $this->view->host_url = $host_url;
  }
}