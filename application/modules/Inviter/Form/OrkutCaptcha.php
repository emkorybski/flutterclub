<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Inviter
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Import.php 2010-07-02 19:54 mirlan $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Inviter
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Inviter_Form_OrkutCaptcha extends Engine_Form
{
    public function init()
    {
        $this->setDescription('INVITER_FROM_IMPORT_DESCRIPTION')
            ->clearDecorators()
            ->clearAttribs()
            ->setAttrib('id', 'invite_friends');


        $img = new Zend_Form_Element_Image('captcha_image');
        $this->addElement($img);

        $this->addElement('text', 'capthca_value', array(
            'label' => 'Type the text',
            'required' => true,
            'autocomplete' => 'on',
            'allowEmpty' => false,
            'validators' => array(
                array('NotEmpty', true),),
            'trim' => true,
            'order' => 2,
            'style' => 'margin-top: 8px; width: 180px;'
        ));

        $this->addElement('button', 'submit', array(
            'label' => 'Send',
            'type' => 'submit',
            'ignore' => true,
            'order' => 4,
            'style' => 'margin-top: 5px;'
        ));

    }

    public function isValid($data)
    {
        $valid = true;
        return $valid;
    }
}