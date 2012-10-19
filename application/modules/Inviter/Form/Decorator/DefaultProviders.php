<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Inviter
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: DefaultProviders.php 2010-07-02 19:54 mirlan $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Inviter
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Engine_Form_Decorator_DefaultProviders extends Zend_Form_Decorator_Abstract
{
  protected $_placement = null;
  
  public function render($content)
  {
    $td_width = (isset($this->_options['widget']) && $this->_options['widget'])?'200px':'300px';
    $igm_width = (isset($this->_options['widget']) && $this->_options['widget'])?'80px':'85px';
    $style = (isset($this->_options['widget']) && $this->_options['widget'])?"style='height: 45px'":'';

    $html = "<table cellpadding='0' cellspacing='0' style='margin-top: 5px;' ><tr>";

    $providers_html = "<div style='padding: 10px; padding-top: 0px;' id='default_providers'>";

    $providers = Engine_Api::_()->inviter()->getProviders2(false, $this->_options['default_providers']);

    foreach ($providers as $provider)
    {
      $providers_html .= "<div  onclick='provider.select_provider($(this), true);' class='default_provider' "
              . $style
              . ">"
              . "<div class='provider_name' style='display:none'>"
              . $provider->provider_title
              . "</div><span style='display:none' class='provider_logo'>"
              . "<img src='application/modules/Inviter/externals/images/providers/"
              . $provider->provider_logo
              . "'/></span>"
              ."<a href='javascript://' onclick='this.blur();' style='float: left'>"
              . "<img src='application/modules/Inviter/externals/images/providers_big/"
              . $provider->provider_logo
              . "' width='"
              . $igm_width
              . "'/></a></div>";
    }

    $providers_html .= "</div>";

    $html .= '<td valign="top">'.$providers_html.'</td></tr>'."<tr><td style='padding-top: 20px;' width='100%'>".$content."</td></tr>".'</table>';

    return $html;
  }
}