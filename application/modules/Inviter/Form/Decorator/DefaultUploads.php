<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Inviter
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: DefaultUploads.php 2010-07-02 19:54 mirlan $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Inviter
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Engine_Form_Decorator_DefaultUploads extends Zend_Form_Decorator_Abstract
{
  protected $_placement = null;

  public function render($content)
  {
    $translate = Zend_Registry::get('Zend_Translate');

    $td_width = (isset($this->_options['widget']) && $this->_options['widget'])?'200px':'300px';
    $html = "
      <div class='creation_descriptionr'>
        <div class='show_creation_description'>
          <a href=\"javascript:show_creation_description('creation_description_conteiner')\">
            ".$translate->_('INVITER_How to create a contact file...')."
          </a>
        </div>

        <div class='creation_description_conteiner creation_item_hide' id='creation_description_conteiner'>

          <div class='creation_item'>
            <div class='creation_item_title'>
              <a href=\"javascript:show_creation_description('outlook_description')\">
                ".$translate->_('INVITER_Outlook')."
              </a>
            </div>
            <div class='creation_item_description' id='outlook_description'>
              ".$translate->_('INVITER_OUTLOOK_UPLOAD_CONTACTS_DESCRIPTION')."
            </div>
          </div>

          <div class='creation_item'>
            <div class='creation_item_title'>
              <a href=\"javascript:show_creation_description('outlookexpress_description')\">
                ".$translate->_('INVITER_Outlook Express')."
              </a>
            </div>
            <div class='creation_item_description creation_item_hide' id='outlookexpress_description'>
              ".$translate->_('INVITER_OUTLOOK_EXPRESS_UPLOAD_CONTACTS_DESCRIPTION')."
            </div>
          </div>

          <div class='creation_item'>
            <div class='creation_item_title'>
              <a href=\"javascript:show_creation_description('thunderbird_description')\">
                ".$translate->_('INVITER_Mozilla Thunderbird')."
              </a>
            </div>
            <div class='creation_item_description creation_item_hide' id='thunderbird_description'>
              ".$translate->_('INVITER_MOZILLA_THUNDERBIRD_UPLOAD_CONTACTS_DESCRIPTION')."
            </div>
          </div>

          <div class='creation_item'>
            <div class='creation_item_title'>
              <a href=\"javascript:show_creation_description('windowsmail_description')\">
                ".$translate->_('INVITER_Windows Mail')."
              </a>
            </div>
            <div class='creation_item_description creation_item_hide' id='windowsmail_description'>
              ".$translate->_('INVITER_WINDOWS_MAIL_UPLOAD_CONTACTS_DESCRIPTION')."
            </div>
          </div>

          <div class='creation_item'>
            <div class='creation_item_title'>
              <a href=\"javascript:show_creation_description('other_description')\">
                ".$translate->_('INVITER_Other')."
              </a>
            </div>
            <div class='creation_item_description creation_item_hide' id='other_description'>
              ".$translate->_('INVITER_OTHER_UPLOAD_CONTACTS_DESCRIPTION')."
            </div>
          </div>
          
        </div>
      </div>
      
      <table cellpadding='0' cellspacing='0' style='margin-top: 20px;' ><tr><td valign='top' width='".$td_width."'>".$content."</td>";

    $providers_html = "<div style='padding: 10px; padding-top: 0px;' id='default_providers'>";

    $providers_html .= "<img src='application/modules/Inviter/externals/images/uploads/outlook.png' width='85px'/>&nbsp;";
    $providers_html .= "<img src='application/modules/Inviter/externals/images/uploads/outlook_express.png' width='85px'/>&nbsp;";
    $providers_html .= "<img src='application/modules/Inviter/externals/images/uploads/mozilla_thunderbird.png' width='85px'/>&nbsp;";
    $providers_html .= "<img src='application/modules/Inviter/externals/images/uploads/windows_adress_book.png' width='85px'/>&nbsp;";

    $providers_html .= "</div>";

    $html .= '<td valign="top">'.$providers_html.'</td></tr></table>';

    return $html;
  }
}