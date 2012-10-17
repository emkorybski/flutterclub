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
class News_Form_Commonsearch extends Engine_Form
{
  public function init()
  {
    $this
      ->setAttribs(array(
        'id' => 'filter_form',
        'class' => 'global_form_box',
      ))
      ->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array('module' => 'news', 'controller' => 'index', 'action' => 'list'), 'news_category', true))
      ;

    $this->loadDefaultDecorators();
	$this->getDecorator('Description')->setOptions(array('tag' => 'h4', 'placement' => 'PREPEND'));


    $this->addElement('Text', 'search', array(
      'label' => 'Search',
      'style' =>      "width:150px;margin-bottom:5px;",
    ));
    $this->addElement('Select', 'categoryparent', array(
      'label' => 'Category',
      'multiOptions' => array(
        '-1' => 'All Categories',
       
      ),
      'onchange'=>"loadFeed()",
      'style' =>      "width:160px;margin-bottom:5px;",
    ));

    $this->addElement('Select', 'category', array(
      'label' => 'Feed',
      'multiOptions' => array(
        '0' => 'All Feeds',
      ),
      'style' =>      "width:160px;margin-bottom:5px;",
    ));
   //start date
//    $startdate = new Engine_Form_Element_CalendarDateTime('start_date');
    $startdate = new Engine_Form_Element_Date('start_date');
    $startdate->setLabel("Start Date");
//    $startdate->setAllowEmpty(true);
//    $startdate->setRequired(false);
    $startdate->setAttrib('style', "width:50px;margin-bottom:5px;");
    $this->addElement($startdate);
    // End time
//    $enddate = new Engine_Form_Element_CalendarDateTime('end_date');
     $enddate = new Engine_Form_Element_Date('end_date');
    $enddate->setLabel("End Date");
//    $enddate->setAllowEmpty(true);
//    $enddate->setRequired(false);
    $enddate->setAttrib('style', "width:50px;margin-bottom:5px;");
    $this->addElement($enddate);
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