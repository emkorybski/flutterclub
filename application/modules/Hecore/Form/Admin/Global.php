<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Page
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Global.php 2010-08-31 16:05 idris $
 * @author     Idris
 */

/**
 * @category   Application_Extensions
 * @package    Page
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Hecore_Form_Admin_Global extends Engine_Form
{
  public function init()
  {
    $this
      ->setTitle('Hire Experts Core Module Global Settings')
      ->setDescription('HECORE_FORM_ADMIN_GLOBAL_DESCRIPTION');

    $settings = Engine_Api::_()->getApi('settings', 'core');
    $privacy = $settings->getSetting('hecore.friend.widget.privacy');
    $privacy = $privacy ? unserialize($privacy) : array('everyone', 'registered', 'owner_network', 'owner_member_member', 'owner_member', 'owner');

    // Element: auth_view
    $this->addElement('MultiCheckbox', 'privacy', array(
      'label' => 'Friend Widget Privacy',
      'description' => 'HECORE_FRIEND_WIDGET_FORM_ADMIN_LEVEL_PRIVACY_DESCRIPTION',
      'multiOptions' => array(
        'everyone'            => 'Everyone',
        'registered'          => 'All Registered Members',
        'owner_network'       => 'Friends and Networks',
        'owner_member_member' => 'Friends of Friends',
        'owner_member'        => 'Friends Only',
        'owner'               => 'Just Me'
      ),
      'value' => $privacy,
    ));

    $this->addElement('Checkbox', 'listing', array(
      'label' => 'HECORE_FRIEND_WIDGET_FORM_ADMIN_LEVEL_LISTING_DESCRIPTION',
      'description' => 'Make Friends Sticky?',
      'value' => $settings->getSetting('hecore.friend.widget.listing', 1),
    ));

    $this->addElement('Button', 'submit', array(
        'label' => 'Save Changes',
        'type' => 'submit'
    ));
  }
}