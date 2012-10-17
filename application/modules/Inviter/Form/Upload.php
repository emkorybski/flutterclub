<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Inviter
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Upload.php 2010-07-02 19:54 mirlan $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Inviter
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Inviter_Form_Upload extends Engine_Form
{
  public function init()
  {
    $this->setDescription('INVITER_FORM_UPDLOAD_DESCRIPTION')
      ->clearDecorators()
      ->clearAttribs()
      ->setAttrib('name', 'upload_contacts')
      ->setAttrib('id', 'form-upload')
      ->setAction('inviter/index/upload-contacts')
      ->setAttrib('enctype','multipart/form-data');

    $fancyUpload = new Engine_Form_Element_FancyUpload('file');
    $fancyUpload->clearDecorators()
                ->addDecorator('FormFancyUpload')
                ->addDecorator('viewScript', array(
                  'viewScript' => '_FancyUpload.tpl',
                  'placement'  => '',
                  ));

    $this->addElement($fancyUpload);

    $path = Engine_Api::_()->getModuleBootstrap('inviter')->getModulePath();
    $this->addDisplayGroupPrefixPath('Engine_Form_Decorator_', $path . '/Form/Decorator/', 'Decorator');
    $this->addDisplayGroup(array_keys($this->getElements()), 'from_elements');

    $this->from_elements->addDecorator('DefaultUploads');
  }
}