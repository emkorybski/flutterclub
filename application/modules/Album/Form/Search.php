<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Album
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: Search.php 9747 2012-07-26 02:08:08Z john $
 * @author     Jung
 */

/**
 * @category   Application_Extensions
 * @package    Album
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
class Album_Form_Search extends Engine_Form
{
  public function init()
  {
    $this
      ->setAttribs(array(
        'id' => 'filter_form',
        'class' => 'global_form_box',
      ))
      ->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()));

    parent::init();
    
    $this->addElement('Text', 'search', array(
      'label' => 'Search Albums:'
    ));

    $this->addElement('Select', 'sort', array(
      'label' => 'Browse By:',
      'multiOptions' => array(
        'recent' => 'Most Recent',
        'popular' => 'Most Popular',
      ),
    ));
    
    // prepare categories
    $categories = Engine_Api::_()->getDbtable('categories', 'album')->getCategoriesAssoc();
    if( count($categories) > 0 ) {
      $this->addElement('Select', 'category_id', array(
        'label' => 'Category',
        'multiOptions' => $categories,
      ));
    }
  }
}