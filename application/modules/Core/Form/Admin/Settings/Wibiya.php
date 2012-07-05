<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Core
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: Viglink.php 9478 2011-11-08 02:03:50Z john $
 * @author     John Boehr <john@socialengine.com>
 */

/**
 * @category   Application_Core
 * @package    Core
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 */
class Core_Form_Admin_Settings_Wibiya extends Engine_Form
{
  public function init()
  {
    // Set form attributes
    $this->setTitle('Wibiya Integration');
    
    $description = $this->getTranslator()->translate(
        'You can now easily integrate Wibiya into your site. Simply ' .
        'enter the URL to the javascript file below. ' .
        ' The URL should look ' . 
        'like this: %2$s'
    );
    $description = vsprintf($description, array(
      'http://www.wibiya.com/',
      'http://cdn.wibiya.com/Toolbars/dir_xxxx/Toolbar_xxxxxxx/Loader_xxxxxxx.js'
    ));
    $this->setDescription($description);
    
    // Decorators
    $this->loadDefaultDecorators();
    $this->getDecorator('Description')->setOption('escape', false);
    
    // element: src
    $this->addElement('text', 'src', array(
      'label' => 'Wibiya Javascript URL',
    ));
    
    // init submit
    $this->addElement('Button', 'submit', array(
      'label' => 'Save Changes',
      'type' => 'submit',
      'ignore' => true,
    ));
  }
}
