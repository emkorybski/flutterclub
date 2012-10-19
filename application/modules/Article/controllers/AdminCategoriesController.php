<?php

/**
 * Radcodes - SocialEngine Module
 *
 * @category   Application_Extensions
 * @package    Article
 * @copyright  Copyright (c) 2009-2010 Radcodes LLC (http://www.radcodes.com)
 * @license    http://www.radcodes.com/license/
 * @version    $Id$
 * @author     Vincent Van <vincent@radcodes.com>
 */
 

class Article_AdminCategoriesController extends Radcodes_Controller_AdminCategoriesAbstract
{
  public function init()
  {
    if (!Engine_Api::_()->article()->checkLicense()) {
      return $this->_redirectCustom(array('route'=>'admin_default', 'module'=>'article', 'controller'=>'settings', 'notice' => 'license'));
    }   

    parent::init();
  } 
}
