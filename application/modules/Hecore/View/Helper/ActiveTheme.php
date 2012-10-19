<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Hecore
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: ActiveTheme.php 2010-07-02 19:53 ermek $
 * @author     Ermek
 */

/**
 * @category   Application_Extensions
 * @package    Hecore
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Hecore_View_Helper_ActiveTheme extends Engine_View_Helper_HtmlElement
{
  public function activeTheme()
  {
    $table = Engine_Api::_()->getDbtable('themes', 'core');
    $activeTheme = $table->fetchRow( $table->select()->where('active = ?', 1) );

    return ($activeTheme && $activeTheme->name) ? $activeTheme->name : 'default';
  }
}