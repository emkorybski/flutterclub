<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Inviter
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Level.php 2010-07-02 19:54 mirlan $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Inviter
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Inviter_Form_Admin_Level extends Engine_Form
{
  public function init()
  {
    $this
      ->setTitle('INVITER_Member Level Settings')
      ->setDescription("INVITER_FORM_ADMIN_LEVEL_DESCRIPTION");

    $this->loadDefaultDecorators();
    $this->getDecorator('Description')->setOptions(array('tag' => 'h4', 'placement' => 'PREPEND'));

    // prepare user levels
    $table = Engine_Api::_()->getDbtable('levels', 'authorization');
    $select = $table->select();
    $user_levels = $table->fetchAll($select);
    
    foreach ($user_levels as $user_level){
      $levels_prepared[$user_level->level_id]= $user_level->getTitle();
    }
    
    // category field
    $this->addElement('Select', 'level_id', array(
      'label' => 'Member Level',
      'multiOptions' => $levels_prepared,
      'onchange' => 'javascript:fetchLevelSettings(this.value);',
      'ignore' => true
    ));
    
    $this->addElement('Radio', 'use', array(
      'label' => 'INVITER_Allow Using of Inviter?',
      'description' => 'INVITER_Do you want to let members use inviter?',
      'multiOptions' => array(
        0 => 'INVITER_No, do not allow use inviter.',
        1 => 'INVITER_Yes, allow use inviter.',
      ),
      'value' => 1,
    ));

      $this->addElement('MultiCheckbox', 'introduction', array(
              'label' => 'INVITER_Introduction levels title',
              'description' => 'INVITER_Introduction levels description',
              'multiOptions' => $levels_prepared,
              'value' => $levels_prepared
            ));

//    $this->addElement('MultiCheckbox', 'sex', array(
//        'label' => 'INVITER_Introduction sex title',
//        'description' => 'INVITER_Introduction sex description',
//        'multiOptions' => $levels_prepared,
//        'value' => $levels_prepared
//    ));

    $this->addElement('Button', 'submit', array(
      'label' => 'Save Settings',
      'type' => 'submit',
      'ignore' => true
    ));

  }
}