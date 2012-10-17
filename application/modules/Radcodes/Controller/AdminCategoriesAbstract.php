<?php

/**
 * Radcodes - SocialEngine Module
 *
 * @category   Application_Extensions
 * @package    Radcodes
 * @copyright  Copyright (c) 2009-2010 Radcodes LLC (http://www.radcodes.com)
 * @license    http://www.radcodes.com/license/
 * @version    $Id$
 * @author     Vincent Van <vincent@radcodes.com>
 */
 
class Radcodes_Controller_AdminCategoriesAbstract extends Core_Controller_Action_Admin
{

  protected $_enableProfileType = false;
  
  /**
   * Type of category, ex: module_category, article_category
   * @var $_itemType string
   */
  protected $_itemType;
  
  /**
   * Module name in proper case, ex: Module, Article
   * @var $_moduleName string
   */
  protected $_moduleName;
  
  /**
   * Module name in lower case, ex: module, article
   * @var $_moduleSpec string
   */
  protected $_moduleSpec;
  
  
  protected $_formSpec = array(
    'create' => 'Radcodes_Form_Admin_Category_Create',
    'edit' => 'Radcodes_Form_Admin_Category_Edit',
    'delete' => 'Radcodes_Form_Admin_Category_Delete',
    'photo' => 'Radcodes_Form_Admin_Category_Photo',
    'move' => 'Radcodes_Form_Admin_Category_Move',
    'profiletype' => 'Radcodes_Form_Admin_Category_ProfileType',  
  );
  
  public function init()
  {

    $this->setup();

    if( !Engine_Api::_()->core()->hasSubject() )
    {
      if( 0 !== ($category_id = (int) $this->_getParam('category_id')) &&
          null !== ($category = $this->getCategoryTable()->getCategory($category_id)) )
      {
        Engine_Api::_()->core()->setSubject($category);
      }
    }

    $this->_helper->requireSubject->setActionRequireTypes(array(
      'delete' => $this->_itemType,
      'edit' => $this->_itemType,
      'icon' => $this->_itemType,
    ));    
    
    //die(dirname(dirname(__FILE__)) . '/views/scripts');
    //die(get_class($this->view));
    // Hack up the view paths
    $this->view->addHelperPath(dirname(dirname(__FILE__)) . '/views/helpers', 'Radcodes_View_Helper');
    $this->view->addScriptPath(dirname(dirname(__FILE__)) . '/views/scripts');

    $this->view->addHelperPath(dirname(dirname(dirname(__FILE__))) . DS . $this->_moduleName . '/views/helpers', $this->_moduleName . '_View_Helper');
    $this->view->addScriptPath(dirname(dirname(dirname(__FILE__))) . DS . $this->_moduleName . '/views/scripts');
      
    $this->view->moduleName = $this->_moduleName;
    $this->view->moduleSpec = $this->_moduleSpec;
    $this->view->itemType = $this->_itemType;        
    
    $this->view->enableProfileType = $this->_enableProfileType;
    
    $this->_loadNavigation();
  }
  
  protected function setup()
  {
    // Parse module name from class
    if( !$this->_moduleName ) {
      $this->_moduleName = substr(get_class($this), 0, strpos(get_class($this), '_'));
    }

    if( !$this->_moduleSpec) {
      //$this->_moduleSpec = strtolower($this->_moduleName);
      $this->_moduleSpec = Engine_Api::deflect($this->_moduleName);
    }
    
    // Try to set item type
    if( !$this->_itemType ) {
      $this->_itemType = $this->_moduleSpec . '_category';
    }

    //die("itemType=$this->_itemType moduleName=$this->_moduleName _moduleSpec=$this->_moduleSpec");
    
    if( !$this->_itemType || !$this->_moduleName || !Engine_APi::_()->hasItemType($this->_itemType) ) {
      throw new Core_Model_Exception('Invalid _itemType or _moduleName');
    }
  
    $this->_enableProfileType = $this->getCategoryTable()->supportProfileType();
  }
  
  /**
   * @return Radcodes_Model_DbTable_Categories
   */
  public function getCategoryTable()
  {
    return Engine_Api::_()->getItemTable($this->_itemType);
  }
  
  
  public function indexAction()
  {
    $this->view->categories = $this->getCategoryTable()->getTopLevelCategories();
  }
  
