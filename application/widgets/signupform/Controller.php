<?php
/**
 * SocialEngine
 *
 * @category   Application_Widget
 * @package    Widget
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @author     John
 */

/**
 * @category   Application_Widget
 * @package    Widget
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 */
class Widget_signupformController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
    $this->view->form = $form = new User_Form_Signup_Account();
      return;
    }
  
}