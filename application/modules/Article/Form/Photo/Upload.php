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
 
 
 
class Article_Form_Photo_Upload extends Engine_Form
{
  public function init()
  {
    // Init form
    $this
      ->setTitle('Add New Photos')
      ->setDescription('Choose photos on your computer to add to this article. (2MB maximum)')
      ->setAttrib('id', 'form-upload')
      ->setAttrib('class', 'global_form article_form_upload')
      ->setAttrib('name', 'albums_create')
      ->setAttrib('enctype','multipart/form-data')
      ->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()))
      ;

      /* OLD -----
    $fancyUpload = new Engine_Form_Element_FancyUpload('file');
    $fancyUpload->clearDecorators()
                ->addDecorator('FormFancyUpload')
                ->addDecorator('viewScript', array(
                  'viewScript' => '_FancyUpload.tpl',
                  'placement'  => '',
                  ));
    Engine_Form::addDefaultDecorators($fancyUpload);
    $this->addElement($fancyUpload);

    $this->addElement('Hidden', 'fancyuploadfileids');
    ---- END OLD */
    
    $this->addElement('FancyUpload', 'file');

    // Init submit
    $this->addElement('Button', 'submit', array(
      'label' => 'Save Photos',
      'type' => 'submit',
    ));
  }
}