  public function profileTypeAction()
  {
    $this->view->category = $category = Engine_Api::_()->core()->getSubject($this->_itemType);
    $this->view->form = $form = $this->getForm('profiletype');

    $form->populate($category->toArray());
    
    if (!$this->getRequest()->isPost())
    {
      return;
    }
    
    if (!$form->isValid($this->getRequest()->getPost()))
    {
      return;
    }
    
    $values = $form->getValues();
    
    $db = Engine_Db_Table::getDefaultAdapter();
    $db->beginTransaction();
    
    try
    {
      if (!empty($values['remove_photo'])) {
        $category->removePhoto();
      }
      
      $category->setFromArray($values);
      $category->save();
      
      // Set photo
      if( !empty($values['photo']) ) {
        $category->removePhoto();
        $category->setPhoto($form->photo);
      }
      
      $db->commit();
    }
    
    catch( Exception $e )
    {
      $db->rollBack();
      throw $e;
    }
    
    $this->_forward('success', 'utility', 'core', array(
      'smoothboxClose' => 10,
      'parentRefresh'=> 10,
      'messages' => array(Zend_Registry::get('Zend_Translate')->_('Category updated.'))
    ));     
    
  }
  
  public function iconAction()
  {
    $this->view->category = $category = Engine_Api::_()->core()->getSubject($this->_itemType);
    $this->view->form = $form = $this->getForm('photo');
  }
  
  public function deletePhotoAction()
  {
    $this->view->category = $category = Engine_Api::_()->core()->getSubject($this->_itemType);
    $this->view->form = $form = $this->getForm('photo');
    
    if (!$this->getRequest()->isPost())
    {
      return;
    }
    
    if (!$form->isValid($this->getRequest()->getPost()))
    {
      return;
    }
    
    $db = Engine_Db_Table::getDefaultAdapter();
    $db->beginTransaction();
    
    try
    {
      $category->removePhoto();
      $category->save();
      $db->commit();
    }
    
    catch( Exception $e )
    {
      $db->rollBack();
      throw $e;
    }
    
    $this->_forward('success', 'utility', 'core', array(
      'smoothboxClose' => 10,
      'parentRefresh'=> 10,
      'messages' => array(Zend_Registry::get('Zend_Translate')->_('Category photo deleted.'))
    )); 
  }
  
  public function addAction()
  {
    $this->view->form = $form = $this->getForm('create');
    
    $categories = $this->getCategoryTable()->getTopLevelCategoriesAssoc();
    $categories = array("0" => "__ ROOT __") + $categories;
    $form->getElement('parent_id')->setMultiOptions($categories);
    
    if (!$this->getRequest()->isPost())
    {
      return;
    }
    
    if (!$form->isValid($this->getRequest()->getPost()))
    {
      return;
    }
    
    // we will add the category
    $values = $form->getValues();
    
    $db = Engine_Db_Table::getDefaultAdapter();
    $db->beginTransaction();
    
    try
    {
      $values['user_id'] = 0;
      
      $table = $this->getCategoryTable();
      $category = $table->createRow();
      $category->setFromArray($values);
      $category->save();
      
      // Set photo
      if( !empty($values['photo']) ) {
        $category->setPhoto($form->photo);
      }
      
      $db->commit();
    }
    
    catch( Exception $e )
    {
      $db->rollBack();
      throw $e;
    }
    
    $this->_forward('success', 'utility', 'core', array(
      'smoothboxClose' => 10,
      'parentRefresh'=> 10,
      'messages' => array(Zend_Registry::get('Zend_Translate')->_('Category added.'))
    )); 
  }
  
  
  public function editAction()
  {
    $this->view->category = $category = Engine_Api::_()->core()->getSubject($this->_itemType);
    $this->view->form = $form = $this->getForm('edit');
    
    if ($category->hasChildrenCategory())
    {
      $form->removeElement('parent_id');
    }
    else 
    {
      $categories = $this->getCategoryTable()->getTopLevelCategoriesAssoc();
      $categories = array("" => "__ ROOT __") + $categories;
      foreach($categories as $catid => $catname) {
        if ($catid == $category->getIdentity()) {
          unset($categories[$catid]);
        }
      }
      $form->getElement('parent_id')->setMultiOptions($categories);
    }
    
    $form->populate($category->toArray());
    
    
    if (!$this->getRequest()->isPost())
    {
      return;
    }
    
    if (!$form->isValid($this->getRequest()->getPost()))
    {
      return;
    }
    
    $values = $form->getValues();
    
    $db = Engine_Db_Table::getDefaultAdapter();
    $db->beginTransaction();
    
    try
    {
      if (!empty($values['remove_photo'])) {
        $category->removePhoto();
      }
      
      $category->setFromArray($values);
      $category->save();
      
      // Set photo
      if( !empty($values['photo']) ) {
        $category->removePhoto();
        $category->setPhoto($form->photo);
      }
      
      $db->commit();
    }
    
    catch( Exception $e )
    {
      $db->rollBack();
      throw $e;
    }
    
    $this->_forward('success', 'utility', 'core', array(
      'smoothboxClose' => 10,
      'parentRefresh'=> 10,
      'messages' => array(Zend_Registry::get('Zend_Translate')->_('Category updated.'))
    ));    
    
  }
  
  
  public function deleteAction()
  {
    $this->view->category = $category = Engine_Api::_()->core()->getSubject($this->_itemType);
    $this->view->form = $form = $this->getForm('delete');
    /*
    $multiOptions = $this->getCategoryTable()->getMultiOptionsAssoc();
    $multiOptions = array('0' => 'Please select a category') + $multiOptions;
    $form->getElement('category_id')->setMultiOptions($multiOptions);
    */
    
    $form->populate($category->toArray());
    
    if (!$this->getRequest()->isPost())
    {
      return;
    }
    
    if (!$form->isValid($this->getRequest()->getPost()))
    {
      return;
    }    
    
    
    $db = Engine_Db_Table::getDefaultAdapter();
    $db->beginTransaction();

    try
    {
      $category->delete();        
      $db->commit();
      Engine_Api::_()->core()->clearSubject();
    }

    catch( Exception $e )
    {
      $db->rollBack();
      throw $e;
    }
    
    $this->_forward('success', 'utility', 'core', array(
        'smoothboxClose' => 10,
        'parentRefresh'=> 10,
        'messages' => array(Zend_Registry::get('Zend_Translate')->_('Category deleted.'))
    ));
    
  }  
  
