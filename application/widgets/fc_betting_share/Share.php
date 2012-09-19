<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: Add.php 7564 2010-10-05 23:10:16Z john $
 * @author     John
 */

/**
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 */
class FC_Betting_Share extends Engine_Form
{
	public function init()
	{
		$this->setTitle('Share Bet')
			->setDescription('Share Bet Description')
			->setAttrib('class', 'global_form_popup')
			->setAction($_SERVER['REQUEST_URI']);

		$this->addElement('Text', 'message', array(
			'label' => 'Enter your message',
			'allowEmpty' => true,
			'required' => false,
		));

		$this->addElement('Button', 'submit', array(
			'label' => 'Share Bet',
			'type' => 'submit',
			'ignore' => true,
			'decorators' => array('ViewHelper')
		));

		$this->addElement('Cancel', 'cancel', array(
			'label' => 'cancel',
			'link' => true,
			'prependText' => ' or ',
			'href' => '',
			'onclick' => 'parent.Smoothbox.close();',
			'decorators' => array(
				'ViewHelper'
			)
		));
		$this->addDisplayGroup(array('submit', 'cancel'), 'buttons');
	}
}