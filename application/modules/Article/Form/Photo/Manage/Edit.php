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
 
 
 
class Article_Form_Photo_Manage_Edit extends Engine_Form
{
  protected $_isArray = true;

  public function init()
  {
    $this->clearDecorators()
      ->addDecorator('FormElements');

    $this->addElement('Text', 'title', array(
      'label' => 'Title',
      'filters' => array(
        new Engine_Filter_Censor(),
        new Engine_Filter_HtmlSpecialChars(),
      ),
      'decorators' => array(
    'ViewHelper',
        array('HtmlTag', array('tag' => 'div', 'class'=>'articles_editphotos_title_input')),
        array('Label', array('tag' => 'div', 'placement' => 'PREPEND', 'class' => 'articles_editphotos_title')),
      ),
    ));

    $this->addElement('Textarea', 'description', array(
      'label' => 'Image Description',
      'rows' => 2,
      'cols' => 120,
      'filters' => array(
        new Engine_Filter_Censor(),
      ),
      'decorators' => array(
        'ViewHelper',
        array('HtmlTag', array('tag' => 'div', 'class'=>'articles_editphotos_caption_input')),
        array('Label', array('tag' => 'div', 'placement' => 'PREPEND', 'class'=>'articles_editphotos_caption_label')),
      ),
    ));

    $this->addElement('Checkbox', 'delete', array(
      'label' => "Delete Photo",
      'decorators' => array(
        'ViewHelper',
        array('Label', array('placement' => 'APPEND')),
        array('HtmlTag', array('tag' => 'div', 'class' => 'photo-delete-wrapper')),
      ),
    ));


    $this->addElement('Hidden', 'photo_id', array(
      'validators' => array(
        'Int',
      )
    ));
  }
}