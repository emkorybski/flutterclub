<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Hecore
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: HecoreBaseUrl.php 2012-04-04 19:53 ermek $
 * @author     Ermek
 */

/**
 * @category   Application_Extensions
 * @package    Hecore
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Hecore_View_Helper_HecoreBaseUrl extends Engine_View_Helper_HtmlElement
{
  public function hecoreBaseUrl()
  {
    if (version_compare(Engine_Api::_()->getDbTable('modules', 'core')->getModule('core')->version, '4.1.8', '>=')){
      return $this->view->layout()->staticBaseUrl;
    } else {
      return $this->view->baseUrl() . '/';
    }

  }
}