<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Video
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: Controller.php 9382 2011-10-14 00:41:45Z john $
 * @author     John Boehr <john@socialengine.com>
 */

/**
 * @category   Application_Extensions
 * @package    Video
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 */
class Video_Widget_BrowseSearchController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
    $viewer = Engine_Api::_()->user()->getViewer();
    
    // Make form
    $this->view->form = $form = new Video_Form_Search();
    
    // Process form
    $p = Zend_Controller_Front::getInstance()->getRequest()->getParams();
    if( $form->isValid($p) ) {
      $values = $form->getValues();
    } else {
      $values = array();
    }
    $this->view->formValues = $values;

    $values['status'] = 1;
    $values['search'] = 1;

    $this->view->category = $values['category'];


    if( !empty($values['tag']) ) {
      $this->view->tag = Engine_Api::_()->getItem('core_tag', $values['tag'])->text;
    }
    
    // check to see if request is for specific user's listings
    $user_id = $this->_getParam('user');
    if( $user_id ) {
      $values['user_id'] = $user_id;
    }
  }
}
