<?php

/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Core
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: Settings.php 9673 2012-04-11 22:49:36Z richard $
 * @author     Jung
 */

/**
 * @category   Application_Core
 * @package    Core
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 */
class Core_Form_Admin_Mail_Settings extends Engine_Form
{

  public function init()
  {
		
	$settings = Engine_Api::_()->getApi('settings', 'core');
	
	if( $settings->getSetting('user.support.links', 0) == 1 ) {
	  $moreinfo = $this->getTranslator()->translate( 
        'More Info: <a href="%1$s" target="_blank"> KB Article</a>');
	} else {
	  $moreinfo = $this->getTranslator()->translate( 
        '');
	}
	
    $description = vsprintf($moreinfo, array(
      'http://www.socialengine.net/support/documentation/article?q=181&question=Admin-Panel---Settings--Mail-Settings',
    ));
	
	// Decorators
    $this->loadDefaultDecorators();
	$this->getDecorator('Description')->setOption('escape', false);
  
    // Set form attributes
    $this->setTitle('Mail Settings');
	$this->setDescription($description);
    
    $this->addElement('Text', 'contact', array(
      'label' => 'Contact Form Email',
      'description' => 'Enter the email address you want contact form messages to be sent to.',
    ));

    // Element: mail_name
    $this->addElement('Text', 'name', array(
      'label' => 'From Name',
      'description' => 'Enter the name you want the emails from the system to come from in the field below.',
      'value' => 'Site Admin',
    ));

    // Element: mail_from
    $this->addElement('Text', 'from', array(
      'label' => 'From Address',
      'description' => 'Enter the email address you want the emails from the system to come from in the field below.',
      'value' => 'no-reply@' . $_SERVER['HTTP_HOST'],
    ));

    // Element: mail_count
    $this->addElement('Text', 'count', array(
      'label' => 'Mail Count',
      'description' => 'The number of emails to send out each time the Background Mailer task is run.',
      'validators' => array(
        array('Int', true),
        array('GreaterThan', true, array(0)),
      ),
    ));

    // Element: mail_queue
    $this->addElement('Radio', 'queueing', array(
      'label' => 'Email Queue',
      'description' => 'Utilizing an email queue, you can allow your website to throttle the emails being sent out to prevent overloading the mail server.',
      'required' => true,
      'multiOptions' => array(
        1 => 'Yes, enable email queue',
        0 => 'No, always send emails immediately',
      ),
     'value' => 1,
    ));

    // Element: mail_smtp
    $this->addElement('Radio', 'mail_smtp', array(
      'label' => 'Send through SMTP',
      'description' => 'Emails typically get sent through the web server using the PHP mail() function.  Alternatively you can have emails sent out using SMTP, usually requiring a username and password, and optionally using an external mail server.',
      'required' => false,
      'multiOptions' => array(
        0 => 'Use the built-in mail() function',
        1 => 'Send emails through an SMTP server',
      ),
      'value' => 0,
    ));

    // Element: mail_smtp_server
    $this->addElement('Text', 'mail_smtp_server', array(
      'label' => 'SMTP Server Address',
      'required' => false,
      'value' => '127.0.0.1',
    ));

    // Element: mail_smtp_port
    $this->addElement('Text', 'mail_smtp_port', array(
      'label' => 'SMTP Server Port',
      'description' => 'Default: 25. Also commonly on port 465 (SMTP over SSL) or port 587.',
      'required' => false,
      'value' => '25',
      'validators' => array(
        'Int'
      ),
    ));
    $this->mail_smtp_port->getDecorator("Description")->setOption("placement", "append");

    // Element: mail_smtp_authentication
    $this->addElement('Radio', 'mail_smtp_authentication', array(
      'label' => 'SMTP Authentication?',
      'description' => 'Does your SMTP Server require authentication?',
      'required' => false,
      'multiOptions' => array(
        1 => 'Yes',
        0 => 'No',
      ),
      'value' => 0,
    ));

    // Element: mail_smtp_username
    $this->addElement('Text', 'mail_smtp_username', array(
      'label' => 'SMTP Username',
    ));

    // Element: mail_smtp_password
    $this->addElement('Password', 'mail_smtp_password', array(
      'label' => 'SMTP Password',
      'description' => 'Leave blank to use previous.',
    ));
    $this->mail_smtp_password->getDecorator("Description")->setOption("placement", "append");

    // Element: mail_smtp_ssl
    $this->addElement('Radio', 'mail_smtp_ssl', array(
      'label' => 'Use SSL or TLS?',
      'required' => false,
      'multiOptions' => array(
        '' => 'None',
        'tls' => 'TLS',
        'ssl' => 'SSL',
      ),
      'value' => '',
    ));

    // Element: submit
    $this->addElement('Button', 'submit', array(
      'label' => 'Save Changes',
      'type' => 'submit',
      'ignore' => true,
    ));
  }

}