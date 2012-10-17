<?php

/**
 * Radcodes - SocialEngine Module
 *
 * @category   Application_Extensions
 * @package    Radcodes
 * @copyright  Copyright (c) 2009-2010 Radcodes LLC (http://www.radcodes.com)
 * @license    http://www.radcodes.com/license/
 * @version    $Id$
 * @author     Vincent Van <vincent@radcodes.com>
 */
 
class Radcodes_Widget_AdminMessageController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
    $this->view->message = file_get_contents("http://www.radcodes.com/lib/rest/news/?category=message");  
  }
}