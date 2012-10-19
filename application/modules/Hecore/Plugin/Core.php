<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Hecore
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Core.php 2010-08-31 16:05 ermek $
 * @author     Ermek
 */

/**
 * @category   Application_Extensions
 * @package    Hecore
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Hecore_Plugin_Core
{
  public function onRenderLayoutDefault($event)
  {
    // Arg should be an instance of Zend_View
    $view = $event->getPayload();

    if (!($view instanceof Zend_View)) {
      return ;
    }

    $theme_name = $view->activeTheme();
    $script = <<<JS
    en4.core.runonce.add(function() {
      $$('body').addClass('layout_active_theme_{$theme_name}');
    });
JS;

    $view->headScript()
      ->appendFile($view->hecoreBaseUrl()
        . 'application/modules/Hecore/externals/scripts/core.js')
      ->appendFile($view->hecoreBaseUrl()
        . 'application/modules/Hecore/externals/scripts/imagezoom/core.js')
      ->appendScript($script);

    $view->headLink()
      ->appendStylesheet($view->hecoreBaseUrl()
        . 'application/css.php?request=application/modules/Hecore/externals/styles/imagezoom/core.css');

    $view->headTranslate(array('Confirm', 'Cancel', 'or', 'close'));
  }

  public function onRenderLayoutAdmin($event)
  {
    $this->onRenderLayoutDefault($event);
  }

  public function onRenderLayoutAdminSimple($event)
  {
    $this->onRenderLayoutDefault($event);
  }

  public function onRenderLayoutDefaultSimple($event)
  {
    $this->onRenderLayoutDefault($event);
  }
}