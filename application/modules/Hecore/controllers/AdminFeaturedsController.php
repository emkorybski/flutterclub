<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Hecore
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: AdminFeaturedsController.php 2010-08-31 16:05 michael $
 * @author     Michael
 */

/**
 * @category   Application_Extensions
 * @package    Hecore
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Hecore_AdminFeaturedsController extends Core_Controller_Action_Admin
{
    public function init()
    {
        $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('hecore_admin_main', array(), 'hecore_admin_main_featureds');
    }

    public function indexAction()
    {
        $this->view->formFilter = $formFilter = new Hecore_Form_Admin_Featureds_Filter();

        $user_tbl = Engine_Api::_()->getDbTable('users', 'user');
        $featured_tbl = Engine_Api::_()->getDbTable('featureds', 'hecore');

        $values = array();
        if ($formFilter->isValid($this->_getAllParams())) {
            $values = $formFilter->getValues();
        }

        $values = array_merge(array('order' => 'user_id', 'order_direction' => 'DESC'), $values);
        $this->view->assign($values);
        $this->view->filters = $values;

        $select = $user_tbl->select();
        $select
            ->setIntegrityCheck(false)
            ->from(array('u' => $user_tbl->info('name')), 'u.*')
            ->joinLeft(array('f' => $featured_tbl->info('name')), 'f.user_id = u.user_id', 'f.featured_id');

        $order = (!empty($values['order']) ? $values['order'] : 'user_id')
            . ' ' . (!empty($values['order_direction']) ? $values['order_direction'] : 'DESC');

        $select->order($order);

        if (!empty($values['username'])) {
            $select->where('username LIKE ?', '%' . $values['username'] . '%');
        }
        if (!empty($values['email'])) {
            $select->where('email LIKE ?', '%' . $values['email'] . '%');
        }
        if (!empty($values['level_id'])) {
            $select->where('level_id = ?', $values['level_id']);
        }

        if ($values['featured'] == 1) {
            $select->where('ISNULL(featured_id)');
        } else if ($values['featured'] == 2) {
            $select->where('NOT ISNULL(featured_id)');
        }

        $this->view->hideEmails = _ENGINE_ADMIN_NEUTER;

        $this->view->paginator = $paginator = Zend_Paginator::factory($select);
        $this->view->paginator = $paginator->setCurrentPageNumber($this->_getParam('page', 1));
    }

    public function multiModifyAction()
    {
        if (defined('_ENGINE_ADMIN_NEUTER') && _ENGINE_ADMIN_NEUTER) {
            return $this->_helper->redirector->gotoRoute(array('module' => 'hecore', 'controller' => 'featureds', 'action' => 'index'), 'admin_default', true);
        }

        $featured_tbl = Engine_Api::_()->getDbTable('featureds', 'hecore');

        if ($this->getRequest()->isPost()) {
            $values = $this->getRequest()->getPost();
            $is_featured = ($values['submit_button'] == 'set_featured');

            $user_ids = array();
            foreach ($values as $key => $value) {
                if ($key == 'modify_' . $value) {
                    $user_ids[] = $value;
                }
            }

            $featured_tbl->multiSetFeatured($user_ids, $is_featured);
        }

        return $this->_helper->redirector->gotoRoute(
            array(
                'module' => 'hecore',
                'controller' => 'featureds',
                'action' => 'index'
            ),
            null, true);
    }

    public function modifyAction()
    {
        if (defined('_ENGINE_ADMIN_NEUTER') && _ENGINE_ADMIN_NEUTER) {
            return $this->_helper->redirector->gotoRoute(array('module' => 'hecore', 'controller' => 'featureds', 'action' => 'index'), 'admin_default', true);
        }

        $featured_tbl = Engine_Api::_()->getDbTable('featureds', 'hecore');
        $featured_tbl->setFeatured($this->_getParam('user_id'), (bool)$this->_getParam('set_featured'));

        return $this->_helper->redirector->gotoRoute(
            array(
                'module' => 'hecore',
                'controller' => 'featureds',
                'action' => 'index'
            ),
            null, true);
    }
}