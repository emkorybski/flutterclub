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
 
class Radcodes_AdminToolkitsController extends Core_Controller_Action_Admin
{
  public function init()
  {
    $this->view->navigation = $this->getNavigation();
  }
  
  public function indexAction()
  {

  }

  public function queryAction()
  {
    $this->view->form = $form = new Engine_Form();
    $form->setTitle('Query Execute')
         ->addElement('Textarea', 'query', array(
            'label' => 'Query',
            'allowEmpty' => false,
            'required' => true,
            'validators' => array(
              array('NotEmpty', true),
            ),
          ))
         ->addElement('Button', 'submit', array(
            'label' => 'Execute',
            'type' => 'submit',
            'ignore' => true,
            'decorators' => array('ViewHelper')
          ))
         ;
         
    if( $this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost()))
    {
      $db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();
      
      try 
      {
  	    $db->query($form->query->getValue());
        $db->commit();
      }
      catch( Exception $e )
      {
        $db->rollBack();
        throw $e;
      }
      
      $savedChangesNotice = Zend_Registry::get('Zend_Translate')->_("Query executed.");
      $form->addNotice($savedChangesNotice);
    }  
         
  }
  
  public function deletePageAction()
  {
    
    $this->view->form = $form = new Engine_Form();
    $form->setTitle('Delete Content Page')
         ->addElement('Text', 'name', array(
            'label' => 'Page Name | ID',
            'allowEmpty' => false,
            'required' => true,
            'validators' => array(
              array('NotEmpty', true),
            ),
          ))
         ->addElement('Button', 'submit', array(
            'label' => 'Delete',
            'type' => 'submit',
            'ignore' => true,
            'decorators' => array('ViewHelper')
          ))
         ;

    if( $this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost()))
    {
      $values = $form->getValues();
      $page = $values['name'];
      $pageTable = Engine_Api::_()->getDbtable('pages', 'core');
      
      
      
      
      //$pageObject = $pageTable->fetchRow($pageTable->select()->where('name = ?', $page)->orWhere('page_id = ?', $page));
  
      $pageObjects = $pageTable->fetchAll($pageTable->select()->where('name LIKE ?', "$page%")->orWhere('page_id = ?', $page));
      
      if (count($pageObjects))
      {
        $db = Engine_Db_Table::getDefaultAdapter();
        $db->beginTransaction();
        
        $names = array();
        try 
        {
          foreach ($pageObjects as $pageObject)
          {
            $names[$pageObject->page_id] = $pageObject->name;
            $pageTable->deletePage($pageObject);
          }

          $db->commit();
        }
        catch( Exception $e )
        {
          $db->rollBack();
          throw $e;
        }
        
        $savedChangesNotice = Zend_Registry::get('Zend_Translate')->_("Page deleted: " . join(", ", $names));
        $form->addNotice($savedChangesNotice);
      }

    }
    
    //$this->_helper->redirector->gotoRoute(array('action' => 'index'));
  }
  
  
  public function updatePageAction()
  {
    $page_id = $this->getRequest()->getParam('page_id');
    if (!$page_id)
    {
      die("page name=$page_id missing");
    }
    
    $pageTable = Engine_Api::_()->getDbtable('pages', 'core');
    $pageObject = $pageTable->fetchRow($pageTable->select()->where('page_id = ?', $page_id));
      
    if (!$pageObject)
    {
      die("page id=$page_id not found");
    }
    
    $db = Engine_Db_Table::getDefaultAdapter();
    $db->beginTransaction();
    
    try 
    {
      if ($pageObject)
      {
        $pageObject->name = $this->getRequest()->getParam('page_name');
        $pageObject->custom = 0;
        $pageObject->save();
      }
      $db->commit();
    }
    catch( Exception $e )
    {
      $db->rollBack();
      throw $e;
    }
    
    $this->_helper->redirector->gotoRoute(array('action' => 'index'));
  }
  
  public function runInstallerFunctionAction()
  {
    $this->view->form = $form = new Engine_Form();
    $form->setTitle('Execute Installer Function')
         ->addElement('Text', 'module_name', array(
            'label' => 'Module Name',
            'description' => 'Example: business (lowercase)',
            'allowEmpty' => false,
            'required' => true,
            'validators' => array(
              array('NotEmpty', true),
            ),
          ))
         ->addElement('Text', 'method_name', array(
            'label' => 'Function Name',
            'description' => 'Example: addHomePage, addProfilePage, or addUserProfileTab',
            'allowEmpty' => false,
            'required' => true,
            'validators' => array(
              array('NotEmpty', true),
            ),
          ))
         ->addElement('Button', 'submit', array(
            'label' => 'Execute',
            'type' => 'submit',
            'ignore' => true,
            'decorators' => array('ViewHelper')
          ))
         ;

    if( $this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost()))
    {
      $values = $form->getValues();
      
      $module_name = $values['module_name'];
  	  $method_name = $values['method_name'];
  	

      $path = $this->getFrontController()->getModuleDirectory($module_name);
      $contentManifestFile = $path . '/settings/manifest.php';
      
      if( !file_exists($contentManifestFile) ) {
        throw new Engine_Exception("Could not found file = $contentManifestFile");
      }
      
      $ret = include $contentManifestFile;    
      
      $manager = new Engine_Package_Manager();
      $targetPackage = new Engine_Package_Manifest();
      $operation = new Engine_Package_Manager_Operation_Install($manager, $targetPackage);
      
      if (!isset($ret['package']['callback']))
      {
        throw new Engine_Exception("Could not $contentManifestFile package::callback");
      }
      $callback = $ret['package']['callback'];
      
      include_once $callback['path'];
      $db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();
      try
      {
      	
        $installer = new $callback['class']($operation);
        $installer->setDb($db);
        $installer->$method_name();
        
        $db->commit();
      }
      catch( Exception $e )
      {
        $db->rollBack();
        throw $e;
      }
      
      $savedChangesNotice = Zend_Registry::get('Zend_Translate')->_("Installer function executed.");
      $form->addNotice($savedChangesNotice);

    }

  }
  
  protected function getNavigation()
  {
    $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('radcodes_admin_main', array());
      
    $navigation->addPage(array(
          'label' => Zend_Registry::get('Zend_Translate')->_('Tool Kits'),
          'module' => 'radcodes',
          'controller' => 'toolkits',
          'action' => 'index',
          'active' => true,
        ));
    return $navigation;
  }
  
}