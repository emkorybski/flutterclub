<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Hecore
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Controller.php 2010-08-31 16:05 michael $
 * @author     Michael
 */

/**
 * @category   Application_Extensions
 * @package    Hecore
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Hecore_Widget_FeaturedCarouselController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
    if (!Engine_Api::_()->getDbTable('modules', 'core')->isModuleEnabled('hecore')) {
      return $this->setNoRender();
    }

    $paginator = $this->view->paginator = Engine_Api::_()->getDbTable('featureds', 'hecore')->getFeatureds(null, true);
    $paginator->setItemCountPerPage(999);

    $this->view->total = $total = $paginator->getTotalItemCount();

    if (!$total || !count($paginator)) {
      return $this->setNoRender();
    }
  }
}