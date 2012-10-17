<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: Filter.php 9597 2012-01-11 22:06:55Z john $
 * @author     John
 */

/**
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 */
class Inviter_Form_InvitesFilter extends Engine_Form
{
    public function init()
    {
        $this
            ->clearDecorators()
            ->addDecorator('FormElements')
            ->addDecorator('Form')
            ->addDecorator('HtmlTag', array('tag' => 'div', 'class' => 'inviter-search'))
            ->addDecorator('HtmlTag2', array('tag' => 'div', 'class' => 'clear'));

        $this
            ->setAttribs(array(
            'class' => 'global_form_box',
        ))
            ->setMethod('POST');

        $providers = Engine_Api::_()->getDbtable('invites', 'inviter')->getUsedProviders(false);
        $provider = new Zend_Form_Element_Select('provider');
        $provider
            ->setLabel('Provider')
            ->clearDecorators()
            ->addDecorator('ViewHelper')
            ->addDecorator('Label', array('tag' => null, 'placement' => 'PREPEND'))
            ->addDecorator('HtmlTag', array('tag' => 'div'))
            ->setMultiOptions($providers);

        $recipient = new Zend_Form_Element_Text('recipient');
        $recipient
            ->setLabel('Recipient')
            ->clearDecorators()
            ->addDecorator('ViewHelper')
            ->addDecorator('Label', array('tag' => null, 'placement' => 'PREPEND'))
            ->addDecorator('HtmlTag', array('tag' => 'div'));

        $date = new Zend_Form_Element_Select('date');
        $date
            ->setLabel('Date Direction')
            ->clearDecorators()
            ->addDecorator('ViewHelper')
            ->addDecorator('Label', array('tag' => null, 'placement' => 'PREPEND'))
            ->addDecorator('HtmlTag', array('tag' => 'div'))
            ->setMultiOptions(array(0=>'', 'ASC'=>'Ascending', 'DESC'=>'Descending'));

        $submit = new Zend_Form_Element_Button('search', array('type' => 'submit'));
        $submit
            ->setLabel('Search')
            ->clearDecorators()
            ->addDecorator('ViewHelper')
            ->addDecorator('HtmlTag', array('tag' => 'div', 'class' => 'buttons'))
            ->addDecorator('HtmlTag2', array('tag' => 'div'));

        $this->addElements(array(
            $provider,
            $recipient,
            $date,
            $submit
        ));

        // Set default action without URL-specified params
        $params = array();
        foreach (array_keys($this->getValues()) as $key) {
            $params[$key] = null;
        }
        $this->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble($params));
    }
}