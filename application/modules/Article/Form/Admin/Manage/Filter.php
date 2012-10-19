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
 
 
 
class Article_Form_Admin_Manage_Filter extends Engine_Form
{
  
  public function init()
  {
    $this->clearDecorators()
      ->addDecorator('FormElements')
      ->addDecorator('Form')
      ->addDecorator('HtmlTag', array('tag' => 'div', 'class' => 'search'))
      ->addDecorator('HtmlTag2', array('tag' => 'div', 'class' => 'clear'));

    $this
      ->setAttribs(array(
        'id' => 'filter_form',
        'class' => 'global_form_box',
      ));

    $categories = array(""=>"") + Engine_Api::_()->getItemTable('article_category')->getMultiOptionsAssoc();
    $this->addElement('Select', 'category', array(
      'label' => 'Category',
      'filters' => array(
        new Radcodes_Lib_Filter_Null()
      ),
      'multiOptions' => $categories,      
    ));        
      
    $this->addElement('Text', 'keyword', array(
      'label' => 'Keyword',
      'attribs' => array('size' => 10),
    ));  
    
    $this->addElement('Text', 'user', array(
      'label' => 'User',
      'attribs' => array('size' => 10),
    ));  

    $this->addElement('Select', 'published', array(
      'label' => 'Status',
      'multiOptions' => array(
        '' => '',
        '1' => 'Published',
        '0' => 'Draft',
      ),
    ));
    
    $yes_no = array(
        '' => '',
        '1' => 'Yes',
        '0' => 'No',
    );  
    
    $this->addElement('Select', 'featured', array(
      'label' => 'Featured',
      'multiOptions' => $yes_no,
    ));
    
    $this->addElement('Select', 'sponsored', array(
      'label' => 'Sponsored',
      'multiOptions' => $yes_no,
    ));


    foreach( $this->getElements() as $fel ) {
      if( $fel instanceof Zend_Form_Element ) {
        
        $fel->clearDecorators()
          ->addDecorator('ViewHelper')
          ->addDecorator('Label', array('tag' => null, 'placement' => 'PREPEND'))
          ->addDecorator('HtmlTag', array('tag' => 'div', 'id'  => $fel->getName() . '-search-wrapper', 'class' => 'form-search-wrapper'));
        
      }
    }  
    
    $submit = new Engine_Form_Element_Button('filtersubmit', array('type' => 'submit'));
    $submit
      ->setLabel('Search')
      ->clearDecorators()
      ->addDecorator('ViewHelper')
      ->addDecorator('HtmlTag', array('tag' => 'div', 'class' => 'buttons'))
      ->addDecorator('HtmlTag2', array('tag' => 'div'));

    $this->addElement($submit);
      
    $this->addElement('Hidden', 'order', array(
      'order' => 1001,
    ));

    $this->addElement('Hidden', 'order_direction', array(
      'order' => 1002,
    ));
      
      
          
    $this->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array('module'=>'article', 'controller'=>'manage'), 'admin_default', true));
      
  }
  
}