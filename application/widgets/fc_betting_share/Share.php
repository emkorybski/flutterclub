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
			->setDescription('Share details of this bet and an optional message')
			->setAttrib('class', 'global_form_popup')
			->setAction($_SERVER['REQUEST_URI']);

		$this->addElement('Textarea', 'message', array(
			'allowEmpty' => true,
			'required' => false,
			'placeholder' => 'Write a message...',
			'rows' => '3',
		));

		$this->addElement('Button', 'submit', array(
			'label' => 'Share',
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