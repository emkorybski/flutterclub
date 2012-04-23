<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: Controller.php 9595 2012-01-11 20:49:39Z john $
 * @author     John
 */

/**
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 */
class User_Widget_LoginOrSignupController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
    // Do not show if logged in
    if( Engine_Api::_()->user()->getViewer()->getIdentity() ) {
      $this->setNoRender();
      return;
    }
    
    // Display form
    $form = $this->view->form = new User_Form_Login(array(
      'mode' => 'column',
    ));;
    $form->setTitle(null)->setDescription(null);
    $form->removeElement('forgot');

    // Facebook login
    if( 'none' == Engine_Api::_()->getApi('settings', 'core')->core_facebook_enable ) {
      $form->removeElement('facebook');
    }
    
    // Check for recaptcha - it's too fat
    $this->view->noForm = false;
    if( ($captcha = $form->getElement('captcha')) instanceof Zend_Form_Element_Captcha && 
        $captcha->getCaptcha() instanceof Zend_Captcha_ReCaptcha ) {
      $this->view->noForm = true;
      $form->removeElement('email');
      $form->removeElement('password');
      $form->removeElement('captcha');
      $form->removeElement('submit');
      $form->removeElement('remember');
//      $form->removeElement('facebook');
//      $form->removeElement('twitter');
      $form->removeDisplayGroup('buttons');
    }
  }
  
  public function getCacheKey()
  {
    return false;
  }
}