  public function moveAction()
  {

    $this->view->form = $form = $this->getForm('move');
        
    if (!$this->getRequest()->isPost())
    {
      return;
    }
    
    if (!$form->isValid($this->getRequest()->getPost()))
    {
      return;
    }     
    
    $values = $form->getValues();
    
    $db = Engine_Db_Table::getDefaultAdapter();
    $db->beginTransaction();

    try
    {
      $from_category_id = $values['from_category_id'];
      $to_category_id = $values['to_category_id'];
      
      if ($from_category_id != $to_category_id)
      {
        $entriesTable = Engine_Api::_()->getItemTable($this->_moduleSpec);
        $where = $entriesTable->getAdapter()->quoteInto('category_id = ?', $from_category_id);
        $data = array('category_id' => $to_category_id);
        $entriesTable->update($data, $where);
        
        $db->commit();
      }

    }

    catch( Exception $e )
    {
      $db->rollBack();
      throw $e;
    }
    $this->_forward('success', 'utility', 'core', array(
        'smoothboxClose' => 10,
        'parentRefresh'=> 10,
        'messages' => array(Zend_Registry::get('Zend_Translate')->_('Category entries moved.'))
    ));

  }
  
  public function orderAction()
  {
    if (!$this->getRequest()->isPost()) {
      return;
    }
    $db = Engine_Db_Table::getDefaultAdapter();
    $db->beginTransaction();

    try
    {
      $parent_id = $this->getRequest()->getParam('parent_id', 0);
      $categories = $this->getCategoryTable()->getChildrenOfParent($parent_id);
      foreach ($categories as $category)
      {
        $category->order = $this->getRequest()->getParam('admin_category_item_'.$category->category_id);
        $category->save();
        //echo "\n".$category->category_id.'='.$category->order;
      }
      
      $db->commit();
    }
    catch( Exception $e )
    {
      $db->rollBack();
      throw $e;
    }
    
    return;
  }  
  
  protected function _loadNavigation()
  {
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation($this->_moduleSpec . '_admin_main', array(), $this->_moduleSpec . '_admin_main_categories');    
  }
  
  /**
   * @return Engine_Form
   */
  protected function getForm($spec)
  {
    $class = $this->_formSpec[$spec];
    $form = new $class(array(
    	'categoryTable'=>$this->getCategoryTable(),
      'enableProfileType' => $this->_enableProfileType,
      'moduleApi' => $this->getModuleApi(),
    ));
    return $form;
  }
  
  /**
   * @return Core_Api_Abstract
   */
  protected function getModuleApi()
  {
    return Engine_Api::_()->getApi('core', $this->_moduleSpec);
  }
  
}

