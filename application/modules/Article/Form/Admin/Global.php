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
 
 
 
class Article_Form_Admin_Global extends Engine_Form
{
  public function init()
  {
    $this
      ->setTitle('Global Settings')
      ->setDescription('These settings affect all members in your community.');
    
    $this->addElement('Text', 'article_license', array(
      'label' => 'Article License Key',
      'description' => 'Enter the your license key that is provided to you when you purchased this plugin. If you do not know your license key, please contact Radcodes support team.',
      'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('article.license', 'XXXX-XXXX-XXXX-XXXX'),
      'filters' => array(
        'StringTrim'
      ),
      'allowEmpty' => false,
      'validators' => array(
        new Radcodes_Lib_Validate_License('article'),
      ),
    ));
      
      /*
    $this->addElement('Radio', 'article_public', array(
      'label' => 'Public Permissions',
      'description' => "ARTICLE_FORM_ADMIN_GLOBAL_ARTICLEPUBLIC_DESCRIPTION",
      'multiOptions' => array(
        1 => 'Yes, the public can view articles unless they are made private.',
        0 => 'No, the public cannot view articles.'
      ),
      'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('article.public', 1),
    ));
      */
    
    $this->addElement('Radio', 'article_sorting', array(
      'label' => 'Sorting Priority',
      'description' => "If you would like sponsored articles, or featured articles etc.. to show on top of browse page, you can apply pre-sorted order on articles:",
      'multiOptions' => array(
				0 => "User preference",
        1 => "Sponsored articles, then user preference",
        2 => "Sponsored articles, featured articles, then user preference",
				3 => "Featured articles, then user preference",
				4 => "Featured articles, sponsored articles, then user preference",
      ),
      'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('article.sorting', 0),
    ));
      
    $this->addElement('Text', 'article_page', array(
      'label' => 'Articles Per Page',
      'description' => 'ARTICLE_FORM_ADMIN_GLOBAL_ARTICLE_PAGE_DESCRIPTION',
      'class' => 'short',
      'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('article.page', 10),
      'validators' => array(
        'Digits',
        new Zend_Validate_Between(1,100),
      ),
    ));

    /*
    $this->addElement('Text', 'article_gallery', array(
      'label' => 'Article Photos',
      'description' => 'How many photos will be shown on article\'s photo gallery section? (Enter a number between 0 and 999)',
      'class' => 'short',
      'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('article.gallery', 4),
    ));
    
    $this->addElement('Radio', 'article_showmainphoto', array(
      'label' => 'Main Photo Cover',
      'description' => 'If the article has main photo cover, would you like to show it on the article view page?',
      'multiOptions' => array(
        1 => 'Yes',
        0 => 'No'
      ),
      'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('article.showmainphoto', 0),
    ));
    */

    
    // Add submit button
    $this->addElement('Button', 'submit', array(
      'label' => 'Save Changes',
      'type' => 'submit',
      'ignore' => true
    ));
  }
}