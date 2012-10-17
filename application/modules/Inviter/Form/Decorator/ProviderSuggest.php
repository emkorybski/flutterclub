<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Inviter
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: ProviderSuggest.php 2010-07-02 19:54 mirlan $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Inviter
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Engine_Form_Decorator_ProviderSuggest extends Zend_Form_Decorator_Abstract
{
  protected $_placement = null;
  
  public function render($content)
  {
      $providers = Engine_Api::_()->inviter()->getProviders2(false, 10);
    $path = "application/modules/Inviter/externals/images/providers/";

    $elementName = $this->getElement()->getName();

    $html = $content.
    "<div class='show_providers'  id='".$elementName."-show_providers'>  
      <div class='show_provider_btn' id='".$elementName."-toggle_providers' onclick='provider.show_providers($(this));'></div>
      <div class='selected_provider' id='".$elementName."-selected_provider'></div>
    </div>
    <div class='provider_select' id='provider_select' >
      <div class='provider_list providers_hidden' id='provider_list'>";
        $html2='';
           foreach ($providers as $provider)
           {
             $html2.="
             <div class='provider' onclick='provider.select_provider($(this), false);'>
               <div class='provider_logo'>
                 <img src='".$path.$provider->provider_logo."'/>
               </div><div class='provider_name'>
                   ".$provider->provider_title."
                 </div>
                 <div style='clear:both;'></div>
               </div>";
           }
           $html .=$html2."
        </div>
    </div>
    ";
    
    $providers = Engine_Api::_()->inviter()->getProviderArray(true);
    $html .="<script type='text/javascript'> \$providers=".Zend_Json::encode($providers)."; </script>";
   
    return '<div style="margin-top: 8px">'.$html.'</div>';
  }
}