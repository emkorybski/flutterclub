<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Inviter
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Level.php 2010-03-31 10:15 ermek $
 * @author     Ermek
 */

/**
 * @category   Application_Extensions
 * @package    Inviter
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Inviter_Form_Admin_Providers extends Engine_Form
{
    public function init()
    {
        $module_path = Engine_Api::_()->getModuleBootstrap('inviter')->getModulePath();
        $he_module_path = Engine_Api::_()->getModuleBootstrap('hecore')->getModulePath();
        $this->addPrefixPath('Engine_Form_Decorator_', $module_path . '/Form/Decorator/', 'decorator');

        $this
            ->setTitle('INVITER_Providers Settings')
            ->setDescription("INVITER_FORM_ADMIN_PROVIDERS_DESCRIPTION")
            ->setOptions(array('class' => 'he_inviter_settings'));

        $settings = Engine_Api::_()->getApi('settings', 'core');
        $translate = $this->getTranslator();

        // Facebook
        $this->addElement('Text', 'inviter_facebook_consumer_key', array(
            'label' => 'Facebook App ID',
            'value' => $settings->getSetting('inviter.facebook.consumer.key', ''),
        ));

        $this->addElement('Text', 'inviter_facebook_consumer_secret', array(
            'label' => 'Facebook App Secret',
            'value' => $settings->getSetting('inviter.facebook.consumer.secret', ''),
        ));

        $this->addElement('Image', 'facebook', array('class' => 'inviter_loader_hidden'));
        $this->getElement('facebook')->setImage("application/modules/Inviter/externals/images/loader.gif");
        $this->getElement('facebook')->setDecorators(array('ViewHelper'));
        $this->addElement('Button', 'save_fb', array('label' => $translate->_('Save Credentials'), 'class' => 'provider-action-button'));
        $this->addElement('Button', 'clear_fb', array('label' => $translate->_('INVITER_Clear Credentials'), 'class' => 'provider-action-button provider-action-button-right'));
        $this->getElement('save_fb')->setDecorators(array('ViewHelper'));
        $this->getElement('clear_fb')->setDecorators(array('ViewHelper'));

        $this->addDisplayGroup(
            array('inviter_facebook_consumer_key', 'inviter_facebook_consumer_secret', 'save_fb', 'clear_fb', 'facebook'),
            'facebook_settings',
            array('class' => 'he_setting_fieldset')
        );

        $this->getDisplayGroup('facebook_settings')->addDecorator(
            'ProviderDescription',
            array('label' => $translate->_('Facebook settings'), 'description' => $translate->_('INVITER_FACEBOOK_APP_DESC')));

        // Twitter
        $this->addElement('Text', 'inviter_twitter_consumer_key', array(
            'label' => 'Consumer Key',
            'value' => $settings->getSetting('inviter.twitter.consumer.key', ''),
        ));

        $this->addElement('Text', 'inviter_twitter_consumer_secret', array(
            'label' => 'Consumer Secret',
            'value' => $settings->getSetting('inviter.twitter.consumer.secret', ''),
        ));

        $this->addElement('Image', 'twitter', array('class' => 'inviter_loader_hidden'));
        $this->getElement('twitter')->setImage("application/modules/Inviter/externals/images/loader.gif");
        $this->getElement('twitter')->setDecorators(array('ViewHelper'));
        $this->addElement('Button', 'save_tw', array('label' => $translate->_('Save Credentials'), 'class' => 'provider-action-button'));
        $this->addElement('Button', 'clear_tw', array('label' => $translate->_('INVITER_Clear Credentials'), 'class' => 'provider-action-button provider-action-button-right'));
        $this->getElement('save_tw')->setDecorators(array('ViewHelper'));
        $this->getElement('clear_tw')->setDecorators(array('ViewHelper'));

        $this->addDisplayGroup(
            array('inviter_twitter_consumer_key', 'inviter_twitter_consumer_secret', 'save_tw', 'clear_tw', 'twitter'),
            'twitter_settings',
            array('class' => 'he_setting_fieldset')
        );

        $this->getDisplayGroup('twitter_settings')->addDecorator(
            'ProviderDescription',
            array('label' => $translate->_('Twitter settings'), 'description' => $translate->_('INVITER_TWITTER_APP_DESC')));

        // LinkedIn
        $this->addElement('Text', 'inviter_linkedin_consumer_key', array(
            'label' => 'API Key',
            'value' => $settings->getSetting('inviter.linkedin.consumer.key', ''),
        ));

        $this->addElement('Text', 'inviter_linkedin_consumer_secret', array(
            'label' => 'Secret Key',
            'value' => $settings->getSetting('inviter.linkedin.consumer.secret', ''),
        ));

        $this->addElement('Image', 'linkedin', array('class' => 'inviter_loader_hidden'));
        $this->getElement('linkedin')->setImage("application/modules/Inviter/externals/images/loader.gif");
        $this->getElement('linkedin')->setDecorators(array('ViewHelper'));
        $this->addElement('Button', 'save_ld', array('label' => $translate->_('Save Credentials'), 'class' => 'provider-action-button'));
        $this->addElement('Button', 'clear_ld', array('label' => $translate->_('INVITER_Clear Credentials'), 'class' => 'provider-action-button provider-action-button-right'));
        $this->getElement('save_ld')->setDecorators(array('ViewHelper'));
        $this->getElement('clear_ld')->setDecorators(array('ViewHelper'));

        $this->addDisplayGroup(
            array('inviter_linkedin_consumer_key', 'inviter_linkedin_consumer_secret', 'save_ld', 'clear_ld', 'linkedin'),
            'linkedin_settings',
            array('class' => 'he_setting_fieldset')
        );

        $this->getDisplayGroup('linkedin_settings')->addDecorator(
            'ProviderDescription',
            array('label' => $translate->_('LinkedIn settings'), 'description' => $translate->_('INVITER_LINKEDIN_APP_DESC')));


        // GMail
        $this->addElement('Text', 'inviter_gmail_consumer_key', array(
            'label' => 'OAuth Consumer Key',
            'value' => $settings->getSetting('inviter.gmail.consumer.key', ''),
        ));

        $this->addElement('Text', 'inviter_gmail_consumer_secret', array(
            'label' => 'OAuth Consumer Secret',
            'value' => $settings->getSetting('inviter.gmail.consumer.secret', ''),
        ));

        $this->addElement('Image', 'gmail', array('class' => 'inviter_loader_hidden'));
        $this->getElement('gmail')->setImage("application/modules/Inviter/externals/images/loader.gif");
        $this->getElement('gmail')->setDecorators(array('ViewHelper'));
        $this->addElement('Button', 'save_gm', array('label' => $translate->_('Save Credentials'), 'class' => 'provider-action-button'));
        $this->addElement('Button', 'clear_gm', array('label' => $translate->_('INVITER_Clear Credentials'), 'class' => 'provider-action-button provider-action-button-right'));
        $this->getElement('save_gm')->setDecorators(array('ViewHelper'));
        $this->getElement('clear_gm')->setDecorators(array('ViewHelper'));

        $this->addDisplayGroup(
            array('inviter_gmail_consumer_key', 'inviter_gmail_consumer_secret', 'save_gm', 'clear_gm', 'gmail'),
            'gmail_settings',
            array('class' => 'he_setting_fieldset')
        );

        $this->getDisplayGroup('gmail_settings')->addDecorator(
            'ProviderDescription',
            array('label' => $translate->_('GMail settings'), 'description' => $translate->_('INVITER_GMAIL_APP_DESC')));

        // Yahoo
        $this->addElement('Text', 'inviter_yahoo_consumer_key', array(
            'label' => 'Consumer Key',
            'value' => $settings->getSetting('inviter.yahoo.consumer.key', ''),
        ));

        $this->addElement('Text', 'inviter_yahoo_consumer_secret', array(
            'label' => 'Consumer Secret',
            'value' => $settings->getSetting('inviter.yahoo.consumer.secret', ''),
        ));

        $this->addElement('Image', 'yahoo', array('class' => 'inviter_loader_hidden'));
        $this->getElement('yahoo')->setImage("application/modules/Inviter/externals/images/loader.gif");
        $this->getElement('yahoo')->setDecorators(array('ViewHelper'));
        $this->addElement('Button', 'save_ya', array('label' => $translate->_('Save Credentials'), 'class' => 'provider-action-button'));
        $this->addElement('Button', 'clear_ya', array('label' => $translate->_('INVITER_Clear Credentials'), 'class' => 'provider-action-button provider-action-button-right'));
        $this->getElement('save_ya')->setDecorators(array('ViewHelper'));
        $this->getElement('clear_ya')->setDecorators(array('ViewHelper'));

        $this->addDisplayGroup(
            array('inviter_yahoo_consumer_key', 'inviter_yahoo_consumer_secret', 'save_ya', 'clear_ya', 'yahoo'),
            'yahoo_settings',
            array('class' => 'he_setting_fieldset')
        );

        $this->getDisplayGroup('yahoo_settings')->addDecorator(
            'ProviderDescription',
            array('label' => $translate->_('Yahoo settings'), 'description' => $translate->_('INVITER_YAHOO_APP_DESC')));

        // hotmail
        $this->addElement('Text', 'inviter_hotmail_consumer_key', array(
            'label' => 'Client ID',
            'value' => $settings->getSetting('inviter.hotmail.consumer.key', ''),
        ));

        $this->addElement('Text', 'inviter_hotmail_consumer_secret', array(
            'label' => 'Client secret',
            'value' => $settings->getSetting('inviter.hotmail.consumer.secret', ''),
        ));

        $this->addElement('Image', 'hotmail', array('class' => 'inviter_loader_hidden'));
        $this->getElement('hotmail')->setImage("application/modules/Inviter/externals/images/loader.gif");
        $this->getElement('hotmail')->setDecorators(array('ViewHelper'));
        $this->addElement('Button', 'save_ms', array('label' => $translate->_('Save Credentials'), 'class' => 'provider-action-button'));
        $this->addElement('Button', 'clear_ms', array('label' => $translate->_('INVITER_Clear Credentials'), 'class' => 'provider-action-button provider-action-button-right'));
        $this->getElement('save_ms')->setDecorators(array('ViewHelper'));
        $this->getElement('clear_ms')->setDecorators(array('ViewHelper'));

        $this->addDisplayGroup(
            array('inviter_hotmail_consumer_key', 'inviter_hotmail_consumer_secret', 'save_ms', 'clear_ms', 'hotmail'),
            'hotmail_settings',
            array('class' => 'he_setting_fieldset')
        );

        $this->getDisplayGroup('hotmail_settings')->addDecorator(
            'ProviderDescription',
            array('label' => $translate->_('Live/Hotmail/MSN settings'), 'description' => $translate->_('INVITER_HOTMAIL_APP_DESC')));


        // last.fm
        $this->addElement('Image', 'lastfm', array('class' => 'inviter_loader_hidden'));
        $this->getElement('lastfm')->setImage("application/modules/Inviter/externals/images/loader.gif");
        $this->getElement('lastfm')->setDecorators(array('ViewHelper'));
        $this->addElement('Text', 'inviter_lastfm_api_key', array(
            'label' => 'Api key',
            'value' => $settings->getSetting('inviter.lastfm.api.key', ''),
        ));

        $this->addElement('Text', 'inviter_lastfm_secret', array(
            'label' => 'Secret',
            'value' => $settings->getSetting('inviter.lastfm.secret', ''),
        ));

        $this->addElement('Button', 'save_lf', array('label' => $translate->_('Save Credentials'), 'class' => 'provider-action-button'));
        $this->addElement('Button', 'clear_lf', array('label' => $translate->_('INVITER_Clear Credentials'), 'class' => 'provider-action-button provider-action-button-right'));
        $this->getElement('save_lf')->setDecorators(array('ViewHelper'));
        $this->getElement('clear_lf')->setDecorators(array('ViewHelper'));

        $this->addDisplayGroup(
            array('inviter_lastfm_api_key', 'inviter_lastfm_secret', 'save_lf', 'clear_lf', 'lastfm'),
            'lastfm_settings',
            array('class' => 'he_setting_fieldset')
        );

        $this->getDisplayGroup('lastfm_settings')->addDecorator(
            'ProviderDescription',
            array('label' => $translate->_('Last.fm settings'), 'description' => $translate->_('INVITER_LASTFM_APP_DESC')));


        // myspace

        //    $this->addElement('Text', 'inviter_myspace_consumer_key', array(
        //        'label' => 'Consumer Key',
        //        'value' => $settings->getSetting('inviter.myspace.consumer.key', ''),
        //    ));
        //
        //  $this->addElement('Text', 'inviter_myspace_consumer_secret', array(
        //    'label' => 'Consumer Secret',
        //    'value' => $settings->getSetting('inviter.myspace.consumer.secret', ''),
        //  ));
        //
        //  $this->addDisplayGroup(
        //    array('inviter_myspace_consumer_key', 'inviter_myspace_consumer_secret'),
        //    'myspace_settings',
        //    array('class' => 'he_setting_fieldset')
        //  );
        //
        //  $this->getDisplayGroup('myspace_settings')->addDecorator(
        //    'ProviderDescription',
        //    array('label' => $translate->_('MySpace settings'), 'description' => $translate->_('INVITER_MYSPACE_APP_DESC')));


        // foursquare

        $this->addElement('Text', 'inviter_foursquare_consumer_key', array(
            'label' => 'Client ID',
            'value' => $settings->getSetting('inviter.foursquare.consumer.key', ''),
        ));

        $this->addElement('Text', 'inviter_foursquare_consumer_secret', array(
            'label' => 'Client Secret',
            'value' => $settings->getSetting('inviter.foursquare.consumer.secret', ''),
        ));

        $this->addElement('Image', 'foursquare', array('class' => 'inviter_loader_hidden'));
        $this->getElement('foursquare')->setImage("application/modules/Inviter/externals/images/loader.gif");
        $this->getElement('foursquare')->setDecorators(array('ViewHelper'));
        $this->addElement('Button', 'save_16', array('label' => $translate->_('Save Credentials'), 'class' => 'provider-action-button'));
        $this->addElement('Button', 'clear_16', array('label' => $translate->_('INVITER_Clear Credentials'), 'class' => 'provider-action-button provider-action-button-right'));
        $this->getElement('save_16')->setDecorators(array('ViewHelper'));
        $this->getElement('clear_16')->setDecorators(array('ViewHelper'));

        $this->addDisplayGroup(
            array('inviter_foursquare_consumer_key', 'inviter_foursquare_consumer_secret', 'save_16', 'clear_16', 'foursquare'),
            'foursquare_settings',
            array('class' => 'he_setting_fieldset')
        );

        $this->getDisplayGroup('foursquare_settings')->addDecorator(
            'ProviderDescription',
            array('label' => $translate->_('Foursquare settings'), 'description' => $translate->_('INVITER_FOUR_APP_DESC')));


        // mail.ru

        $this->addElement('Text', 'inviter_mailru_id', array(
            'label' => 'ID',
            'value' => $settings->getSetting('inviter.mailru.id', ''),
        ));

        $this->addElement('Text', 'inviter_mailru_private_key', array(
            'label' => 'Private Key',
            'value' => $settings->getSetting('inviter.mailru.private.key', ''),
        ));
        $this->addElement('Text', 'inviter_mailru_secret_key', array(
            'label' => 'Secret Key',
            'value' => $settings->getSetting('inviter.mailru.secret.key', ''),
        ));

        $this->addElement('Image', 'mailru', array('class' => 'inviter_loader_hidden', 'onClick'=>'return false', 'href'=>'javascript:void(0);'));
        $this->getElement('mailru')->setImage("application/modules/Inviter/externals/images/loader.gif");
        $this->getElement('mailru')->setDecorators(array('ViewHelper'));
        $this->addElement('Button', 'save_mr', array('label' => $translate->_('Save Credentials'), 'class' => 'provider-action-button'));
        $this->addElement('Button', 'clear_mr', array('label' => $translate->_('INVITER_Clear Credentials'), 'class' => 'provider-action-button provider-action-button-right'));
        $this->getElement('save_mr')->setDecorators(array('ViewHelper'));
        $this->getElement('clear_mr')->setDecorators(array('ViewHelper'));

        $this->addDisplayGroup(
            array('inviter_mailru_id', 'inviter_mailru_private_key', 'inviter_mailru_secret_key', 'save_mr', 'clear_mr', 'mailru'),
            'mailru_settings',
            array('class' => 'he_setting_fieldset')
        );

        $this->getDisplayGroup('mailru_settings')->addDecorator(
            'ProviderDescription',
            array('label' => $translate->_('Mail.ru settings'), 'description' => $translate->_('INVITER_MAILRU_APP_DESC')));

        // Add submit button
//        $this->addElement('Button', 'submit', array(
//            'label' => 'Save Changes',
//            'type' => 'submit',
//            'ignore' => true
//        ));
    }
}