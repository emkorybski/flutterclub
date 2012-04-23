<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: General.php 9610 2012-01-23 23:44:23Z john $
 * @author     Steve
 */

/**
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 */
class User_Form_Settings_General extends Engine_Form
{
  protected $_item;

  public function setItem(User_Model_User $item)
  {
    $this->_item = $item;
  }

  public function getItem()
  {
    if( null === $this->_item ) {
      throw new User_Model_Exception('No item set in ' . get_class($this));
    }

    return $this->_item;
  }

  public function init()
  {
    // @todo fix form CSS/decorators
    // @todo replace fake values with real values
    $this->setTitle('General Settings')
      ->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()))
      ;

    // Init email
    $this->addElement('Text', 'email', array(
      'label' => 'Email Address',
      'required' => true,
      'allowEmpty' => false,
      'validators' => array(
        array('NotEmpty', true),
        array('EmailAddress', true),
        array('Db_NoRecordExists', true, array(Engine_Db_Table::getTablePrefix().'users', 'email', array('field' => 'user_id', 'value' => $this->getItem()->getIdentity())))
      ),
      'filters' => array(
        'StringTrim'
      )
    ));
    $this->email->getValidator('NotEmpty')->setMessage('Please enter a valid email address.', 'isEmpty');
    $this->email->getValidator('Db_NoRecordExists')->setMessage('Someone has already registered this email address, please use another one.', 'recordFound');

    // Init username
    if( Engine_Api::_()->getApi('settings', 'core')->getSetting('user.signup.username', 1) > 0 ) {
      $this->addElement('Text', 'username', array(
        'label' => 'Profile Address',
        'required' => true,
        'allowEmpty' => false,
        'validators' => array(
          array('NotEmpty', true),
          array('Alnum', true),
          array('StringLength', true, array(4, 64)),
          array('Regex', true, array('/^[a-z][a-z0-9]*$/i')),
          array('Db_NoRecordExists', true, array(Engine_Db_Table::getTablePrefix().'users', 'username', array('field' => 'user_id', 'value' => $this->getItem()->getIdentity())))
        ),
      ));
      $this->username->getValidator('NotEmpty')->setMessage('Please enter a valid profile address.', 'isEmpty');
      $this->username->getValidator('Db_NoRecordExists')->setMessage('Someone has already picked this profile address, please use another one.', 'recordFound');
      $this->username->getValidator('Regex')->setMessage('Profile addresses must start with a letter.', 'regexNotMatch');
      $this->username->getValidator('Alnum')->setMessage('Profile addresses must be alphanumeric.', 'notAlnum');
    }
    
    // Init type
    $this->addElement('Select', 'accountType', array(
      'label' => 'Account Type',
    ));


    // Init Facebook
    $facebook_enable = Engine_Api::_()->getApi('settings', 'core')
        ->getSetting('core_facebook_enable', 'none');
    if( 'none' != $facebook_enable ) {
      $desc = 'Linking your Facebook account will let you login with Facebook';
      if( 'publish' == $facebook_enable ) {
        $desc .= ' and publish content to your Facebook wall.';
      } else {
        $desc .= '.';
      }
      $this->addElement('Dummy', 'facebook', array(
        'label' => 'Facebook Integration',
        'description' => $desc,
        'content' => User_Model_DbTable_Facebook::loginButton('Integrate with my Facebook'),
      ));
      $this->addElement('Checkbox', 'facebook_id', array(
        'label' => 'Integrate with my Facebook',
        'description' => 'Facebook Integration',
      ));
    }

    
    // Init Twitter
    $twitter_enable = Engine_Api::_()->getApi('settings', 'core')
        ->getSetting('core_twitter_enable', 'none');
    if( 'none' != $twitter_enable ) {
      $desc = 'Linking your Twitter account will let you login with Twitter';
      if( 'publish' == $facebook_enable ) {
        $desc .= ' and publish content to your Twitter feed.';
      } else {
        $desc .= '.';
      }
      $this->addElement('Dummy', 'twitter', array(
        'label' => 'Twitter Integration',
        'description' => $desc,
        'content' => User_Model_DbTable_Twitter::loginButton('Integrate with my Twitter'),
      ));
      $this->addElement('Checkbox', 'twitter_id', array(
        'label' => 'Integrate with my Twitter',
        'description' => 'Twitter Integration',
      ));
    }
    
    $janrain_enable = Engine_Api::_()->getApi('settings', 'core')
        ->getSetting('core_janrain_enable', 'none');
    if( $janrain_enable && $janrain_enable != 'none' ) {
      // Check if already linked
      $janrainTable = Engine_Api::_()->getDbtable('janrain', 'user');
      $janrainExists = $janrainTable->select()
          ->from($janrainTable, new Zend_Db_Expr('TRUE'))
          ->where('user_id = ?', $this->getItem()->getIdentity())
          ->limit(1)
          ->query()
          ->fetchColumn()
          ;
      if( !$janrainExists ) {
        $desc = 'Linking another account will let you login using that account.';
        $this->addElement('Dummy', 'janrain', array(
          'label' => 'Social Integration',
          'description' => $desc,
          'content' => User_Model_DbTable_Janrain::loginButton('page'),
        ));
      } else {
        $this->addElement('Radio', 'janrainnoshare', array(
          'label' => 'Share Dialog',
          'description' => 'Do you want the option to share a post to ' . 
              'facebook or twitter to be displayed after posting?',
          'multiOptions' => array(
            '0' => 'Yes, display the dialog.',
            '1' => 'No, do not display the dialog.',
          ),
          'value' => 0,
        ));
      }
    }
    

    // Init timezone
    $this->addElement('Select', 'timezone', array(
      'label' => 'Timezone',
      'description' => 'Select the city closest to you that shares your same timezone.',
      'multiOptions' => array(
        'US/Pacific'  => '(UTC-8) Pacific Time (US & Canada)',
        'US/Mountain' => '(UTC-7) Mountain Time (US & Canada)',
        'US/Central'  => '(UTC-6) Central Time (US & Canada)',
        'US/Eastern'  => '(UTC-5) Eastern Time (US & Canada)',
        'America/Halifax'   => '(UTC-4)  Atlantic Time (Canada)',
        'America/Anchorage' => '(UTC-9)  Alaska (US & Canada)',
        'Pacific/Honolulu'  => '(UTC-10) Hawaii (US)',
        'Pacific/Samoa'     => '(UTC-11) Midway Island, Samoa',
        'Etc/GMT-12' => '(UTC-12) Eniwetok, Kwajalein',
        'Canada/Newfoundland' => '(UTC-3:30) Canada/Newfoundland',
        'America/Buenos_Aires' => '(UTC-3) Brasilia, Buenos Aires, Georgetown',
        'Atlantic/South_Georgia' => '(UTC-2) Mid-Atlantic',
        'Atlantic/Azores' => '(UTC-1) Azores, Cape Verde Is.',
        'Europe/London' => 'Greenwich Mean Time (Lisbon, London)',
        'Europe/Berlin' => '(UTC+1) Amsterdam, Berlin, Paris, Rome, Madrid',
        'Europe/Athens' => '(UTC+2) Athens, Helsinki, Istanbul, Cairo, E. Europe',
        'Europe/Moscow' => '(UTC+3) Baghdad, Kuwait, Nairobi, Moscow',
        'Iran' => '(UTC+3:30) Tehran',
        'Asia/Dubai' => '(UTC+4) Abu Dhabi, Kazan, Muscat',
        'Asia/Kabul' => '(UTC+4:30) Kabul',
        'Asia/Yekaterinburg' => '(UTC+5) Islamabad, Karachi, Tashkent',
        'Asia/Dili' => '(UTC+5:30) Bombay, Calcutta, New Delhi',
        'Asia/Katmandu' => '(UTC+5:45) Nepal',
        'Asia/Omsk' => '(UTC+6) Almaty, Dhaka',
        'India/Cocos' => '(UTC+6:30) Cocos Islands, Yangon',
        'Asia/Krasnoyarsk' => '(UTC+7) Bangkok, Jakarta, Hanoi',
        'Asia/Hong_Kong' => '(UTC+8) Beijing, Hong Kong, Singapore, Taipei',
        'Asia/Tokyo' => '(UTC+9) Tokyo, Osaka, Sapporto, Seoul, Yakutsk',
        'Australia/Adelaide' => '(UTC+9:30) Adelaide, Darwin',
        'Australia/Sydney' => '(UTC+10) Brisbane, Melbourne, Sydney, Guam',
        'Asia/Magadan' => '(UTC+11) Magadan, Soloman Is., New Caledonia',
        'Pacific/Auckland' => '(UTC+12) Fiji, Kamchatka, Marshall Is., Wellington',
      ),
    ));

    // Init default locale
    $locale = Zend_Registry::get('Locale');

    $localeMultiKeys = array_merge(
      array_keys(Zend_Locale::getLocaleList())
    );
    $localeMultiOptions = array();
    $languages = Zend_Locale::getTranslationList('language', $locale);
    $territories = Zend_Locale::getTranslationList('territory', $locale);
    foreach($localeMultiKeys as $key)
    {     
       if (!empty($languages[$key])) 
       {
         $localeMultiOptions[$key] = $languages[$key];
       }
       else
       {
         $locale = new Zend_Locale($key);
         $region = $locale->getRegion();
         $language = $locale->getLanguage(); 
         if ((!empty($languages[$language]) && (!empty($territories[$region])))) {
           $localeMultiOptions[$key] =  $languages[$language] . ' (' . $territories[$region] . ')';
         }
       }
    }
    $localeMultiOptions = array_merge(array('auto'=>'[Automatic]'), $localeMultiOptions);
    
    $this->addElement('Select', 'locale', array(
      'label' => 'Locale',
      'description' => 'Dates, times, and other settings will be displayed using this locale setting.',
      'multiOptions' => $localeMultiOptions
    ));

    
    // Init submit
    $this->addElement('Button', 'submit', array(
      'label' => 'Save Changes',
      'type' => 'submit',
      'ignore' => true,
    ));
    
    // Create display group for buttons
    #$this->addDisplayGroup($emailAlerts, 'checkboxes');

    // Set default action
    $this->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array(
       'module' => 'user',
       'controller' => 'settings',
       'action' => 'general',
    ), 'default'));
  }
}