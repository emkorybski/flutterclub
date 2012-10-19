<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Hecore
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: IndexController.php 2010-07-02 19:52 idris $
 * @author     Idris
 */

/**
 * @category   Application_Extensions
 * @package    Hecore
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Hecore_IndexController extends Core_Controller_Action_Standard
{
  public function init()
  {
    $this->view->params = $params = (array)$this->_getParam('params');
    $this->view->callback = $callback = $this->_getParam('c');
    $this->view->title = $title = $this->_getParam('t', '');
    $this->view->module = $module = $this->_getParam('m');
    $this->view->list = $list = $this->_getParam('l');
    $this->view->not_logged_in = $not_logged_in = $this->_getParam('nli', 0);
    $this->view->p = $p = (int)$this->_getParam('p', 1);
    $this->view->contacts = $contacts = (array)$this->_getParam('contacts', array());
    $this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();
    $this->view->disabled_label = !empty($this->view->params['disabled_label']) ? $this->view->params['disabled_label'] : "";
    $this->view->ipp = $ipp = (isset($params['ipp'])) ? $params['ipp'] : (int)$this->_getParam('ipp', 30);
    
    // Params
    $keyword = $this->_getParam('keyword');
    $list_type = $this->_getParam('list_type');

    if ($keyword) {
      $params['keyword'] = $keyword;
    }

    if ($list_type) {
      $params['list_type'] = $list_type;
    }

    $this->view->list_type = (isset($params['list_type'])) ? $params['list_type'] : 'all';

    // User logged in or not
    if (!$not_logged_in && !$viewer->getIdentity()) {
      $this->view->error = 1;
      $this->view->message = $this->view->translate("hecore_You should be logged in to view this page.");
      return ;
    }

    $this->view->module = $module = trim(strtolower($module));

    // Sanity checks
    $table = Engine_Api::_()->getDbTable('modules', 'core');
    $select = $table->select();
    $select
      ->where("name = ?", $module);

    if (!$table->getAdapter()->fetchOne($select)) {
      $this->view->error = 2;
      $this->view->message = "Module does not exists.";
      return ;
    }

    $api = Engine_Api::_()->getApi('core', $module);
    if (!is_callable(array($api, $list))) {
      $this->view->error = 3;
      $this->view->message = "Method does not exists in module's API.";
      return ;
    }

    $api = Engine_Api::_()->$module();
    if (!method_exists($api, $list)) {
      $this->view->error = 5;
      $this->view->message = "Method '$list' does not exists.";
      return ;
    }
    
    // Get Items and check return result
    $this->view->items = $items = Engine_Api::_()->$module()->$list($params);
    if ($items === null) {
      $this->view->error = 4;
      $this->view->message = "Method returned null.";
      return ;
    }

    if ($items instanceof Zend_Paginator) {
      $this->view->total = $items->getTotalItemCount();
      $this->view->current_count = $items->getCurrentItemCount();
    }

    $listDisabled = $list . 'Disabled';
    if (method_exists($api, $listDisabled)) {
      $disabledItems = Engine_Api::_()->$module()->$listDisabled($params);
      $this->view->disabledItems = $disabledItems;
    } else {
      $this->view->disabledItems = array();
    }

    $listChecked = $list . 'Checked';
    if (method_exists($api, $listChecked)) {
      $checkedItems = Engine_Api::_()->$module()->$listChecked($params);
      $this->view->checkedItems = (array)$checkedItems;
    } else {
      $this->view->checkedItems = array();
    }

    $this->view->checkedItems = array_merge($this->view->checkedItems, $contacts);

    $listPotential = $list . 'Potential';
    if (method_exists($api, $listPotential) && isset($params['potential']) && $params['potential']) {
      $potentialItems = Engine_Api::_()->$module()->$listPotential($params);
      $this->view->potentialItems = $potentialItems;
    } else {
      $this->view->potentialItems = array();
    }

  }

  public function contactsAction()
  {
    if (isset($this->view->items) && ($this->view->items instanceof Zend_Paginator)) {
      $this->view->items->setItemCountPerPage($this->view->ipp);
    }

    if (isset($this->view->p) && ($this->view->items instanceof Zend_Paginator)) {
      $this->view->items->setCurrentPageNumber($this->view->p);
    }

    $this->view->need_pagination = (bool)($this->view->p < count($this->view->items));

    if ( isset($this->view->params['scriptpath']) ) {
      if (null !== ($scriptpath = $this->view->params['scriptpath'])){
        $this->view->setScriptPath($scriptpath);
      }
    }

    if ($this->_getParam('format') == 'json') {
      $this->view->html = $this->view->render('_contacts_items.tpl');
    }

    $this->_helper->layout->disableLayout();
  }
  
  public function listAction()
  {
    $this->view->p = $p = $this->_getParam('p', 1);
    
    if (isset($this->view->items)) {
      $this->view->items->setItemCountPerPage(9);
      $this->view->items->setCurrentPageNumber($p);
    }

    if ($this->_getParam('format') == 'json') {
      $this->view->html = $this->view->render('_hecore_items.tpl');
    }

    $this->_helper->layout->disableLayout();
  }
}