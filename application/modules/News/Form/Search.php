<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    News
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: WidgetController.php
 * @author     Jung
 */

/**
 * @category   Application_Extensions
 * @package    News
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 */
class News_Form_Search extends Engine_Form
{
  public function init()
  {
    $this
      ->setAttribs(array(        
       'id' => 'filter_form',
       'class' => 'global_form_box',
      ))
      ->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()))
      ;
      
    $this->loadDefaultDecorators();
	$this->getDecorator('Description')->setOptions(array('tag' => 'h4', 'placement' => 'PREPEND'));
                
    
    $this->addElement('Text', 'search', array(
      'label' => 'Search News',            
      'style' =>      "width:155px;margin-bottom:5px;",   
    ));
    
    $this->addElement('Select', 'category', array(
      'label' => 'Feed',      
      'multiOptions' => array(
        '0' => 'All Feeds',
      ),
      'style' =>      "width:160px;margin-bottom:5px;",      
    ));
    
    // Buttons
    $this->addElement('Button', 'submit', array(
      'label' => 'Search',
      'type' => 'submit',
      'ignore' => true,
      'decorators' => array('ViewHelper')
    ));
   
    $this->addDisplayGroup(array('submit', 'getdata'), 'buttons');
    $button_group = $this->getDisplayGroup('buttons');
    $button_group->addDecorator('DivDivDivWrapper');
    
    
    $this->addElement('Hidden', 'page', array(
      'order' => 100
    ));       
    
  }
}